<?php

declare(strict_types=1);

namespace Fractal512\Meilisan\Console;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Meilisearch\Contracts\IndexesQuery;
use Meilisearch\Endpoints\Indexes;
use Meilisearch\Http\Client;

class IndexCommand extends Command
{
    protected $signature = 'meilisearch:index
            {action : Action on index (get, set, delete)}
            {index? : Name of the index in the Meilisearch database}
            {--o|offset= : Number of indexes to skip}
            {--l|limit= : Number of indexes to return}
            {--k|key= : Name of the primary key}';

    protected $description = 'Manage indexes in the Meilisearch database';

    public function handle(EngineManager $manager): void
    {
        /** @var Client $engine */
        $engine = $manager->engine();

        $index = $this->argument('index');

        switch ($this->argument('action')) {
            case 'get':
                if (!empty($index)) {
                    $result = $engine->index($index)->fetchRawInfo();
                    if (!empty($result)) {
                        $this->info("\"$index\" index details:");
                        $this->table(
                            [],
                            array_map(fn(string $value, string $key) => [
                                $key,
                                $value,
                            ], $result, array_keys($result))
                        );
                    }
                    break;
                }

                $indexesQuery = new IndexesQuery();

                if ($this->option('offset')) {
                    $indexesQuery->setOffset((int) $this->option('offset'));
                }

                if ($this->option('limit')) {
                    $indexesQuery->setLimit((int) $this->option('limit'));
                }

                $result = $engine->getIndexes($indexesQuery);
                $this->info("Total indexes: {$result->getTotal()}. Limit: {$result->getLimit()}. Offset: {$result->getOffset()}.");
                $data = $result->getResults();
                if (!empty($data)) {
                    $this->table(
                        ['#', 'UID', 'Primary Key', 'Created at', 'Updated at'],
                        array_map(fn(Indexes $item, int $index) => [
                            $index + 1 + ($this->option('offset') ?? 0),
                            $item->getUid(),
                            $item->getPrimaryKey(),
                            $item->getCreatedAt()->format('Y-m-d H:i:s'),
                            $item->getUpdatedAt()->format('Y-m-d H:i:s'),
                        ], $data, array_keys($data))
                    );
                } else {
                    $this->comment('No indexes found.');
                }
                break;
            case 'set':
                if (empty($this->argument('index'))) {
                    $this->warn('No index provided.');
                    break;
                }

                $options = [];

                if ($this->option('key')) {
                    $options = ['primaryKey' => $this->option('key')];
                }

                $engine->createIndex($this->argument('index'), $options);

                $this->info('Index ' . $this->argument('index') . ' created successfully.');
                break;
            case 'delete':
                if (empty($this->argument('index'))) {
                    $this->warn('No index provided.');
                    break;
                }

                $engine->deleteIndex($this->argument('index'));

                $this->info('Index ' . $this->argument('index') . ' deleted.');
        }
    }
}
