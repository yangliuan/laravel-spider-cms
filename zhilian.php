<?php

require './vendor/autoload.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\selector;

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => '智联招聘',
    'log_show' => true,
    'domains' => array(
        'sou.zhaopin.com',
        'jobs.zhaopin.com',
    ),
    'db_config' => array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'pass' => '123456',
        'name' => 'lara-spider-cms',
    ),
    'export' => array(
        'type' => 'db',
        'table' => 'zhilian',  // 如果数据表没有数据新增请检查表结构和字段名是否匹配
    ),
    'scan_urls' => array(
        'https://sou.zhaopin.com/?jl=530&sf=0&st=0&kw=PHP&kt=3',
        'https://jobs.zhaopin.com/',
    ),
    'list_url_regexes' => array(
        "https://sou.zhaopin.com/?p=\d+&jl=530&sf=0&st=0&kw=PHP&kt=3",
    ),
    'content_url_regexes' => array(
        'https://jobs.zhaopin.com/',
    ),
    'fields' => array(
        array(
            //岗位名称
            'name' => 'job_position',
            'selector' => "//h3[@class='summary-plane__title']",
            'required' => true,
        ),
        array(
            //薪资
            'name' => 'salary',
            'selector' => "//span[@class='summary-plane__salary']",
            'required' => true,
        ),
        array(
            //概述
            'name' => 'summary',
            'selector' => "//ul[@class='summary-plane__info']/li[position()>1]",
            'required' => true,
        ),
        array(
            //职位描述
            'name' => 'job_desc',
            'selector' => "//div[@class='describtion__detail-content']",
            'required' => true,
        ),
        array(
            //工作地址
            'name' => 'job_address',
            'selector' => "//span[@class='job-address__content-text']",
            'required' => true,
        ),
        array(
            //更新时间
            'name' => 'updated_time',
            'selector' => "//span[@class='summary-plane__time']",
            'required' => true,
        ),
    ),
);
$spider = new phpspider($configs);
$spider->on_start = function ($phpspider) {
    requests::set_header('Referer', 'https://www.zhaopin.com/');
    $listApi = 'https://fe-api.zhaopin.com/c/i/sou';
    $field = [
        'start' => 0,
        'pageSize' => 90,
        'cityId' => 538,
        'workExperience' => -1,
        'education' => -1,
        'companyType' => -1,
        'employmentType' => -1,
        'jobWelfareTag' => -1,
        'sortType' => 'publish',
        'kw' => 'php',
        'kt' => 3,
        '_v' => 0.22923982,
        'x-zp-page-request-id' => '521b56b9d27048b6b386222c3191637c-1555132459338-297018',
    ];

    for ($i = 0; $i < 10000; ++$i) {
        $field['start'] = $field['pageSize'] * $i;
        $json = requests::get($listApi, $field);
        $resArr = json_decode($json, true);
        $numFound = $resArr['data']['numFound'];
        $numTotal = $resArr['data']['numTotal'];
        if (0 == $numFound || 0 == $numTotal) {
            break;
        }
        foreach ($resArr['data']['results'] as $key => $value) {
            if ($value['positionURL']) {
                $phpspider->add_url($value['positionURL']);
            }
        }
    }
};
$spider->on_status_code = function ($status_code, $url, $content, $phpspider) {
    // 如果状态码为429，说明对方网站设置了不让同一个客户端同时请求太多次
    if ('429' == $status_code) {
        // 将url插入待爬的队列中,等待再次爬取
        $phpspider->add_url($url);
        // 当前页先不处理了
        return false;
    }
    // 不拦截的状态码这里记得要返回，否则后面内容就都空了
    return $content;
};
$spider->is_anti_spider = function ($url, $content, $phpspider) {
    // $content中包含"404页面不存在"字符串
    if (false !== strpos($content, '404页面不存在')) {
        // 如果使用了代理IP，IP切换需要时间，这里可以添加到队列等下次换了IP再抓取
        // $phpspider->add_url($url);
        return true; // 告诉框架网页被反爬虫了，不要继续处理它
    }
    // 当前页面没有被反爬虫，可以继续处理
    return false;
};
$spider->on_extract_field = function ($fieldname, $data, $page) {
    if ('job_address' == $fieldname) {
        $data = selector::remove($data, "//i[contains(@class,'iconfont icon-locate')]");
    }
    if ('updated_time' == $fieldname) {
        $data = selector::remove($data, "//i[contains(@class,'iconfont icon-update-time')]");
    }
    if ('job_desc' == $fieldname) {
        $data = strip_tags($data);
    }

    return $data;
};
$spider->start();
