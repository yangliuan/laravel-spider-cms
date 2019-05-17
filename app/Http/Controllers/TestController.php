<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class TestController extends Controller
{
    public function avgQuery(Request $request)
    {
        $es = app('es');
        $params = [
            'index' => 'zhilian',
            'type' => '_doc',
            'body' => [
                'size' => 0,
                'aggs' => [
                    'job_desc' => [
                        'terms' => [
                            'size' => 6500,
                            'field' => 'job_desc'
                        ]
                    ]
                ]
            ]
        ];
        $results = $es->search($params);
        $name = [];
        $data = [];
        if (isset($results['aggregations']['job_desc']['buckets']) && is_array($results['aggregations']['job_desc']['buckets'])) {
            $buckets = $results['aggregations']['job_desc']['buckets'];
            foreach ($buckets as $key => $bucket) {
                //英文
                if (preg_match('/[a-zA-Z]/', $bucket['key']) && $bucket['doc_count'] >= 50) {
                    $name[] = $bucket['key'];
                    $data[] = $bucket['doc_count'];
                }
            }
        }
        return response()->json(compact('name', 'data'));
    }
}
