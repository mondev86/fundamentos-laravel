@extends('layouts.app')

@section('title', 'Fundamentos Laravel')

@section('content')
<div class="text-center mb-12">
    <h1 class="text-4xl font-bold text-white mb-3">
        Prepárate para tu <span class="text-laravel">prueba técnica</span> de Laravel
    </h1>
    <p class="text-gray-400 text-lg">Estudia los temas clave · Practica con quiz · Simula la entrevista</p>

    <div class="flex justify-center gap-4 mt-6">
        <a href="{{ route('quiz.index') }}"
           class="bg-laravel hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-lg transition">
            🎯 Empezar Quiz
        </a>
        <a href="{{ route('quiz.interview') }}"
           class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition">
            💬 Modo Entrevista
        </a>
    </div>
</div>

<div class="bg-gray-900 rounded-xl p-5 mb-8 border border-gray-800">
    <p class="text-sm text-gray-400 mb-2">Temas disponibles</p>
    <div class="flex flex-wrap gap-2">
        @foreach($topics as $t)
            <a href="{{ route('topic.show', $t['slug']) }}"
               class="text-xs bg-gray-800 hover:bg-laravel text-gray-300 hover:text-white px-3 py-1 rounded-full transition">
                {{ $t['icon'] }} {{ $t['title'] }}
            </a>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($topics as $topic)
    <a href="{{ route('topic.show', $topic['slug']) }}"
       class="topic-card bg-gray-900 border border-gray-800 hover:border-laravel rounded-xl p-5 transition duration-200 block">
        <div class="text-3xl mb-3">{{ $topic['icon'] }}</div>
        <h2 class="text-lg font-semibold text-white mb-1">{{ $topic['title'] }}</h2>
        <p class="text-gray-500 text-sm">
            {{ count($topic['sections']) }} sección{{ count($topic['sections']) > 1 ? 'es' : '' }} ·
            {{ count($topic['quiz']) }} preguntas de quiz
        </p>
        <div class="mt-3 text-laravel text-sm font-medium">Estudiar →</div>
    </a>
    @endforeach
</div>
@endsection