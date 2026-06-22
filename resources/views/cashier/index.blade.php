@extends('layouts.app')

@section('content')
<style>
    /* CSS Print: Hanya menampilkan area print dan menyembunyikan sisanya */
    @media print {
        body * { visibility: hidden !important; }
        #print-area, #print-area * { visibility: visible !important; }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 58mm;
            display: block !important;
        }
    }
</style>

<div id="cashier-app" v-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div id="print-area" class="hidden">
        <div style="width: 58mm; font-family: monospace; font-size: 12px; color: black;">
            <div style="text-align: center;">
                <h3 style="margin: 0; font-size: 16px;">BRODEV STORE</h3>

                <p style="margin: 5px 0; font-weight: bold;"># @{{ transactionCode }}</p>

                <p style="margin: 0;">{{ date('d/m/Y H:i') }}</p>
                <hr style="border-top: 1px dashed black; margin: 5px 0;">
            </div>
            <div style="margin: 10px 0;">
                <div v-for="item in cart" style="display: flex; justify-content: space-between;">
                    <span>@{{ item.quantity }} x @{{ item.name }}</span>
                    <span>Rp @{{ (item.price * item.quantity).toLocaleString() }}</span>
                </div>
            </div>

            <hr style="border-top: 1px dashed black; margin: 5px 0;">

            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal</span>
                <span>Rp @{{ total.toLocaleString() }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Tax (11%)</span>
                <span>Rp @{{ Math.round(total * 0.11).toLocaleString() }}</span>
            </div>

            <hr style="border-top: 1px dashed black; margin: 5px 0;">

            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px;">
                <span>TOTAL</span>
                <span>Rp @{{ (total + Math.round(total * 0.11)).toLocaleString() }}</span>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p>Terima Kasih!</p>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-4">
        <input v-model="searchQuery" type="text" placeholder="Search product..."
               class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs font-mono">

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="product in filteredProducts" :key="product.id"
                 @click="addToCart(product)"
                 class="bg-slate-900 border border-slate-800 p-4 rounded-xl cursor-pointer hover:border-indigo-500 transition-all">
                <p class="text-slate-200 font-bold">@{{ product.name }}</p>
                <p class="text-emerald-400 text-xs">Rp @{{ product.price.toLocaleString() }}</p>
                <p :class="isLowStock(product.stock) ? 'text-rose-400 font-bold' : 'text-slate-500'" class="text-[10px]">
                    Stock: @{{ product.stock }} @{{ isLowStock(product.stock) ? '⚠️' : '' }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl h-fit">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-indigo-400 font-bold">Current Order</h2>
            <div id="cashier-clock" class="text-[10px] font-mono text-slate-500 bg-slate-950 px-2 py-1 rounded border border-slate-800">
                --:--:--
            </div>
        </div>

        <div v-if="cart.length === 0" class="text-slate-600 text-xs italic">Cart is empty...</div>

        <div v-for="(item, index) in cart" :key="index" class="flex justify-between items-center text-xs mb-3 border-b border-slate-800 pb-2">
            <div>
                <p class="text-slate-200 font-bold">@{{ item.name }}</p>
                <p class="text-slate-500">Rp @{{ item.price.toLocaleString() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="updateQuantity(item, -1)" class="bg-slate-800 px-2 py-1 rounded hover:bg-slate-700">-</button>
                <span class="w-6 text-center font-mono">@{{ item.quantity }}</span>
                <button @click="updateQuantity(item, 1)" class="bg-indigo-800 px-2 py-1 rounded hover:bg-indigo-700">+</button>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-4 pt-4 flex justify-between font-bold">
            <span>Total</span>
            <span class="text-emerald-400">Rp @{{ total.toLocaleString() }}</span>
        </div>
        <div class="mt-4">
            <label class="block text-[10px] text-slate-500 uppercase mb-1">Payment Method</label>
            <select v-model="paymentMethod"
                    class="w-full bg-slate-950 border border-slate-800 p-2 rounded-lg text-xs text-white focus:border-indigo-500 outline-none transition-all">
                <option value="Cash">Cash</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>
        <button @click="checkout" :disabled="cart.length === 0"
                class="w-full mt-6 bg-indigo-600 py-2 rounded-lg text-white font-bold text-xs hover:bg-indigo-500 disabled:opacity-50">
            Process Checkout
        </button>
    </div>
</div>
@endsection

@push('bottom-scripts')
<script>
    // Logic Jam Real-time
    function updateClock() {
        const el = document.getElementById('cashier-clock');
        if (el) {
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}).replace(/\//g, '/');
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':');
            el.innerText = `${dateStr} | ${timeStr}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Logic Vue
    document.addEventListener('DOMContentLoaded', () => {
        const { createApp, ref, computed } = Vue;
        createApp({
            setup() {
                const products = ref(@json($products));
                const cart = ref([]);
                const searchQuery = ref('');
                const isLowStock = (stock) => stock <= 5;
                const paymentMethod = ref('Cash');
                const filteredProducts = computed(() => {
                    return products.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
                });

                const addToCart = (product) => {
                    if (product.stock <= 0) return alert('Out of stock!');
                    const existing = cart.value.find(i => i.id === product.id);
                    if (existing) existing.quantity++;
                    else cart.value.push({ ...product, quantity: 1 });
                    product.stock--;
                };

                const total = computed(() => cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0));

                const transactionCode = ref('');

                const checkout = () => {
                    const subtotal = total.value;
                    const tax = Math.round(subtotal * 0.11);
                    const totalAmount = subtotal + tax;

                    fetch('{{ route("api.checkout") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ cart: cart.value, subtotal, tax, total: totalAmount, payment_method: paymentMethod.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            transactionCode.value = data.code; // <--- Simpan kode dari server
                            setTimeout(() => {
                                window.print();
                                window.location.reload();
                            }, 500);
                        } else {
                            alert(data.message);
                        }
                    });
                };

                const updateQuantity = (item, change) => {
                    const product = products.value.find(p => p.id === item.id);
                    if (change > 0 && product.stock <= 0) return alert('Stok habis!');
                    item.quantity += change;
                    product.stock -= change;
                    if (item.quantity <= 0) cart.value.splice(cart.value.indexOf(item), 1);
                };

                return { products, cart, searchQuery, filteredProducts, addToCart, total, checkout, updateQuantity, transactionCode, isLowStock, paymentMethod};
            }
        }).mount('#cashier-app');
    });
</script>
@endpush
