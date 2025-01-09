<?php

namespace JPCaparas\FakerOpenAI;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\ServiceProvider;
use JPCaparas\FakerOpenAI\Providers\FakerOpenAIPromptProvider;

class FakerOpenAIServiceProvider extends ServiceProvider
{
    /**
     * The array of resolved Faker instances.
     *
     * @var array
     */
    protected static $fakers = [];

    public function register()
    {
        $this->registerFakerGenerator();
    }

    public function boot()
    {
        //
    }

    /**
     * Overridden the default Faker generator to include custom providers.
     *
     * @see \Illuminate\Database\DatabaseServiceProvider::registerFakerGenerator()
     */
    protected function registerFakerGenerator(): void
    {
        $this->app->singleton(FakerGenerator::class, function ($app, $parameters) {
            $locale = $parameters['locale'] ?? $app['config']->get('app.faker_locale', 'en_US');

            if (! isset(static::$fakers[$locale])) {
                $fakerFactory = FakerFactory::create($locale);

                $fakerFactory->addProvider(new FakerOpenAIPromptProvider);

                static::$fakers[$locale] = $fakerFactory;
            }

            static::$fakers[$locale]->unique(true);

            return static::$fakers[$locale];
        });

        // For usage on the `faker()` helper, which requires the locale to be set.
        $fakerLocale = $this->app['config']->get('app.faker_locale', 'en_US');
        $this->app->alias(FakerGenerator::class, FakerGenerator::class.':'.$fakerLocale);
    }
}
