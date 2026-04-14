<?php

namespace App\Http\Controllers;

use App\Data\LaravelData;

class TopicController extends Controller
{
    public function show(string $slug)
    {
        $topics = LaravelData::topics();
        $topic = collect($topics)->firstWhere('slug', $slug);

        abort_if(! $topic, 404);

        $currentIndex = collect($topics)->search(fn ($t) => $t['slug'] === $slug);
        $prev = $topics[$currentIndex - 1] ?? null;
        $next = $topics[$currentIndex + 1] ?? null;

        return view('topics.show', compact('topic', 'topics', 'prev', 'next'));
    }
}
