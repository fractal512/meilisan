<?php

declare(strict_types=1);

namespace Fractal512\Meilisan;

use Fractal512\Meilisan\Console\FilterableAttributesCommand;
use Fractal512\Meilisan\Console\IndexCommand;
use Fractal512\Meilisan\Console\RankingRulesCommand;
use Fractal512\Meilisan\Console\SortableAttributesCommand;
use Fractal512\Meilisan\Console\StopWordsCommand;
use Illuminate\Support\ServiceProvider;

class MeilisanServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterableAttributesCommand::class,
                IndexCommand::class,
                RankingRulesCommand::class,
                SortableAttributesCommand::class,
                StopWordsCommand::class,
            ]);
        }
    }
}
