<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-white leading-tight">
                    Room {{ $room->room_number }} — Quality Inspection Checklist
                </h2>
                <p class="text-xs text-slate-400 mt-1">Verify room cleanliness and restock standards prior to guest arrival.</p>
            </div>
            <a href="{{ route('housekeeping.index') }}" class="btn-ghost text-xs">&larr; Back to Housekeeping</a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto card p-6">
        <form method="POST" action="{{ route('rooms.inspect.store', $room->id) }}" class="space-y-6">
            @csrf

            <div class="p-4 rounded-xl bg-slate-800 border border-white/10 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-white text-base">Room {{ $room->room_number }}</h3>
                    <p class="text-xs text-slate-400">Floor {{ $room->floor }} &middot; {{ $room->roomType->name }}</p>
                </div>
                <span class="badge badge-{{ $room->status === 'available' ? 'available' : 'dirty' }} text-xs uppercase">{{ $room->status }}</span>
            </div>

            <div class="space-y-3">
                <h4 class="font-bold text-white text-xs uppercase tracking-widest text-accent">Inspection Checklist</h4>

                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-accent transition cursor-pointer">
                    <input type="checkbox" name="linen_changed" value="1" checked class="rounded bg-slate-900 border-slate-700 text-accent focus:ring-accent w-4 h-4">
                    <span class="text-xs text-white font-medium">1. Bed linens & pillowcases fresh & replaced</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-accent transition cursor-pointer">
                    <input type="checkbox" name="bathroom_sanitized" value="1" checked class="rounded bg-slate-900 border-slate-700 text-accent focus:ring-accent w-4 h-4">
                    <span class="text-xs text-white font-medium">2. Bathroom sanitized & fresh towels placed</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-accent transition cursor-pointer">
                    <input type="checkbox" name="amenities_restocked" value="1" checked class="rounded bg-slate-900 border-slate-700 text-accent focus:ring-accent w-4 h-4">
                    <span class="text-xs text-white font-medium">3. Toiletries, soap, & tea/coffee restocked</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-accent transition cursor-pointer">
                    <input type="checkbox" name="appliances_checked" value="1" checked class="rounded bg-slate-900 border-slate-700 text-accent focus:ring-accent w-4 h-4">
                    <span class="text-xs text-white font-medium">4. Air conditioner, TV, & lights verified functional</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-accent transition cursor-pointer">
                    <input type="checkbox" name="minibar_checked" value="1" checked class="rounded bg-slate-900 border-slate-700 text-accent focus:ring-accent w-4 h-4">
                    <span class="text-xs text-white font-medium">5. Minibar inventory checked & refilled</span>
                </label>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1">Inspector Notes</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Optional notes for housekeeping supervisor..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('housekeeping.index') }}" class="btn-ghost text-xs">Cancel</a>
                <button type="submit" class="btn-accent text-xs">Submit Inspection</button>
            </div>
        </form>
    </div>
</x-app-layout>
