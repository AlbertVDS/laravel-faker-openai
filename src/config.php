<?php

return [
    'openai' => [
        'model' => env('OPENAI_MODEL', 'text-davinci-003'),
        'default_prompt' => env('OPENAI_DEFAULT_PROMPT', 'Generate fake data'),
    ],
];
