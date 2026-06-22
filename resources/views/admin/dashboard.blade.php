@extends('layouts.app')

@section('content')
<div class="space-y-6 md:space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4 gap-2">
        <div>
            <h2 class="text-indigo-400 font-black text-xl tracking-wide uppercase font-mono">Brodev-Cashier Analytics</h2>
            <p class="text-slate-500 text-xs font-mono">Real-time daily item outflow and operational revenue matrix.</p>
        </div>
        <div class="text-xs font-mono text-slate-400 bg-slate-950 px-3 py-1.5 rounded-lg border border-slate-800 self-start sm:self-auto flex items-center gap-2">
            System Clock:
            <span id="system-clock" class="text-emerald-400 font-bold">Initializing...</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

        <div class="bg-slate-950 border border-slate-800 p-5 md:p-6 rounded-2xl flex items-center justify-between shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="space-y-1.5 z-10">
                <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Today Operational Revenue</div>
                <div class="text-2xl md:text-3xl font-black font-mono text-emerald-400">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400">Total settlement nominal values checked out by cashiers today.</p>
            </div>
            <div class="text-3xl md:text-4xl opacity-10 group-hover:opacity-20 transition-opacity z-0 select-none absolute right-4">💰</div>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 md:p-6 rounded-2xl flex items-center justify-between shadow-lg relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            <div class="space-y-1.5 z-10">
                <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Daily Products Outflow</div>
                <div class="text-2xl md:text-3xl font-black font-mono text-indigo-400">
                    {{ $todayItemsSold }} <span class="text-xs font-normal text-slate-500 font-sans">Units</span>
                </div>
                <p class="text-[11px] text-slate-400">Total cumulative item quantity subtracted from physical inventory assets today.</p>
            </div>
            <div class="text-3xl md:text-4xl opacity-10 group-hover:opacity-20 transition-opacity z-0 select-none absolute right-4">📦</div>
        </div>

    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 bg-slate-950/40 flex justify-between items-center">
            <h3 class="text-slate-200 font-bold text-xs font-mono uppercase tracking-wider">7-Day Operational Performance Matrix</h3>
            <span class="text-[9px] md:text-[10px] bg-slate-900 border border-slate-800 px-2 py-0.5 rounded font-mono text-slate-500">Interval: Aggregated Daily</span>
        </div>

        <div class="w-full overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead class="bg-slate-900/40 text-[10px] font-mono text-slate-500 uppercase">
                    <tr>
                        <th class="p-4 border-b border-slate-800">Calendar Date</th>
                        <th class="p-4 border-b border-slate-800">Items Outward Volume</th>
                        <th class="p-4 border-b border-slate-800 text-right">Settled Gross Revenue</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-mono text-slate-400">
                    @foreach($trends as $day)
                    <tr class="border-b border-slate-800/40 hover:bg-slate-900/30 transition-all">
                        <td class="p-4 text-slate-300 font-sans font-medium">{{ $day['date'] }}</td>
                        <td class="p-4">
                            <span class="{{ $day['items_sold'] > 0 ? 'text-indigo-400 font-bold' : 'text-slate-600' }}">
                                {{ $day['items_sold'] }} units
                            </span>
                        </td>
                        <td class="p-4 text-right text-emerald-400 font-bold">
                            Rp {{ number_format($day['revenue'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('bottom-scripts')
<script>
    window.nexusLocalSetup = function() {
        return {}; // Safe context fallback bypass injection
    };
    function updateClock() {
        const el = document.getElementById('system-clock');
        if (el) {
            const now = new Date();

            // Format Tanggal (DD/MM/YYYY)
            const dateStr = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).replace(/\//g, '/'); // Tetap menggunakan /

            // Format Waktu (HH:mm:ss)
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            el.innerText = `${dateStr} | ${timeStr}`;
        }
    }

    // Update setiap detik
    setInterval(updateClock, 1000);
    updateClock();

    window.nexusLocalSetup = function() {
        return {};
    };
</script>
@endpush
