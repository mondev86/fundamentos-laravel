<?php

namespace App\Http\Controllers;

use App\Data\LaravelData;

class QuizController extends Controller
{
    public function index()
    {
        $allQuestions = collect(LaravelData::topics())
            ->flatMap(fn ($t) => collect($t['quiz'])->map(fn ($q) => array_merge($q, ['topic' => $t['title']])))
            ->shuffle()
            ->values()
            ->all();

        return view('quiz.index', ['questions' => $allQuestions]);
    }

    public function interview()
    {
        return view('quiz.interview', [
            'questions' => LaravelData::interviewQuestions(),
        ]);
    }
}
