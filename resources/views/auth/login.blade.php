<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>NEXUS_CORE - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-sm bg-slate-950 border border-slate-800 p-8 rounded-2xl shadow-xl">
        <div class="mb-8 text-center">
            <h1 class="text-indigo-400 font-black text-2xl tracking-tighter">NEXUS_CORE</h1>
            <p class="text-slate-500 text-xs font-mono mt-2 uppercase tracking-widest">Authorized Access Only</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] text-slate-500 font-mono uppercase mb-1 block ml-1">Email Address</label>
                <input type="email" name="email" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-4 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
            </div>
            <div>
                <label class="text-[10px] text-slate-500 font-mono uppercase mb-1 block ml-1">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-4 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
            </div>

            @if ($errors->any())
                <p class="text-rose-500 text-[10px] font-mono italic">{{ $errors->first() }}</p>
            @endif

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-lg text-xs font-bold transition-all shadow-md uppercase tracking-widest mt-4">
                Sign In to Nexus
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/" class="text-[10px] text-slate-600 hover:text-slate-400 font-mono transition-colors">← Back to Cashier Terminal</a>
        </div>
    </div>
</body>
</html>
