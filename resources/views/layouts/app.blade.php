<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Prep — @yield('title', 'Inicio')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { laravel: '#FF2D20' } } }
        }
    </script>
    <style>
        pre { overflow-x: auto; }
        .topic-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">

    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-6">
            <a href="{{ route('home') }}" class="text-laravel font-bold text-xl tracking-tight">
                🔥 Laravel Prep
            </a>
            <div class="flex gap-4 ml-auto text-sm">
                <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition">Temas</a>
                <a href="{{ route('quiz.index') }}" class="text-gray-300 hover:text-white transition">Quiz</a>
                <a href="{{ route('quiz.interview') }}" class="text-gray-300 hover:text-white transition">Entrevista</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        @yield('content')
    </main>

</body>
</html>