<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-white leading-tight">
                Guest Reviews & Testimonials Moderation
            </h2>
            <p class="text-xs text-slate-400 mt-1">Review verified guest feedback, ratings, and publication status.</p>
        </div>
    </x-slot>

    <div class="card p-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 font-semibold uppercase">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Guest</th>
                        <th class="py-3 px-4">Booking Ref</th>
                        <th class="py-3 px-4">Rating</th>
                        <th class="py-3 px-4">Headline & Feedback</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($reviews as $rev)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="py-3.5 px-4 text-slate-400 font-mono whitespace-nowrap">{{ $rev->created_at->format('M j, Y') }}</td>
                        <td class="py-3.5 px-4 font-bold text-white">{{ $rev->guest->name ?? 'Guest' }}</td>
                        <td class="py-3.5 px-4 font-mono text-accent">#{{ $rev->booking->booking_reference ?? 'N/A' }}</td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center text-amber-400 font-bold">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                @endfor
                                <span class="ml-1 text-slate-400 text-xs font-normal">({{ $rev->rating }}/5)</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-white">{{ $rev->headline }}</p>
                            <p class="text-slate-300 text-xs mt-0.5">{{ $rev->comment }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="badge badge-{{ $rev->is_published ? 'available' : 'dirty' }} text-xs uppercase">
                                {{ $rev->is_published ? 'Published' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <form method="POST" action="{{ route('reviews.toggle', $rev->id) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-ghost text-xs px-3 py-1">
                                    {{ $rev->is_published ? 'Hide Review' : 'Publish Review' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No guest reviews submitted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $reviews->links() }}
        </div>
    </div>
</x-app-layout>
