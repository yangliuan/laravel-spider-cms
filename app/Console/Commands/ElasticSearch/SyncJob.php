<?php

namespace App\Console\Commands\ElasticSearch;

use Illuminate\Console\Command;
use App\JobZhiLian;

class SyncJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:syncjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步岗位数据';

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
        $es = app('es');
        JobZhiLian::query()
            ->chunkById(100, function ($jobs) use ($es) {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的岗位描述', $jobs->first()->id, $jobs->last()->id));
                $params = ['body' => []];
                foreach ($jobs as $job) {
                    $data = $job->toESArray();
                    $params['body'][] = [
                        'index' => [
                            // 从参数中读取索引名称
                            '_index' => 'zhilian',
                            '_type'  => '_doc',
                            '_id'    => $data['id'],
                        ],
                    ];
                    $params['body'][] = $data;
                }
                try {
                    // 使用 bulk 方法批量创建
                    $es->bulk($params);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });
        $this->info('数据同步成功');
    }
}
