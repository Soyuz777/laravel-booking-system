<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Booking Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white shadow rounded p-4">
                <div class="text-sm text-gray-500">Total Bookings</div>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white shadow rounded p-4">
                <div class="text-sm text-gray-500">Upcoming</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['upcoming'] }}</div>
            </div>
            <div class="bg-white shadow rounded p-4">
                <div class="text-sm text-gray-500">Pending</div>
                <div class="text-2xl font-bold text-yellow-500">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white shadow rounded p-4">
                <div class="text-sm text-gray-500">Completed</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
            </div>
            <div class="bg-white shadow rounded p-4">
                <div class="text-sm text-gray-500">Total Users</div>
                <div class="text-2xl font-bold text-purple-600">{{ $stats['users'] }}</div>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Calendar --}}
        <div id="calendar" class="bg-white p-4 rounded shadow mb-6"></div>

        {{-- Booking Table --}}
        <table class="w-full bg-white shadow rounded overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-3">Title</th>
                    <th class="text-left p-3">Date</th>
                    <th class="text-left p-3">Time</th>
                    <th class="text-left p-3">Duration</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $booking->title }}</td>
                        <td class="p-3">{{ $booking->date }}</td>
                        <td class="p-3">{{ $booking->time }}</td>
                        <td class="p-3">{{ $booking->duration }} min</td>
                        <td class="p-3 capitalize">{{ $booking->status }}</td>
                        <td class="p-3 flex gap-2">
                            @can('update', $booking)
                                <a href="{{ route('bookings.edit', $booking) }}" class="text-blue-600 hover:underline">Edit</a>
                            @endcan
                            @can('delete', $booking)
                                <form action="{{ route('bookings.destroy', $booking) }}" method="POST"
                                      onsubmit="return confirm('Delete this booking?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 p-4">No bookings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FullCalendar Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                height: "auto",
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                select: function (info) {
                    Swal.fire({
                        title: 'New Booking',
                        html:
                            `<input id="swal-title" class="swal2-input" placeholder="Title">
                             <input id="swal-time" type="time" class="swal2-input" placeholder="Time">
                             <input id="swal-duration" type="number" class="swal2-input" placeholder="Duration (minutes)">
                             <textarea id="swal-description" class="swal2-textarea" placeholder="Description"></textarea>`,
                        showCancelButton: true,
                        preConfirm: () => {
                            const title = document.getElementById('swal-title').value;
                            const time = document.getElementById('swal-time').value;
                            const duration = document.getElementById('swal-duration').value;
                            const description = document.getElementById('swal-description').value;

                            if (!title || !time || !duration) {
                                Swal.showValidationMessage('Please fill in all required fields.');
                                return false;
                            }

                            return { title, time, duration, description };
                        }
                    }).then(result => {
                        if (result.isConfirmed && result.value) {
                            fetch("{{ route('bookings.store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "X-Requested-With": "XMLHttpRequest"
                                },
                                body: JSON.stringify({
                                    title: result.value.title,
                                    date: info.startStr,
                                    time: result.value.time,
                                    duration: result.value.duration,
                                    description: result.value.description
                                })
                            })
                            .then(async res => {
                                if (!res.ok) {
                                    const err = await res.json();
                                    throw new Error(err.message || 'Failed to create booking.');
                                }
                                return res.json();
                            })
                            .then(data => {
                                Swal.fire("Success", "Booking created!", "success");
                                calendar.addEvent({
                                    title: data.booking.title,
                                    start: `${data.booking.date}T${data.booking.time}`,
                                    url: `/bookings/${data.booking.id}/edit`
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                Swal.fire("Error", err.message || "Something went wrong", "error");
                            });
                        }
                    });
                },
                events: [
                    @foreach ($bookings as $booking)
                        {
                            title: '{{ $booking->title }}',
                            start: '{{ $booking->date }}T{{ $booking->time }}',
                            url: '{{ route('bookings.edit', $booking) }}'
                        },
                    @endforeach
                ]
            });

            calendar.render();
        });
    </script>
</x-app-layout>