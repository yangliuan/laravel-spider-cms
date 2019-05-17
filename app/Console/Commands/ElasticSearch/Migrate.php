<?php

namespace App\Console\Commands\ElasticSearch;

use Illuminate\Console\Command;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移es索引文件';

    protected $es;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->es = app('es');
        if ($this->es->indices()->exists(['index' => 'zhilian'])) {
            $this->warn('The es index zhilian has already exists!');
        }
        $params = [
            'index' => 'zhilian',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                ],
                'mappings' => [
                    'properties' => [
                        'salary' => [
                            'analyzer' => 'ik_max_word',
                            'term_vector' => 'with_positions_offsets',
                            'boost' => 8,
                            'type' => 'text',
                            'fielddata' => 'true'
                        ],
                        'summary' => [
                            'analyzer' => 'ik_max_word',
                            'term_vector' => 'with_positions_offsets',
                            'boost' => 8,
                            'type' => 'text',
                            'fielddata' => 'true'
                        ],
                        'job_desc' => [
                            'analyzer' => 'english',
                            'term_vector' => 'with_positions_offsets',
                            'boost' => 8,
                            'type' => 'text',
                            'fielddata' => 'true'
                        ],
                        'job_address' => [
                            'analyzer' => 'ik_smart',
                            'term_vector' => 'with_positions_offsets',
                            'boost' => 8,
                            'type' => 'text',
                            'fielddata' => 'true'
                        ]
                    ]
                ],
            ]
        ];
        $response = $this->es->indices()->create($params);
        $this->info('索引迁移成功!');
    }
}
