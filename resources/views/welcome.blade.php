<!DOCTYPE html>
     <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
     <head>
         <meta charset="utf-8">
         <meta name="viewport" content="width=device-width, initial-scale=1">
         <title>Laravel</title>
         @vite(['resources/css/app.css', 'resources/js/app.js'])
     </head>
     <body class="antialiased">
         <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
             <h1 class="text-3xl font-bold mb-4">Welcome to Laravel!</h1>
             @if (Route::has('login'))
                 <div class="space-x-4">
                     @auth
                         <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
                         <form method="POST" action="{{ route('logout') }}" class="inline">
                             @csrf
                             <button type="submit" class="text-red-600 hover:underline">Logout</button>
                         </form>
                     @else
                         <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Log in</a>
                         @if (Route::has('register'))
                             <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
                         @endif
                     @endauth
                 </div>
             @endif
         </div>
     </body>
     </html>