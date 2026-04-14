@extends('layouts.app')

@section('title', 'Simulador de Entrevista')

@section('content')
<div class="max-w-2xl mx-auto" x-data="{ open: null }">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">💬 Simulador de Entrevista</h1>
        <p class="text-gray-400">Responde mentalmente cada pregunta antes de ver la respuesta</p>
    </div>

    <div class="space-y-4">
        @foreach($questions as $i => $qa)
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden"
             x-data="{ show: false }">
            <button @click="show = !show"
                    class="w-full text-left p-5 flex justify-between items-start gap-4 hover:bg-gray-800 transition">
                <div class="flex gap-3 items-start">
                    <span class="text-laravel font-bold text-lg shrink-0">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <p class="text-white font-medium leading-snug">{{ $qa['q'] }}</p>
                </div>
                <span class="text-gray-500 shrink-0" x-text="show ? '▲' : '▼'"></span>
            </button>
            <div x-show="show" x-transition class="px-5 pb-5">
                <div class="border-t border-gray-700 pt-4">
                    <p class="text-xs text-green-400 font-semibold uppercase tracking-wider mb-2">Respuesta sugerida</p>
                    <p class="text-gray-300 leading-relaxed">{{ $qa['a'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('quiz.index') }}" class="bg-laravel hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-lg transition">
            🎯 Ir al Quiz →
        </a>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection