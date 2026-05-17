<?php

namespace App\Http\Controllers\Admin\Translation;

use App\Http\Controllers\Controller;
use App\Models\TranslationPrompt;
use Illuminate\Http\Request;

class TranslationPromptController extends Controller
{
    public function index()
    {
        $prompts = TranslationPrompt::orderBy('name')->get();

        return view('admin.translation-prompts.index', compact('prompts'));
    }

    public function edit(TranslationPrompt $prompt)
    {
        return view('admin.translation-prompts.edit', compact('prompt'));
    }

    public function update(Request $request, TranslationPrompt $prompt)
    {
        $data = $request->validate([
            'prompt_template' => 'required|string',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] ??= true;

        $prompt->update($data);

        return redirect()->route('admin.translation.prompts.index')
            ->with('success', 'Translation prompt updated.');
    }
}
