<?php

declare(strict_types=1);

namespace JPCaparas\FakerOpenAI\Tests;

use Faker\Generator as FakerGenerator;
use JPCaparas\FakerOpenAI\FakerOpenAIServiceProvider;
use JPCaparas\FakerOpenAI\Providers\FakerOpenAIPromptProvider;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Laravel\ServiceProvider as OpenAIServiceProvider;
use OpenAI\Responses\Chat\CreateResponse;
use Orchestra\Testbench\TestCase;

class FakerOpenAITest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OpenAIServiceProvider::class,
            FakerOpenAIServiceProvider::class,
        ];
    }

    public function test_it_registers_faker_generator_singleton()
    {
        $faker = $this->app->make(FakerGenerator::class);

        $this->assertInstanceOf(FakerGenerator::class, $faker);
    }

    public function test_it_registers_ai_prompt_provider()
    {
        $faker = $this->app->make(FakerGenerator::class);

        $providers = array_map(fn($provider) => get_class($provider), $faker->getProviders());

        $this->assertContains(FakerOpenAIPromptProvider::class, $providers);
    }

    public function test_prompt_ai_returns_generated_content()
    {
        OpenAI::fake([
            CreateResponse::fake(),
        ]);

        $faker = $this->app->make(FakerGenerator::class);
        $response = $faker->promptAI('test prompt');
        $this->assertNotEmpty($response, 'No response from OpenAI API');
    }
}
