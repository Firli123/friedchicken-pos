@extends('layouts.app')
@section('title','Kasir POS')
@section('page-title','Kasir')

@push('styles')
<style>
    #content { padding:0 !important; }
    .pos-wrap { display:flex; height:calc(100vh - 60px); overflow:hidden; }

    /* LEFT */
    .pos-left { flex:1; display:flex; flex-direction:column; background:#f5f5f5; overflow:hidden; }
    .pos-header {
        background:#fff; padding:12px 16px; border-bottom:1px solid #e0e0e0;
        display:flex; align-items:center; gap:8px; flex-wrap:wrap;
    }
    .cat-btn {
        height:44px; padding:0 18px; border-radius:22px; border:2px solid #e0e0e0;
        background:#fff; font-size:0.85rem; font-weight:600; cursor:pointer;
        color:#757575; transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
        white-space:nowrap; -webkit-tap-highlight-color:transparent;
    }
    .cat-btn.active,.cat-btn:hover { background:#E53935; border-color:#E53935; color:#fff; }
    .search-wrap { position:relative; margin-left:auto; }
    .search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#bdbdbd; }
    .search-input {
        height:44px; padding:0 14px 0 36px; border:2px solid #e0e0e0; border-radius:22px;
        font-size:0.85rem; width:160px; outline:none;
        font-family:'Plus Jakarta Sans',sans-serif; background:#fafafa; transition:all 0.2s;
    }
    .search-input:focus { border-color:#E53935; background:#fff; }

    /* Product Grid */
    .prod-grid {
        flex:1; overflow-y:auto; padding:12px;
        display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
        gap:10px; align-content:start; -webkit-overflow-scrolling:touch;
    }
    @media(max-width:1024px) { .prod-grid { grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); } }
    @media(max-width:768px)  { .prod-grid { grid-template-columns:repeat(3,1fr); gap:8px; } }

    .prod-card {
        background:#fff; border-radius:14px; border:2.5px solid transparent;
        padding:18px 10px; cursor:pointer; text-align:center;
        transition:all 0.15s; box-shadow:0 2px 8px rgba(0,0,0,0.06);
        user-select:none; -webkit-tap-highlight-color:transparent;
    }
    .prod-card:hover { border-color:#E53935; transform:translateY(-2px); box-shadow:0 6px 16px rgba(229,57,53,0.15); }
    .prod-card:active { transform:scale(0.95); }
    .prod-emoji { font-size:2.4rem; display:block; margin-bottom:8px; }
    .prod-name { font-size:0.82rem; font-weight:700; color:#1a1a1a; line-height:1.3; margin-bottom:6px; }
    .prod-price { font-size:0.9rem; font-weight:800; color:#E53935; }

    /* RIGHT: Cart */
    .pos-right {
        width:320px; min-width:300px; background:#fff;
        display:flex; flex-direction:column; border-left:1px solid #e0e0e0;
    }
    @media(max-width:768px) { .pos-right { width:270px; min-width:250px; } }

    .cart-head {
        padding:14px 16px; border-bottom:1px solid #f0f0f0;
        display:flex; align-items:center; justify-content:space-between;
    }
    .cart-title { font-size:0.95rem; font-weight:700; color:#1a1a1a; }
    .cart-sub { font-size:0.75rem; color:#aaa; margin-top:2px; }
    .btn-clear {
        height:36px; padding:0 12px; border:1.5px solid #FFCDD2; background:#FFF5F5;
        color:#E53935; border-radius:8px; font-size:0.78rem; font-weight:600;
        cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:all 0.2s;
    }
    .btn-clear:hover { background:#E53935; color:#fff; border-color:#E53935; }
    .btn-clear:disabled { opacity:0.4; cursor:not-allowed; }

    .cart-items { flex:1; overflow-y:auto; padding:8px; -webkit-overflow-scrolling:touch; }
    .cart-empty {
        height:100%; display:flex; flex-direction:column;
        align-items:center; justify-content:center; color:#ddd; gap:8px;
    }
    .cart-empty i { font-size:3rem; }
    .cart-empty p { font-size:0.82rem; font-weight:600; color:#ccc; }

    .cart-item {
        display:flex; align-items:center; gap:8px; padding:10px;
        border-radius:10px; background:#fafafa; border:1px solid #f0f0f0; margin-bottom:6px;
    }
    .ci-name { font-size:0.82rem; font-weight:600; flex:1; line-height:1.3; color:#1a1a1a; }
    .ci-price { font-size:0.72rem; color:#aaa; }
    .ci-sub { font-size:0.85rem; font-weight:800; color:#E53935; min-width:65px; text-align:right; }
    .qty-wrap { display:flex; align-items:center; gap:4px; }
    .qty-btn {
        width:30px; height:30px; border-radius:50%; border:2px solid #e0e0e0;
        background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
        font-size:1rem; font-weight:700; color:#424242; transition:all 0.15s;
        -webkit-tap-highlight-color:transparent;
    }
    .qty-btn:hover { background:#E53935; border-color:#E53935; color:#fff; }
    .qty-val { font-weight:700; font-size:0.88rem; min-width:22px; text-align:center; }
    .btn-rm { background:none; border:none; color:#e0e0e0; cursor:pointer; padding:0 0 0 4px; transition:color 0.2s; }
    .btn-rm:hover { color:#E53935; }

    /* Summary */
    .pos-summary { border-top:1px solid #f0f0f0; padding:14px 16px; background:#fafafa; }
    .sum-row { display:flex; justify-content:space-between; font-size:0.82rem; color:#757575; margin-bottom:6px; }
    .sum-total { display:flex; justify-content:space-between; align-items:center; padding-top:10px; border-top:2px solid #f0f0f0; margin-bottom:12px; }
    .sum-label { font-size:0.9rem; font-weight:700; color:#1a1a1a; }
    .sum-value { font-size:1.4rem; font-weight:800; color:#E53935; }
    .btn-pay {
        width:100%; height:54px; background:#E53935; color:#fff; border:none;
        border-radius:12px; font-size:1rem; font-weight:700; cursor:pointer;
        display:flex; align-items:center; justify-content:center; gap:8px;
        transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
        -webkit-tap-highlight-color:transparent;
    }
    .btn-pay:hover { background:#C62828; }
    .btn-pay:disabled { background:#ccc; cursor:not-allowed; }

    /* Modal */
    .modal-content { border-radius:20px; border:none; overflow:hidden; }
    .modal-header { background:#E53935; color:#fff; border:none; padding:16px 20px; }
    .modal-title { font-weight:800; }

    .method-wrap { display:flex; gap:10px; margin-bottom:20px; }
    .method-btn {
        flex:1; height:52px; border:2.5px solid #e0e0e0; background:#fff;
        border-radius:12px; font-size:0.88rem; font-weight:700; cursor:pointer;
        color:#757575; transition:all 0.2s; font-family:'Plus Jakarta Sans',sans-serif;
        display:flex; align-items:center; justify-content:center; gap:6px;
        -webkit-tap-highlight-color:transparent;
    }
    .method-btn.active { background:#E53935; border-color:#E53935; color:#fff; }

    .total-box { text-align:center; background:#FFF5F5; border-radius:12px; padding:16px; margin-bottom:20px; }
    .total-box .lbl { font-size:0.72rem; color:#aaa; font-weight:600; text-transform:uppercase; letter-spacing:1px; }
    .total-box .val { font-size:2rem; font-weight:800; color:#E53935; margin-top:4px; }

    .cash-in {
        width:100%; height:56px; border:2.5px solid #E53935; border-radius:12px;
        font-size:1.4rem; font-weight:800; text-align:center; outline:none;
        font-family:'Plus Jakarta Sans',sans-serif; color:#1a1a1a; margin-bottom:12px;
    }

    .quick-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin-bottom:12px; }
    .quick-btn {
        height:42px; border:1.5px solid #e0e0e0; background:#fafafa; border-radius:8px;
        font-size:0.72rem; font-weight:600; cursor:pointer; transition:all 0.15s;
        font-family:'Plus Jakarta Sans',sans-serif;
    }
    .quick-btn:hover { background:#E53935; color:#fff; border-color:#E53935; }

    .numpad { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
    .num-btn {
        height:54px; border:1.5px solid #e0e0e0; background:#fafafa; border-radius:10px;
        font-size:1.1rem; font-weight:600; cursor:pointer; transition:all 0.15s;
        font-family:'Plus Jakarta Sans',sans-serif; -webkit-tap-highlight-color:transparent;
    }
    .num-btn:hover { background:#E53935; color:#fff; border-color:#E53935; }
    .num-btn:active { transform:scale(0.94); }

    .change-box { background:#E8F5E9; border:2px solid #A5D6A7; border-radius:12px; padding:12px 16px; text-align:center; }
    .change-box .lbl { font-size:0.72rem; color:#2E7D32; font-weight:600; text-transform:uppercase; letter-spacing:1px; }
    .change-box .val { font-size:1.6rem; font-weight:800; color:#1B5E20; }
    .err-box { background:#FFEBEE; border:1.5px solid #FFCDD2; border-radius:8px; padding:8px 12px; font-size:0.82rem; color:#C62828; text-align:center; }

    .qris-wrap { text-align:center; padding:8px 0; }
    .qris-wrap img { max-width:190px; border:3px solid #E53935; border-radius:14px; padding:8px; margin-bottom:12px; }
    .qris-amt { font-size:1.5rem; font-weight:800; color:#E53935; }
    .qris-hint { font-size:0.78rem; color:#aaa; margin-top:8px; }

    #receiptContent { font-family:'Courier New',monospace; font-size:12px; padding:16px; background:#FFFDE7; white-space:pre-wrap; }
</style>
@endpush

@section('content')
<div class="pos-wrap">

    <!-- LEFT -->
    <div class="pos-left">
        <div class="pos-header">
            <button class="cat-btn active" onclick="filterCat(this,'all')">Semua</button>
            @foreach($categories as $cat)
            <button class="cat-btn" onclick="filterCat(this,'{{ $cat->slug }}')">{{ $cat->name }}</button>
            @endforeach
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input class="search-input" type="search" id="searchInput" placeholder="Cari produk...">
            </div>
        </div>

        <div class="prod-grid" id="prodGrid">
            @foreach($categories as $cat)
                @foreach($cat->products as $p)
                <div class="prod-card"
                     data-cat="{{ $cat->slug }}"
                     data-name="{{ strtolower($p->name) }}"
                     onclick="addCart({{ $p->id }},'{{ addslashes($p->name) }}',{{ $p->price }})">
                    <span class="prod-emoji">
                        @if($cat->slug==='paket')🍱
                        @elseif($cat->slug==='tambahan')🍚
                        @else🍗
                        @endif
                    </span>
                    <div class="prod-name">{{ $p->name }}</div>
                    <div class="prod-price">Rp{{ number_format($p->price,0,',','.') }}</div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- RIGHT -->
    <div class="pos-right">
        <div class="cart-head">
            <div>
                <div class="cart-title">🛒 Keranjang</div>
                <div class="cart-sub" id="cartCount">0 item</div>
            </div>
            <button class="btn-clear" id="btnClear" onclick="clearCart()" disabled>
                <i class="bi bi-trash3"></i> Kosongkan
            </button>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="cart-empty" id="cartEmpty">
                <i class="bi bi-cart-x"></i>
                <p>Keranjang kosong</p>
            </div>
        </div>

        <div class="pos-summary">
            <div class="sum-row"><span>Subtotal</span><span id="subTotal">Rp0</span></div>
            <div class="sum-row"><span>Diskon</span><span>Rp0</span></div>
            <div class="sum-total">
                <span class="sum-label">TOTAL</span>
                <span class="sum-value" id="grandTotal">Rp0</span>
            </div>
            <button class="btn-pay" id="btnPay" onclick="openPay()" disabled>
                <i class="bi bi-credit-card-2-front"></i> Bayar Sekarang
            </button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">💳 Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="method-wrap">
                    <button class="method-btn active" onclick="switchMethod('cash',this)"><i class="bi bi-cash"></i> Cash</button>
                    <button class="method-btn" onclick="switchMethod('qris',this)"><i class="bi bi-qr-code"></i> QRIS</button>
                </div>
                <div class="total-box">
                    <div class="lbl">Total Pembayaran</div>
                    <div class="val" id="modalTotal">Rp0</div>
                </div>

                <!-- Cash -->
                <div id="cashPanel">
                    <input type="text" class="cash-in" id="cashInput" placeholder="0" inputmode="numeric">
                    <div class="quick-grid" id="quickAmounts"></div>
                    <div class="numpad">
                        @foreach(['7','8','9','4','5','6','1','2','3','000','0','⌫'] as $k)
                        <button class="num-btn" onclick="numpad('{{ $k }}')">{{ $k }}</button>
                        @endforeach
                    </div>
                    <div class="change-box" id="changeBox" style="display:none;">
                        <div class="lbl">Kembalian</div>
                        <div class="val" id="changeVal">Rp0</div>
                    </div>
                    <div class="err-box" id="errBox" style="display:none;margin-top:8px;"></div>
                </div>

                <!-- QRIS -->
                <div id="qrisPanel" style="display:none;">
                    <div class="qris-wrap">
                        @if(!empty($settings['qris_image']))
                        <img src="{{ asset('storage/'.$settings['qris_image']) }}" alt="QRIS">
                        @else
                        <div style="width:180px;height:180px;border:3px dashed #E53935;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;margin:0 auto 12px;color:#ccc;gap:8px;">
                            <i class="bi bi-qr-code" style="font-size:3rem;"></i>
                            <small>Upload QR di Pengaturan</small>
                        </div>
                        @endif
                        <div class="qris-amt" id="qrisAmt">Rp0</div>
                        <div class="qris-hint">Scan QR lalu tekan tombol setelah pembayaran berhasil</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success flex-fill fw-700" id="btnConfirm" onclick="confirmPay()">
                    <i class="bi bi-check-circle me-1"></i> Konfirmasi
                </button>
                <button class="btn btn-danger flex-fill fw-700" id="btnQrisFail" onclick="qrisFail()" style="display:none;">
                    <i class="bi bi-x-circle me-1"></i> Gagal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#2E7D32;color:#fff;border:none;">
                <h5 class="modal-title fw-700"><i class="bi bi-check-circle me-2"></i>Transaksi Berhasil!</h5>
            </div>
            <div class="modal-body p-0"><div id="receiptContent"></div></div>
            <div class="modal-footer border-0 pb-4 px-4 gap-2">
                <button class="btn btn-outline-secondary" onclick="printReceipt()"><i class="bi bi-printer me-1"></i> Cetak</button>
                <button class="btn btn-danger flex-fill fw-700" onclick="newTrx()">Transaksi Baru</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TAX = {{ (int)($settings['tax_rate'] ?? 0) }};
let cart = {}, method = 'cash', trxId = null;

const rp = n => 'Rp' + parseInt(n).toLocaleString('id-ID');
const getTotal = () => { const s = Object.values(cart).reduce((a,i)=>a+i.price*i.qty,0); return s + Math.round(s*TAX/100); };

function addCart(id, name, price) {
    cart[id] ? cart[id].qty++ : (cart[id] = {id,name,price,qty:1});
    renderCart();
}
function changeQty(id,d) { cart[id] && (cart[id].qty+=d); if(cart[id]?.qty<=0) delete cart[id]; renderCart(); }
function removeItem(id) { delete cart[id]; renderCart(); }
function clearCart() { cart={}; renderCart(); }

function renderCart() {
    const items = Object.values(cart);
    const el = document.getElementById('cartItems');
    el.querySelectorAll('.cart-item').forEach(e=>e.remove());

    const empty = items.length === 0;
    document.getElementById('cartEmpty').style.display = empty ? 'flex' : 'none';
    document.getElementById('btnClear').disabled = empty;
    document.getElementById('btnPay').disabled = empty;

    items.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <div style="flex:1;min-width:0;">
                <div class="ci-name">${item.name}</div>
                <div class="ci-price">${rp(item.price)}/pcs</div>
            </div>
            <div class="qty-wrap">
                <button class="qty-btn" onclick="changeQty(${item.id},-1)">−</button>
                <span class="qty-val">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${item.id},1)">+</button>
            </div>
            <div class="ci-sub">${rp(item.price*item.qty)}</div>
            <button class="btn-rm" onclick="removeItem(${item.id})"><i class="bi bi-x-circle"></i></button>
        `;
        el.insertBefore(div, document.getElementById('cartEmpty'));
    });

    const sub = Object.values(cart).reduce((a,i)=>a+i.price*i.qty,0);
    const cnt = items.reduce((a,i)=>a+i.qty,0);
    document.getElementById('subTotal').textContent = rp(sub);
    document.getElementById('grandTotal').textContent = rp(getTotal());
    document.getElementById('cartCount').textContent = cnt + ' item';
}

function filterCat(btn, cat) {
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.prod-card').forEach(c=>{
        c.style.display = (cat==='all'||c.dataset.cat===cat) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.prod-card').forEach(c=>{
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });
});

function openPay() {
    if (!Object.values(cart).length) return;
    const total = getTotal();
    document.getElementById('modalTotal').textContent = rp(total);
    document.getElementById('qrisAmt').textContent = rp(total);
    document.getElementById('cashInput').value = '';
    document.getElementById('changeBox').style.display = 'none';
    document.getElementById('errBox').style.display = 'none';
    renderQuick(total);
    new bootstrap.Modal(document.getElementById('payModal')).show();
}

function switchMethod(m, btn) {
    method = m;
    document.querySelectorAll('.method-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('cashPanel').style.display = m==='cash' ? '' : 'none';
    document.getElementById('qrisPanel').style.display = m==='qris' ? '' : 'none';
    document.getElementById('btnQrisFail').style.display = m==='qris' ? '' : 'none';
    document.getElementById('btnConfirm').innerHTML = m==='cash'
        ? '<i class="bi bi-check-circle me-1"></i> Konfirmasi'
        : '✅ Pembayaran Berhasil';
}

function numpad(k) {
    const el = document.getElementById('cashInput');
    let v = el.value.replace(/\D/g,'');
    if(k==='⌫') v=v.slice(0,-1);
    else if(k==='000') v+='000';
    else v+=k;
    el.value = v ? parseInt(v).toLocaleString('id-ID') : '';
    validateCash();
}

document.getElementById('cashInput').addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    this.value = v ? parseInt(v).toLocaleString('id-ID') : '';
    validateCash();
});

const getCash = () => parseInt(document.getElementById('cashInput').value.replace(/\D/g,''))||0;

function validateCash() {
    const cash=getCash(), total=getTotal();
    const cb=document.getElementById('changeBox'), eb=document.getElementById('errBox');
    if(cash>=total&&cash>0) {
        cb.style.display=''; eb.style.display='none';
        document.getElementById('changeVal').textContent = rp(cash-total);
    } else if(cash>0) {
        cb.style.display='none'; eb.style.display='';
        eb.textContent = 'Kurang ' + rp(total-cash);
    } else {
        cb.style.display='none'; eb.style.display='none';
    }
}

function renderQuick(total) {
    const amounts = [...new Set([total, Math.ceil(total/5000)*5000, Math.ceil(total/10000)*10000, 50000, 100000])].slice(0,4);
    document.getElementById('quickAmounts').innerHTML = amounts.map(a=>`<button class="quick-btn" onclick="setCash(${a})">${rp(a)}</button>`).join('');
}

function setCash(a) { document.getElementById('cashInput').value=a.toLocaleString('id-ID'); validateCash(); }

async function confirmPay() {
    const total = getTotal();
    if(method==='cash'&&getCash()<total) {
        document.getElementById('errBox').style.display='';
        document.getElementById('errBox').textContent='Uang diterima kurang!';
        return;
    }
    const btn = document.getElementById('btnConfirm');
    btn.disabled=true;
    btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    try {
        const res = await fetch('{{ route("pos.payment") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.csrfToken},
            body:JSON.stringify({
                items: Object.values(cart).map(i=>({id:i.id,qty:i.qty})),
                payment_method: method,
                amount_paid: method==='cash' ? getCash() : total,
                discount: 0,
            }),
        });
        const data = await res.json();
        if(data.success) {
            trxId = data.transaction_id;
            if(method==='qris') {
                await fetch(`/pos/qris/${trxId}/confirm`,{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.csrfToken},
                    body:JSON.stringify({status:'paid'}),
                });
            }
            bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();
            await showReceipt(trxId);
        } else { alert(data.error||'Terjadi kesalahan'); }
    } catch(e) { alert('Error: '+e.message); }
    finally {
        btn.disabled=false;
        btn.innerHTML='<i class="bi bi-check-circle me-1"></i> Konfirmasi';
    }
}

async function qrisFail() {
    if(!trxId) return;
    await fetch(`/pos/qris/${trxId}/confirm`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.csrfToken},body:JSON.stringify({status:'failed'})});
    bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();
    clearCart();
}

async function showReceipt(id) {
    const res = await fetch(`/pos/receipt/${id}`);
    const {transaction:t, store:s} = await res.json();
    const line='================================';
    let txt=`${line}\n      ${s.name}\n${line}\n${t.number}\n${t.date}\nKasir: ${t.kasir}\n--------------------------------\n`;
    t.items.forEach(i=>{txt+=`${i.name} x${i.qty}\n${' '.repeat(12)}${parseInt(i.subtotal).toLocaleString('id-ID')}\n`;});
    txt+=`--------------------------------\nTOTAL    ${parseInt(t.total).toLocaleString('id-ID').padStart(13)}\nMETODE   ${t.payment_method.padStart(13)}\nBAYAR    ${parseInt(t.amount_paid).toLocaleString('id-ID').padStart(13)}\n`;
    if(t.change>0) txt+=`KEMBALI  ${parseInt(t.change).toLocaleString('id-ID').padStart(13)}\n`;
    txt+=`${line}\n${s.footer}\n${line}`;
    document.getElementById('receiptContent').textContent=txt;
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
}

function printReceipt() {
    const c=document.getElementById('receiptContent').textContent;
    const w=window.open('','_blank','width=400,height=600');
    w.document.write(`<html><head><style>body{margin:8px;font-family:'Courier New',monospace;font-size:12px;}pre{margin:0;}</style></head><body><pre>${c}</pre><script>window.print();<\/script></body></html>`);
    w.document.close();
}

function newTrx() {
    bootstrap.Modal.getInstance(document.getElementById('receiptModal')).hide();
    clearCart();
}
</script>
@endpush
