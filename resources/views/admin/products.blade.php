@extends('layouts.app')

@section('content')
<div id="inventory-app" v-cloak class="space-y-6">
    <div class="flex justify-between border-b border-slate-800 pb-4">
        <h2 class="text-indigo-400 font-black text-xl uppercase font-mono">Inventory Stock Core</h2>
        <button @click="openAddModal" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-lg text-white text-xs font-bold font-mono transition-all">
            ➕ Add New Product
        </button>
    </div>

    <div class="w-full overflow-x-auto border border-slate-800 rounded-lg">
        <table class="w-full text-left text-xs font-mono text-slate-400">
            <thead class="bg-slate-900 text-slate-500 uppercase">
                <tr>
                    <th class="p-4">Name</th>
                    <th class="p-4">SKU</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4 text-center">Control</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in inventory" :key="item.id"
                    :class="item.deleted_at ? 'bg-rose-950/20 opacity-60' : ''"
                    class="border-t border-slate-800 hover:bg-slate-900/50">
                    <td class="p-4">
                        <span :class="item.deleted_at ? 'line-through text-slate-500' : 'text-slate-200'">@{{ item.name }}</span>
                        <span v-if="item.deleted_at" class="text-[9px] text-rose-500 ml-2 italic">[DELETED]</span>
                    </td>
                    <td class="p-4">@{{ item.sku }}</td>
                    <td class="p-4">@{{ item.stock }}</td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button v-if="item.deleted_at" @click="restoreProduct(item.id)" class="text-emerald-400 hover:text-white">♻️ Restore</button>
                            <template v-else>
                                <button @click="openEditModal(item)" class="text-indigo-400 hover:text-white">✏️</button>
                                <button @click="deleteProduct(item.id)" class="text-rose-500 hover:text-white">🗑️</button>
                            </template>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-if="modal.show" @click.self="closeModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-slate-950 border border-slate-800 rounded-xl w-full max-w-sm p-6 relative">
            <h3 class="text-white font-bold mb-4 uppercase">@{{ modal.isEdit ? 'Update Record' : 'Inject Product' }}</h3>
            <div class="space-y-3">
                <input v-model="form.name" placeholder="Name" class="w-full bg-slate-900 border border-slate-700 p-2 text-white text-xs">
                <input v-model="form.sku" placeholder="SKU" class="w-full bg-slate-900 border border-slate-700 p-2 text-white text-xs">
                <div class="flex gap-2">
                    <input v-model.number="form.stock" placeholder="Stock" type="number" class="w-1/2 bg-slate-900 border border-slate-700 p-2 text-white text-xs">
                    <input v-model.number="form.price" placeholder="Price" type="number" class="w-1/2 bg-slate-900 border border-slate-700 p-2 text-white text-xs">
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button @click="closeModal"
                        class="flex-1 py-2 bg-slate-800 text-slate-400 text-xs font-bold rounded hover:bg-slate-700 hover:text-white transition-colors">
                    Close
                </button>

                <button @click="saveProduct"
                        class="flex-2 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-500">
                    Commit Transaction
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('bottom-scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('inventory-app');
        if (el) {
            const { createApp, ref } = Vue;
            createApp({
                setup() {
                    const inventory = ref(@json($products));
                    const modal = ref({ show: false, isEdit: false, currentId: null });
                    const form = ref({ name: '', sku: '', stock: 0, price: 0 });
                    const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

                    const openAddModal = () => { modal.value = { show: true, isEdit: false }; form.value = { name: '', sku: '', stock: 0, price: 0 }; };
                    const openEditModal = (item) => { modal.value = { show: true, isEdit: true, currentId: item.id }; form.value = { ...item }; };
                    const closeModal = () => modal.value.show = false;

                    const saveProduct = () => {
                        const url = modal.value.isEdit ? `/api/products/${modal.value.currentId}` : '/api/products';
                        const method = modal.value.isEdit ? 'PUT' : 'POST';
                        fetch(url, {
                            method: method,
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
                            body: JSON.stringify(form.value)
                        }).then(() => window.location.reload());
                    };

                    const deleteProduct = (id) => {
                        if(!confirm('Are you sure?')) return;
                        fetch(`/api/products/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': getCsrf() } })
                        .then(() => window.location.reload());
                    };

                    const restoreProduct = (id) => {
                        fetch(`/api/products/restore/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrf() } })
                        .then(() => window.location.reload());
                    };

                    return { inventory, modal, form, openAddModal, openEditModal, closeModal, saveProduct, deleteProduct, restoreProduct };
                }
            }).mount('#inventory-app');
        }
    });
</script>
@endpush
