<?php

namespace App\Search;

use Elasticsearch\Client;
use Illuminate\Support\Facades\Artisan;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class ElasticSearchEngine extends Engine
{
    protected $client;
    public function __construct(Client $client) {
        $this -> client = $client;
    }

    /**
     * @param Builder $builder
     * @return array|callable|mixed
     */
    public function search(Builder $builder) {
        return $this->performSearch($builder, [
            'from' => 0,
            'size' => 10
        ]);
    }

    public function update($models) {
        $models->each(function ($model) {
            $params = $this->getRequestBody($model, [
                'id' => $model->id,
                'body' => $model->toSearchableArray(),
            ]);
            $this->client->index($params);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $models
     */
    public function delete($models) {
        $models->each(function ($model) {
            $params = $this->getRequestBody($model, [
                'id' => $model->id,
            ]);
            $this->client->delete($params);
        });
    }

    public function paginate(Builder $builder, $perPage, $page) {
        return $this->performSearch($builder, [
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
        ]);
    }

    public function mapIds($results) {
        return collect(array_get($results, 'hits.hits'))->pluck('_id')->values();
    }

    public function map(Builder $builder, $results, $model) {
        if (count($hits = array_get($results, 'hits.hits')) === 0)
            return $model->newCollection();

        return $model->getScoutModelsByIds(
            $builder,
            collect($hits)->pluck('_id')->values()->all()
        );
    }

    public function getTotalCount($results) {
        return array_get($results, 'hits.total.value', 0);
    }

    public function flush($model) {
        $this->client->indices()->delete([
            'index' => $model->searchableAs(),
        ]);

        Artisan::call('scout:elasticsearch:create', [
            'model' => get_class($model)
        ]);
    }

    protected function performSearch (Builder $builder, array $options = []) {
        $params = array_merge_recursive($this->getRequestBody($builder->model), [
            'body' => [
                'from' => 0,
                'size' => 20,
                'query' => [
                    'multi_match' => [
                        'query' => $builder->query ?? '',
                        'fields'=> $this->getSearchableFields($builder->model),
                        'type' => 'phrase_prefix'
                    ],
                ]
            ],
        ], $options);
        return $this->client->search($params);
    }

    /**
     * @param $model
     * @return array
     */
    protected function getSearchableFields ($model) {
        if (!method_exists($model, 'searchableFields')) {
            return [];
        }
        return $model->searchableFields();
    }

    /**
     * @param $model
     * @param array $options
     * @return array
     */
    protected function getRequestBody ($model, array $options = []) {
        return array_merge_recursive([
            'index' => $model->searchableAs(),
            'type' => $model->searchableAs(),
        ], $options);
    }
}
