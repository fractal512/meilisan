<?php

declare(strict_types=1);

namespace Fractal512\Meilisan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Laravel\Scout\EngineManager;

class FilterableAttributesCommand extends Command
{
    protected $signature = 'meilisearch:filterable
            {action : Action on filterable attributes (get, set, reset)}
            {index : Name of index in Meilisearch database}
            {attributes? : Comma-separated list of filterable attributes to set}';

    protected $description = 'Manage filterable attributes for the Meilisearch index';

    public function handle(EngineManager $manager): void
    {
        $engine = $manager->engine();

        $index = $this->argument('index');

        try {
            switch ($this->argument('action')) {
                case 'get':
                    $result = $engine->index($index)->getFilterableAttributes();
                    if (is_array($result) && !empty($result)) {
                        $this->table(
                            ["Filterable attributes for \"$index\" index"],
                            array_map(fn(string $item) => [$item], $result)
                        );
                    } else {
                        $this->comment('No filterable attributes found.');
                    }
                    return;
                case 'set':
                    if (!empty($this->argument('attributes'))) {
                        $result = $engine->index($index)->updateFilterableAttributes(explode(',', $this->argument('attributes')));
                        if (is_array($result) && !empty($result)) {
                            $table = [];
                            foreach ($result as $key => $value) {
                                if ($key == 'enqueuedAt') {
                                    $value = Carbon::parse($value)->format('Y-m-d H:i:s');
                                }
                                $table[] = [$key, $value];
                            }
                            $this->info("Setting up filterable attributes for $index index...");
                            $this->table([], $table);
                        } else {
                            print_r($result);
                        }
                    } else {
                        $this->warn('No attributes provided.');
                    }
                    return;
                case 'reset':
                    $result = $engine->index($index)->resetFilterableAttributes();
                    if (is_array($result) && !empty($result)) {
                        $table = [];
                        foreach ($result as $key => $value) {
                            if ($key == 'enqueuedAt') {
                                $value = Carbon::parse($value)->format('Y-m-d H:i:s');
                            }
                            $table[] = [$key, $value];
                        }
                        $this->info("Resetting filterable attributes for $index index...");
                        $this->table([], $table);
                    } else {
                        print_r($result);
                    }
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
