<?php

namespace App\Http\Controllers;

use App\Models\Hpp;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HppController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $hppList = Hpp::with('user')
            ->byDate($date)
            ->orderByDesc('created_at')
            ->get();

        $totalHpp     = $hppList->sum('jumlah');
        $totalRevenue = Transaction::paid()
            ->whereDate('created_at', $date)
            ->sum('total');
        $labaKotor    = $totalRevenue - $totalHpp;
        $marginPersen = $totalRevenue > 0
            ? round(($labaKotor / $totalRevenue) * 100, 1)
            : 0;

        $byKategori = $hppList->groupBy('kategori')->map->sum('jumlah');

        return view('hpp.index', compact(
            'date', 'hppList', 'totalHpp',
            'totalRevenue', 'labaKotor', 'marginPersen', 'byKategori'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'    => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:200'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'kategori'   => ['required', 'in:bahan_baku,operasional,lainnya'],
            'catatan'    => ['nullable', 'string', 'max:500'],
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'jumlah.required'     => 'Jumlah wajib diisi.',
            'jumlah.min'          => 'Jumlah minimal Rp1.',
        ]);

        $data['user_id'] = Auth::id();

        Hpp::create($data);

        return back()->with('success', 'HPP berhasil ditambahkan.');
    }

    public function update(Request $request, Hpp $hpp)
    {
        $data = $request->validate([
            'tanggal'    => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:200'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'kategori'   => ['required', 'in:bahan_baku,operasional,lainnya'],
            'catatan'    => ['nullable', 'string', 'max:500'],
        ]);

        $hpp->update($data);

        return back()->with('success', 'HPP berhasil diperbarui.');
    }

    public function destroy(Hpp $hpp)
    {
        $hpp->delete();
        return back()->with('success', 'HPP berhasil dihapus.');
    }
}