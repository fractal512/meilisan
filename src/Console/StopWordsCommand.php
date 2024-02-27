<?php

declare(strict_types=1);

namespace Fractal512\Meilisan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Laravel\Scout\EngineManager;

class StopWordsCommand extends Command
{
    protected $signature = 'meilisearch:stopwords
            {action : Action on stop words (get, set, reset)}
            {index : Name of index in Meilisearch database}
            {stopwords? : Comma-separated list of stop words to set}';

    protected $description = 'Manage stop words for the Meilisearch index';

    public function handle(EngineManager $manager): void
    {
        $engine = $manager->engine();

        $index = $this->argument('index');

        switch ($this->argument('action')) {
            case 'get':
                $result = $engine->index($index)->getStopWords();
                if (is_array($result) && !empty($result)) {
                    $this->table(
                        ["Stop words for \"$index\" index"],
                        array_map(fn(string $item) => [$item], $result)
                    );
                } else {
                    $this->comment('No stop words found.');
                }
                return;
            case 'set':
                if (!empty($this->argument('attributes'))) {
                    $result = $engine->index($index)->updateStopWords(explode(',', $this->argument('attributes')));
                    if (is_array($result) && !empty($result)) {
                        $table = [];
                        foreach ($result as $key => $value) {
                            if ($key == 'enqueuedAt') {
                                $value = Carbon::parse($value)->format('Y-m-d H:i:s');
                            }
                            $table[] = [$key, $value];
                        }
                        $this->info("Setting up stop words for \"$index\" index...");
                        $this->table([], $table);
                    } else {
                        print_r($result);
                    }
                } else {
                    $this->warn('No stop words provided.');
                }
                return;
            case 'reset':
                $result = $engine->index($index)->resetStopWords();
                if (is_array($result) && !empty($result)) {
                    $table = [];
                    foreach ($result as $key => $value) {
                        if ($key == 'enqueuedAt') {
                            $value = Carbon::parse($value)->format('Y-m-d H:i:s');
                        }
                        $table[] = [$key, $value];
                    }
                    $this->info("Resetting stop words for $index index...");
                    $this->table([], $table);
                } else {
                    print_r($result);
                }
        }
    }
}
