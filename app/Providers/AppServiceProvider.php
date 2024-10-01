<?php

namespace App\Providers;

use Filament\Forms\Components\Field;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\Configurable;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Filters\BaseFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\Column;
use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Model;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    /*
     * If you would like add different configurations to the components you can separate them into different methods.
     * This way you can keep the code clean and organized.
     */

    protected function translatableComponents(): void
    {
        foreach ([Field::class, BaseFilter::class, Placeholder::class, Column::class, Entry::class] as $component) {
            /* @var Configurable $component */
            $component::configureUsing(function (Component $translatable): void {
                /** @phpstan-ignore method.notFound */
                $translatable->translateLabel();
            });
        }
    }

    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());

        $this->translatableComponents();
    }
}
