<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-white leading-tight">
                System Security & Activity Audit Trail
            </h2>
            <p class="text-xs text-slate-400 mt-1">Audit log of system events, role actions, IP addresses, and user activities.</p>
        </div>
    </x-slot>

    <div class="card p-6 space-y-6">
        <!-- Search & Module Filter -->
        <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label for="module" class="block text-slate-400 mb-1">Filter by Module</label>
                <select name="module" id="module" onchange="this.form.submit()" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                    <option value="">All Modules</option>
                    <option value="auth" {{ request('module') === 'auth' ? 'selected' : '' }}>Authentication</option>
                    <option value="booking" {{ request('module') === 'booking' ? 'selected' : '' }}>Booking Engine</option>
                    <option value="check_in_out" {{ request('module') === 'check_in_out' ? 'selected' : '' }}>Check-In / Out</option>
                    <option value="billing" {{ request('module') === 'billing' ? 'selected' : '' }}>Billing & Invoices</option>
                    <option value="rooms" {{ request('module') === 'rooms' ? 'selected' : '' }}>Room Management</option>
                    <option value="housekeeping" {{ request('module') === 'housekeeping' ? 'selected' : '' }}>Housekeeping & Maintenance</option>
                    <option value="users" {{ request('module') === 'users' ? 'selected' : '' }}>User & Role Management</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="search" class="block text-slate-400 mb-1">Search Logs</label>
                <div class="flex gap-2">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search description, event code, or IP address..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                    <button type="submit" class="btn-accent px-4 py-2 text-xs">Search</button>
                    @if(request()->anyFilled(['module', 'search']))
                        <a href="{{ route('audit-logs.index') }}" class="btn-ghost px-3 py-2 text-xs">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Audit Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 font-semibold uppercase">
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">Actor / Staff</th>
                        <th class="py-3 px-4">Module</th>
                        <th class="py-3 px-4">Event</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="py-3 px-4 text-slate-400 font-mono whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3 px-4 font-semibold text-white">
                            {{ $log->user->name ?? 'System Guest / Public' }}
                            @if($log->user)
                                <span class="text-xs text-slate-400 font-normal">({{ $log->user->roles->pluck('name')->join(', ') }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge bg-slate-800 text-slate-300 border-slate-700 uppercase font-mono text-[10px]">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-accent">{{ $log->event }}</td>
                        <td class="py-3 px-4 text-slate-200">{{ $log->description }}</td>
                        <td class="py-3 px-4 font-mono text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">No audit logs matching your filter criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $auditLogs->links() }}
        </div>
    </div>
</x-app-layout>
