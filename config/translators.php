<?php

return [
    'prompts' => [
        'post' => <<<'PROMPT'
Translate the following {from} news content to {to}. Preserve all HTML tags exactly as-is. Do not wrap the response in markdown code blocks. Return a JSON object with keys: title_{target}, summary_{target}, body_{target}, meta_title_{target}, meta_description_{target}.

---
title_{from}: {title}
summary_{from}: {summary}
body_{from}: {body}
meta_title_{from}: {meta_title}
meta_description_{from}: {meta_description}
---
PROMPT,

        'text' => <<<'PROMPT'
Translate the following {from} text to {to}. Return only the translated text, no explanations.

{text}
PROMPT,
    ],
];
