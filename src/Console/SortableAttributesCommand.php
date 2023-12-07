<?php

declare(strict_types=1);

namespace Fractal512\Meilisan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Laravel\Scout\EngineManager;

class SortableAttributesCommand extends Command
{
    protected $signature = 'meilisearch:sortable
            {action : Action on sortable attributes (get, set, reset)}
            {index : Name of index in Meilisearch database}
            {attributes? : Comma-separated list of sortable attributes to set}';

    protected $description = 'Manage sortable attributes for the Meilisearch index';

    public function handle(EngineManager $manager): void
    {
        $engine = $manager->engine();

        $index = $this->argument('index');

        try {
            switch ($this->argument('action')) {
                case 'get':
                    $result = $engine->index($index)->getSortableAttributes();
                    if (is_array($result) && !empty($result)) {
                        $this->table(
                            ["Sortable attributes for \"$index\" index"],
                            array_map(fn(string $item) => [$item], $result)
                        );
                    } else {
                        $this->comment('No sortable attributes found.');
                    }
                    return;
                case 'set':
                    if (!empty($this->argument('attributes'))) {
                        $result = $engine->index($index)->updateSortableAttributes(explode(',', $this->argument('attributes')));
                        if (is_array($result) && !empty($result)) {
                            $table = [];
                            foreach ($result as $key => $value) {
                                if ($key == 'enqueuedAt') {
                                    $value = Carbon::parse($value)->format('Y-m-d H:i:s');
                                }
                                $table[] = [$key, $value];
                            }
                            $this->info("Setting up sortable attributes for $index index...");
                            $this->table([], $table);
                        } else {
                            print_r($result);
                        }
                    } else {
                        $this->warn('No attributes provided.');
                    }
                    return;
                case 'reset':
                    $result = $engine->index($index)->resetSortableAttributes();
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
