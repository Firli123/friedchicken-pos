<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function ($q) {
            $q->active()->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        $settings = Setting::getAllAsArray();

        return view('pos.index', compact('categories', 'settings'));
    }

    /**
     * Get products for POS (AJAX).
     */
    public function getProducts(Request $request)
    {
        $query = Product::with('category')->active()->orderBy('sort_order');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        return response()->json($query->get()->map(fn ($p) => [
            'id'         => $p->id,
            'name'       => $p->name,
            'code'       => $p->code,
            'price'      => $p->price,
            'price_fmt'  => $p->formatted_price,
            'category'   => $p->category->name,
            'image_url'  => $p->image_url,
            'is_active'  => $p->is_active,
        ]));
    }

    /**
     * Process payment and create transaction.
     */
    public function processPayment(Request $request)
    {
        $data = $request->validate([
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'exists:products,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cash,qris'],
            'amount_paid'    => ['required_if:payment_method,cash', 'nullable', 'integer', 'min:0'],
            'discount'       => ['integer', 'min:0'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            // Build items with current prices
            $items    = collect($data['items']);
            $products = Product::whereIn('id', $items->pluck('id'))->get()->keyBy('id');

            $subtotal = 0;
            $lineItems = [];

            foreach ($items as $item) {
                $product  = $products[$item['id']];
                $lineTotal = $product->price * $item['qty'];
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price,
                    'quantity'      => $item['qty'],
                    'subtotal'      => $lineTotal,
                ];
            }

            $discount = $data['discount'] ?? 0;
            $taxRate  = (int) Setting::get('tax_rate', 0);
            $taxable  = $subtotal - $discount;
            $tax      = (int) round($taxable * $taxRate / 100);
            $total    = $taxable + $tax;

            $amountPaid   = $data['payment_method'] === 'cash' ? ($data['amount_paid'] ?? 0) : $total;
            $changeAmount = $data['payment_method'] === 'cash' ? max(0, $amountPaid - $total) : 0;

            // Validate cash payment
            if ($data['payment_method'] === 'cash' && $amountPaid < $total) {
                return response()->json(['error' => 'Uang diterima kurang dari total.'], 422);
            }

            $transaction = Transaction::create([
                'number'         => Transaction::generateNumber(),
                'user_id'        => Auth::id(),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'amount_paid'    => $amountPaid,
                'change_amount'  => $changeAmount,
                'payment_status' => $data['payment_method'] === 'cash' ? 'paid' : 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($lineItems as $lineItem) {
                $transaction->items()->create($lineItem);
            }

            DB::commit();

            ActivityLog::log('create', "Transaksi {$transaction->number} dibuat", 'Transaction', $transaction->id);

            return response()->json([
                'success'        => true,
                'transaction_id' => $transaction->id,
                'number'         => $transaction->number,
                'total'          => $total,
                'change'         => $changeAmount,
                'status'         => $transaction->payment_status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal memproses transaksi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirm QRIS payment.
     */
    public function confirmQris(Request $request, Transaction $transaction)
    {
        if ($transaction->payment_method !== 'qris') {
            return response()->json(['error' => 'Bukan transaksi QRIS.'], 422);
        }

        $status = $request->input('status', 'paid');

        $transaction->update([
            'payment_status' => $status === 'paid' ? 'paid' : 'failed',
            'amount_paid'    => $status === 'paid' ? $transaction->total : 0,
        ]);

        ActivityLog::log(
            'update',
            "Transaksi QRIS {$transaction->number} " . ($status === 'paid' ? 'dikonfirmasi' : 'gagal'),
            'Transaction',
            $transaction->id
        );

        return response()->json([
            'success' => true,
            'status'  => $transaction->payment_status,
            'number'  => $transaction->number,
        ]);
    }

    /**
     * Get receipt data for print.
     */
    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items', 'user']);
        $settings = Setting::getAllAsArray();

        return response()->json([
            'transaction' => [
                'number'         => $transaction->number,
                'date'           => $transaction->created_at->format('d/m/Y H:i'),
                'kasir'          => $transaction->user->name,
                'items'          => $transaction->items->map(fn ($item) => [
                    'name'     => $item->product_name,
                    'price'    => $item->product_price,
                    'qty'      => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]),
                'subtotal'       => $transaction->subtotal,
                'discount'       => $transaction->discount,
                'tax'            => $transaction->tax,
                'total'          => $transaction->total,
                'payment_method' => strtoupper($transaction->payment_method),
                'amount_paid'    => $transaction->amount_paid,
                'change'         => $transaction->change_amount,
                'status'         => $transaction->payment_status,
            ],
            'store' => [
                'name'    => $settings['store_name']    ?? 'FRIED CHICKEN',
                'address' => $settings['store_address'] ?? '',
                'phone'   => $settings['store_phone']   ?? '',
                'footer'  => $settings['receipt_footer'] ?? "Terima Kasih\nSelamat Menikmati",
            ],
        ]);
    }
}
