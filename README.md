## Meilisearch methods wrapper for Laravel Artisan

This wrapper helps to manage [Meilisearch](https://www.meilisearch.com/) functionality via console using Laravel Artisan.

### Table of Contents
- [Installation](#installation)
- [Usage](#usage)
  - [Filterable Attributes](#filterable-attributes)
  - [Ranking Rules](#ranking-rules)
  - [Sortable Attributes](#sortable-attributes)

### Installation
Install the package using composer:
```
composer require fractal512/meilisan
```

### Usage
Use `artisan meilisearch` command to manage Meilisearch indexes, configuration, etc.

#### Filterable Attributes
To manage [filterable attributes](https://www.meilisearch.com/docs/learn/fine_tuning_results/filtering) for the Meilisearch index use command:
```
php artisan meilisearch:filterable <action> <index> [<attributes>]
```
Arguments:
- action - action on filterable attributes (get, set, reset)
- index - name of index in Meilisearch database
- attributes - comma-separated list of filterable attributes to set

#### Ranking Rules
To manage [ranking rules](https://www.meilisearch.com/docs/learn/core_concepts/relevancy) for the Meilisearch index use command:
```
php artisan meilisearch:ranking <action> <index> [<attributes>]
```
Arguments:
- action - action on ranking rules (get, set, reset)
- index - name of index in Meilisearch database
- attributes - comma-separated list of ranking rules to set

#### Sortable Attributes
To manage [sortable attributes](https://www.meilisearch.com/docs/learn/fine_tuning_results/sorting) for the Meilisearch index use command:
```
php artisan meilisearch:sortable <action> <index> [<attributes>]
```
Arguments:
- action - action on sortable attributes (get, set, reset)
- index - name of index in Meilisearch database
- attributes - comma-separated list of sortable attributes to set
