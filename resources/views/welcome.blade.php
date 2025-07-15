<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Booking System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans antialiased text-gray-800">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-2xl bg-white shadow-[0_8px_30px_rgba(0,0,0,0.25)] rounded-lg p-8 text-center">
            <h1 class="text-3xl font-bold mb-4 text-blue-700">Welcome to the Booking System</h1>
            <p class="text-gray-600 mb-6">Plan, schedule, and manage your bookings with ease.</p>

            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded border transition">
                        Register
                    </a>
                @endauth
            </div>

            <div class="mt-10 text-sm text-gray-400">
                &copy; {{ now()->year }} Booking System. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>
