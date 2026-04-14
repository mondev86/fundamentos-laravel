@extends('layouts.app')

@section('title', 'Quiz General')

@section('content')
<div class="max-w-2xl mx-auto" x-data="globalQuiz({{ json_encode($questions) }})">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">🎯 Quiz General</h1>
        <p class="text-gray-400">{{ count($questions) }} preguntas de todos los temas · en orden aleatorio</p>
    </div>

    <div class="bg-gray-800 rounded-full h-2 mb-6">
        <div class="bg-laravel h-2 rounded-full transition-all duration-300"
             :style="`width: ${((current) / questions.length) * 100}%`"></div>
    </div>

    <div x-show="!finished" class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs text-gray-500 bg-gray-800 px-3 py-1 rounded-full" x-text="questions[current].topic"></span>
            <span class="text-gray-400 text-sm" x-text="`${current + 1} / ${questions.length}`"></span>
        </div>

        <p class="text-white text-lg font-medium mb-6" x-text="questions[current].q"></p>

        <div class="space-y-3">
            <template x-for="(opt, i) in questions[current].options" :key="i">
                <button @click="answer(i)" :disabled="answered"
                        :class="{
                            'border-green-500 bg-green-900/30 text-green-300': answered && i === questions[current].answer,
                            'border-red-500 bg-red-900/30 text-red-300': answered && selected === i && i !== questions[current].answer,
                            'border-gray-700 hover:border-laravel text-gray-200': !answered
                        }"
                        class="w-full text-left border rounded-xl px-5 py-3 transition">
                    <span x-text="opt"></span>
                </button>
            </template>
        </div>

        <div x-show="answered" class="mt-4 flex justify-end">
            <button @click="next()"
                    class="bg-laravel hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                <span x-text="current < questions.length - 1 ? 'Siguiente →' : 'Ver resultados'"></span>
            </button>
        </div>
    </div>

    <div x-show="finished" class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
        <p class="text-6xl font-bold text-white mb-2" x-text="score + '/' + questions.length"></p>
        <p class="text-2xl font-semibold mb-1" :class="score >= questions.length * 0.8 ? 'text-green-400' : score >= questions.length * 0.5 ? 'text-amber-400' : 'text-red-400'"
           x-text="score >= questions.length * 0.8 ? '¡Listo para la entrevista! 🚀' : score >= questions.length * 0.5 ? 'Buen avance, sigue repasando 💪' : 'Necesitas repasar más 📖'">
        </p>
        <p class="text-gray-400 mb-6" x-text="`Porcentaje: ${Math.round((score/questions.length)*100)}%`"></p>
        <div class="flex justify-center gap-4">
            <button @click="reset()" class="bg-laravel hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                Repetir quiz
            </button>
            <a href="{{ route('quiz.interview') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                Modo Entrevista →
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function globalQuiz(questions) {
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
            } else { this.finished = true; }
        },
        reset() { this.current = 0; this.selected = null; this.answered = false; this.finished = false; this.score = 0; }
    }
}
</script>
@endsection