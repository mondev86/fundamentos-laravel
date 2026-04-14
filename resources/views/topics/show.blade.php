@extends('layouts.app')

@section('title', $topic['title'])

@section('content')
<div class="flex gap-8">

    <aside class="hidden lg:block w-56 shrink-0">
        <div class="sticky top-24 bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Todos los temas</p>
            @foreach($topics as $t)
            <a href="{{ route('topic.show', $t['slug']) }}"
               class="block py-1.5 px-2 rounded text-sm transition
               {{ $t['slug'] === $topic['slug'] ? 'text-laravel font-semibold bg-red-950' : 'text-gray-400 hover:text-white' }}">
                {{ $t['icon'] }} {{ $t['title'] }}
            </a>
            @endforeach
        </div>
    </aside>

    <div class="flex-1 min-w-0">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="text-gray-500 text-sm hover:text-gray-300">← Inicio</a>
            <h1 class="text-3xl font-bold text-white mt-2">
                {{ $topic['icon'] }} {{ $topic['title'] }}
            </h1>
        </div>

        @foreach($topic['sections'] as $i => $section)
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-5">
            <h2 class="text-xl font-semibold text-white mb-3">{{ $section['title'] }}</h2>
            <p class="text-gray-300 mb-4 leading-relaxed">{{ $section['content'] }}</p>

            <div class="bg-gray-950 border border-gray-700 rounded-lg p-4 mb-4">
                <p class="text-xs text-gray-500 mb-2 font-mono">PHP</p>
                <pre class="text-sm text-green-300 font-mono leading-relaxed">{{ $section['code'] }}</pre>
            </div>

            <div class="bg-amber-950 border border-amber-700 rounded-lg p-4">
                <p class="text-amber-400 text-xs font-semibold uppercase tracking-wider mb-1">
                    💡 Tip para la entrevista
                </p>
                <p class="text-amber-200 text-sm leading-relaxed">{{ $section['tip'] }}</p>
            </div>
        </div>
        @endforeach

        <div class="bg-gray-900 border border-laravel rounded-xl p-6 mb-6" x-data="quiz({{ json_encode($topic['quiz']) }})">
            <h2 class="text-xl font-semibold text-white mb-4">🎯 Quiz de este tema</h2>

            <div x-show="!finished">
                <p class="text-gray-400 text-sm mb-4">
                    Pregunta <span x-text="current + 1"></span> de {{ count($topic['quiz']) }}
                </p>
                <p class="text-white font-medium mb-4" x-text="questions[current].q"></p>

                <div class="space-y-2">
                    <template x-for="(opt, i) in questions[current].options" :key="i">
                        <button @click="answer(i)"
                                :disabled="answered"
                                :class="{
                                    'border-green-500 bg-green-900/40 text-green-300': answered && i === questions[current].answer,
                                    'border-red-500 bg-red-900/40 text-red-300': answered && selected === i && i !== questions[current].answer,
                                    'border-gray-700 hover:border-gray-500': !answered
                                }"
                                class="w-full text-left border rounded-lg px-4 py-2 text-sm transition">
                            <span x-text="opt"></span>
                        </button>
                    </template>
                </div>

                <button x-show="answered" @click="next()"
                        class="mt-4 bg-laravel hover:bg-red-600 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">
                    <span x-text="current < questions.length - 1 ? 'Siguiente →' : 'Ver resultado'"></span>
                </button>
            </div>

            <div x-show="finished" class="text-center py-4">
                <p class="text-4xl font-bold mb-2" x-text="score + '/' + questions.length"></p>
                <p class="text-gray-400 mb-4" x-text="score === questions.length ? '¡Perfecto! 🎉' : score >= questions.length / 2 ? 'Bien, sigue repasando 💪' : 'Repasa este tema 📖'"></p>
                <button @click="reset()" class="bg-gray-700 hover:bg-gray-600 text-white px-5 py-2 rounded-lg text-sm transition">
                    Repetir quiz
                </button>
            </div>
        </div>

        <div class="flex justify-between gap-4">
            @if($prev)
            <a href="{{ route('topic.show', $prev['slug']) }}"
               class="flex-1 bg-gray-800 hover:bg-gray-700 rounded-xl p-4 text-left transition">
                <p class="text-gray-500 text-xs mb-1">← Anterior</p>
                <p class="text-white font-medium">{{ $prev['icon'] }} {{ $prev['title'] }}</p>
            </a>
            @else <div class="flex-1"></div>
            @endif

            @if($next)
            <a href="{{ route('topic.show', $next['slug']) }}"
               class="flex-1 bg-gray-800 hover:bg-gray-700 rounded-xl p-4 text-right transition">
                <p class="text-gray-500 text-xs mb-1">Siguiente →</p>
                <p class="text-white font-medium">{{ $next['icon'] }} {{ $next['title'] }}</p>
            </a>
            @else <div class="flex-1"></div>
            @endif
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function quiz(questions) {
    return {
        questions, current: 0, selected: null,
        answered: false, finished: false, score: 0,
        answer(i) {
            if (this.answered) return;
            this.selected = i;
            this.answered = true;
            if (i === this.questions[this.current].answer) this.score++;
        },
        next() {
            if (this.current < this.questions.length - 1) {
                this.current++; this.answered = false; this.selected = null;
            } else {
                this.finished = true;
            }
        },
        reset() { this.current = 0; this.selected = null; this.answered = false; this.finished = false; this.score = 0; }
    }
}
</script>
@endsection