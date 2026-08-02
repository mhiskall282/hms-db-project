<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-white leading-tight">
                    Room Maintenance & Repair Tickets
                </h2>
                <p class="text-xs text-slate-400 mt-1">Track, report, and resolve room maintenance issues across the property.</p>
            </div>

            <div x-data="{ openCreate: false }">
                <button type="button" @click="openCreate = true" class="btn-accent text-xs">
                    + Report Maintenance Issue
                </button>

                <!-- Create Ticket Modal -->
                <div x-show="openCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak>
                    <div class="bg-slate-900 border border-white/20 rounded-2xl shadow-2xl max-w-md w-full p-6 text-left" @click.away="openCreate = false">
                        <div class="flex justify-between items-center mb-4 pb-2 border-b border-white/10">
                            <h3 class="font-bold text-white text-base">Report Maintenance Issue</h3>
                            <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-white">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('maintenance.store') }}" class="space-y-4 text-xs">
                            @csrf
                            <div>
                                <label for="room_id" class="block text-slate-300 font-semibold mb-1">Select Room <span class="text-red-400">*</span></label>
                                <select name="room_id" id="room_id" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">Room {{ $room->room_number }} (Floor {{ $room->floor }} &middot; {{ ucfirst($room->status) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="issue_title" class="block text-slate-300 font-semibold mb-1">Issue Title <span class="text-red-400">*</span></label>
                                <input type="text" name="issue_title" id="issue_title" required placeholder="e.g. AC unit leaking, TV remote missing" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                            </div>

                            <div>
                                <label for="priority" class="block text-slate-300 font-semibold mb-1">Priority Level <span class="text-red-400">*</span></label>
                                <select name="priority" id="priority" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white">
                                    <option value="low">Low Priority</option>
                                    <option value="medium" selected>Medium Priority</option>
                                    <option value="high">High Priority</option>
                                    <option value="urgent">Urgent / Emergency</option>
                                </select>
                            </div>

                            <div>
                                <label for="description" class="block text-slate-300 font-semibold mb-1">Issue Description</label>
                                <textarea name="description" id="description" rows="3" placeholder="Provide details for maintenance team..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white resize-none"></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                                <button type="button" @click="openCreate = false" class="btn-ghost text-xs">Cancel</button>
                                <button type="submit" class="btn-accent text-xs">Submit Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 font-semibold uppercase">
                        <th class="py-3 px-4">Ticket ID</th>
                        <th class="py-3 px-4">Room</th>
                        <th class="py-3 px-4">Issue</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Reported By</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-white">#MT-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-3.5 px-4 font-bold text-accent">Room {{ $req->room->room_number }}</td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-white">{{ $req->issue_title }}</p>
                            @if($req->description)
                                <p class="text-slate-400 text-xs mt-0.5 line-clamp-1">{{ $req->description }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="badge text-xs uppercase font-bold px-2 py-0.5
                                {{ $req->priority === 'urgent' ? 'bg-red-500/20 text-red-400 border-red-500/40' : ($req->priority === 'high' ? 'bg-amber-500/20 text-amber-400 border-amber-500/40' : 'bg-blue-500/20 text-blue-400 border-blue-500/40') }}">
                                {{ $req->priority }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="badge badge-{{ $req->status === 'resolved' ? 'available' : 'dirty' }} text-xs uppercase">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-300">{{ $req->reporter->name ?? 'Staff' }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $req->reported_at->format('M j, Y H:i') }}</td>
                        <td class="py-3.5 px-4 text-right">
                            @if($req->status !== 'resolved')
                                <form method="POST" action="{{ route('maintenance.resolve', $req->id) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Mark this maintenance issue as resolved? Room status will automatically return to available.')" class="btn-accent text-xs px-3 py-1">
                                        Resolve Issue
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs italic">Resolved by {{ $req->resolver->name ?? 'Staff' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">No maintenance tickets reported. All rooms in working condition!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</x-app-layout>
