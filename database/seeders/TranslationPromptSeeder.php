<?php

namespace Database\Seeders;

use App\Models\TranslationPrompt;
use Illuminate\Database\Seeder;

class TranslationPromptSeeder extends Seeder
{
    public function run(): void
    {
        $prompts = [
            [
                'name' => 'post',
                'prompt_template' => <<<'PROMPT'
Translate the following {from} news content to {to}. Preserve all HTML tags exactly as-is. Do not wrap the response in markdown code blocks. Return a JSON object with keys: title_{target}, summary_{target}, body_{target}, meta_title_{target}, meta_description_{target}.

Rules:
- Maintain journalistic tone and formal register
- Keep proper nouns, dates, and numbers unchanged
- Preserve all HTML tags, attributes, and structure
- Ensure Bengali honorifics are culturally appropriate in English
- Return ONLY valid JSON, no other text

---
title_{from}: {title}
summary_{from}: {summary}
body_{from}: {body}
meta_title_{from}: {meta_title}
meta_description_{from}: {meta_description}
---
PROMPT,
                'description' => 'Used for translating full news posts (title, body, summary, meta)',
                'is_active' => true,
            ],
            [
                'name' => 'text',
                'prompt_template' => <<<'PROMPT'
Translate the following {from} text to {to}. Return only the translated text, no explanations, no markdown formatting.

{text}
PROMPT,
                'description' => 'Used for translating short text snippets',
                'is_active' => true,
            ],
            [
                'name' => 'category',
                'prompt_template' => <<<'PROMPT'
Translate the following {from} category name and description to {to}. Return a JSON object with keys: name_{target}, description_{target}.

---
name_{from}: {title}
description_{from}: {summary}
---
PROMPT,
                'description' => 'Used for translating category names and descriptions',
                'is_active' => true,
            ],
        ];

        foreach ($prompts as $prompt) {
            TranslationPrompt::firstOrCreate(
                ['name' => $prompt['name']],
                $prompt,
            );
        }
    }
}
