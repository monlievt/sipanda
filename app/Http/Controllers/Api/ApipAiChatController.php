<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApipAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApipAiChatController extends Controller
{
    /**
     * Endpoint API untuk Chatbot Asisten Penasihat Virtual APIP
     */
    public function ask(Request $request, ApipAiService $aiService): JsonResponse
    {
        $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'kategori' => ['nullable', 'string', 'max:50'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.max'      => 'Pertanyaan maksimal 500 karakter.',
        ]);

        $question = $request->input('question');
        $kategori = $request->input('kategori');

        $result = $aiService->ask($question, $kategori);

        return response()->json($result);
    }
}
