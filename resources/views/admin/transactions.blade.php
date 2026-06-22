@extends('layouts.app')

@section('content')
<div id="history-app" v-cloak class="space-y-6">
    <h2 class="text-indigo-400 font-black text-xl uppercase font-mono border-b border-slate-800 pb-4">Transaction History</h2>

    <div class="overflow-x-auto border border-slate-800 rounded-lg">
        <table class="w-full text-left text-xs font-mono text-slate-400">
            <thead class="bg-slate-900 text-slate-500 uppercase">
                <tr>
                    <th class="p-4">Transaction Code</th>
                    <th class="p-4">Total Amount</th>
                    <th class="p-4">Payment Method</th>
                    <th class="p-4">Date</th>
                    <th class="p-4 text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="border-t border-slate-800 hover:bg-slate-900/50">
                    <td class="p-4 text-slate-200">#{{ $tx->transaction_code }}</td>
                    <td class="p-4 text-emerald-400">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                    <td class="p-4">{{ $tx->payment_method }}</td>
                    <td class="p-4">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                    <td class="p-4 text-center">
                        <button @click="openDetail({{ $tx->id }})" class="text-indigo-400 hover:text-white underline">View Items</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div v-if="modal.show" @click.self="closeModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-slate-950 border border-slate-800 rounded-xl w-full max-w-lg p-6 relative">
            <h3 class="text-white font-bold mb-4 uppercase">Transaction Items</h3>
            <ul class="space-y-2 mb-6">
                <li v-for="item in items" class="flex justify-between border-b border-slate-800 pb-2 text-xs">
                    <span>@{{ item.name }} x @{{ item.quantity }}</span>
                    <span :class="item.is_price_changed ? 'text-amber-500' : 'text-emerald-400'">
                        Rp @{{ item.price_at_transaction.toLocaleString() }}
                    </span>
                </li>
            </ul>
            <button @click="closeModal" class="w-full py-2 bg-slate-800 text-white text-xs rounded">Close</button>
        </div>
    </div>
</div>
@endsection

@push('bottom-scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const { createApp, ref } = Vue;
        createApp({
            setup() {
                const modal = ref({ show: false });
                const items = ref([]);

                const openDetail = (id) => {
                    fetch(`/api/transactions/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                items.value = data.items;
                                modal.value.show = true;
                            }
                        });
                };

                const closeModal = () => modal.value.show = false;

                return { modal, items, openDetail, closeModal };
            }
        }).mount('#history-app');
    });
</script>
@endpush
