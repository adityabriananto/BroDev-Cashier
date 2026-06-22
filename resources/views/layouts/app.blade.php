<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style> [v-cloak] { display: none; } </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Brodev-Cashier Engine</title>
</head>
<body class="h-full bg-slate-950 text-slate-200">
    <nav class="border-b border-slate-800 p-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-y-4">
            <h1 class="font-bold text-indigo-400">Brodev-Cashier</h1>

            <div class="flex items-center gap-4 text-xs font-mono order-3 w-full sm:order-none sm:w-auto overflow-x-auto py-2 sm:py-0">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white whitespace-nowrap">📊 Dashboard</a>
                    <a href="{{ route('admin.products') }}" class="hover:text-white whitespace-nowrap">📦 Inventory</a>
                    <a href="{{ route('admin.transactions') }}" class="hover:text-white whitespace-nowrap">📜 History</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-rose-400 hover:text-rose-300">🚪 Logout</button>
                    </form>
                @else
                    <a href="{{ route('cashier.index') }}" class="hover:text-white">🛒 Cashier</a>
                    <a href="{{ route('login') }}" class="text-indigo-400">🔒 Sign In</a>
                @endauth
            </div>

            <div class="text-[9px] font-mono text-slate-600 border border-slate-800 px-2 py-1 rounded bg-slate-900/50 order-2 sm:order-none">
                <span>⏱ {{ $performance['time'] }}s</span>
                <span class="ml-2">💾 {{ $performance['memory'] }} MB</span>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto p-4">
        @yield('content')
    </main>
    <footer class="mt-auto py-8 text-center border-t border-slate-800">
        <p class="text-[10px] text-slate-600 font-mono uppercase tracking-widest">
            &copy; {{ date('Y') }} Brodev Cashier System. All rights reserved.
        </p>
        <p class="text-[9px] text-slate-700 mt-1">Powered by Laravel & Vue 3</p>
    </footer>
    @stack('bottom-scripts')
</body>
</html>
