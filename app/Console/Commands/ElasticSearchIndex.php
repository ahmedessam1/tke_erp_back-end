<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;

class ElasticSearchIndex extends Command
{
    protected $signature = 'scout:elasticsearch:create {model}';
    protected $client;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an Elasticsearch index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = app('elasticsearch');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!class_exists($model = $this->argument('model')))
            return $this->error("{$model} could not be resolved.");

        $model = new $model;
        try {
            $this->client->indices()->create([
                'index' => $model->searchableAs(),
                'body' => [
                    'settings' => [
                        'index' => [
                            'analysis' => [
                                'filter' => $this->filters(),
                                'analyzer' => $this->analyzers(),
                            ]
                        ]
                    ],
                ],
            ]);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    protected function filters () {
        return [
            'words_splitter' => [
                'type' => 'word_delimiter',
                'preserve_original' => 'true',
                'catenate_all' => 'true',
            ],
        ];
    }

    protected function analyzers () {
        return [
            'default' => [
                'type' => 'custom',
                'tokenizer' => 'standard',
                'char_filter' => ['html_strip'],
                'filter' => ['lowercase', 'words_splitter']
            ],
        ];
    }
}
