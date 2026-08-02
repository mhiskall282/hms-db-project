<x-app-layout>
    <x-slot name="title">Occupancy Report</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Hotel Occupancy Report</h2>
            <p class="text-xs text-gray-500">Track daily room occupancy counts and occupancy percentage rates (FR-7.1).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.occupancy.pdf', request()->all()) }}" class="btn-outline btn-sm">Export PDF</a>
            <a href="{{ route('reports.occupancy.csv', request()->all()) }}" class="btn-accent btn-sm">Export CSV</a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('reports.occupancy') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label for="from" class="form-label text-xs">Start Date</label>
                <input type="date" name="from" id="from" value="{{ $from->toDateString() }}" class="form-input text-xs">
            </div>
            <div>
                <label for="to" class="form-label text-xs">End Date</label>
                <input type="date" name="to" id="to" value="{{ $to->toDateString() }}" class="form-input text-xs">
            </div>
            <button type="submit" class="btn-primary btn-sm">Generate Report</button>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Occupied Rooms</th>
                        <th>Available Rooms</th>
                        <th>Total Operable Rooms</th>
                        <th>Occupancy Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr>
                        <td class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($row['date'])->format('D, M j, Y') }}</td>
                        <td class="font-semibold text-red-600">{{ $row['occupied'] }}</td>
                        <td class="font-semibold text-green-600">{{ $row['available'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-primary text-xs w-10">{{ $row['occupancy_rate'] }}%</span>
                                <div class="w-32 bg-gray-100 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full" style="width: {{ $row['occupancy_rate'] }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">No data available for the selected date range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
