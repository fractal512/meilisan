<?php

declare(strict_types=1);

namespace Fractal512\Meilisan\Console;

use Illuminate\Console\Command;
use MeiliSearch\Client;

class SortableAttributesCommand extends Command
{
    protected $signature = 'meilisearch:sortable {action} {index} {attributes?}';

    protected $description = 'Manage sortable attributes for meilisearch index';

    public function handle(): void
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = config('scout.prefix') . $this->argument('index');

        $result = '';

        switch ($this->argument('action')) {
            case 'get':
                $result = $client->index($index)->getSortableAttributes();
                break;
            case 'set':
                if (!empty($this->argument('attributes'))) {
                    $result = $client->index($index)->updateSortableAttributes(explode(',', $this->argument('attributes')));
                } else {
                    $result = 'No attributes provided' . PHP_EOL;
                }
                break;
            case 'reset':
                $result = $client->index($index)->resetSortableAttributes();
        }

        print_r($result);
    }
}
