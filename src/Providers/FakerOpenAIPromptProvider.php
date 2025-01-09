<?php

namespace JPCaparas\FakerOpenAI\Providers;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\TransporterException;
use OpenAI\Laravel\Facades\OpenAI;

class FakerOpenAIPromptProvider extends Base
{
    public function __construct()
    {
        //
    }

    /**
     * Prompt the AI to generate a response based on the given term.
     *
     * @example $faker->promptAI('name')
     * @example $faker->promptAI('movieReview') // This will return a movie review
     * @example $faker->promptAI('movieDescription') // This will return a movie description
     * @example $faker->promptAI('name', 'John Doe') // This defaults to 'John Doe' if an error occurs
     * @example $faker->promptAI('name', fn() => 'John Doe') // This defaults to 'John Doe' if an error occurs
     * @example $faker->promptAI('randomNumber', fn() => 99) // This defaults to 99 if an error occurs (but why would you do this?)
     */
    public function promptAI(string $term, null|string|callable $fallback = null, $throwOnError = false): mixed
    {
        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Return only the output value of \Faker\Factory::create()->'.Str::camel($term).'() without any explanation or formatting. A simple string, please.',
                    ],
                ],
            ]);

            return $result->choices[0]->message->content;
        } catch (ErrorException|TransporterException $e) {
            if ($throwOnError) {
                throw $e;
            }

            Log::error($e->getMessage());

            if (is_string($fallback)) {
                return $fallback;
            }

            if (is_callable($fallback)) {
                return $fallback();
            }

            return null;
        }
    }
}
