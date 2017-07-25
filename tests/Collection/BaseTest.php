<?php
// +----------------------------------------------------------------------
// | BaseTest.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Tests\Collection;

use limx\Support\Collection;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected $arr = [
        'name' => 'limx',
        'sex' => 1,
        'tel' => ['mobile' => '18678017520', 'phone' => '8653623']
    ];

    public function testInit()
    {
        $arr = ['name' => 'limx', 'sex' => 1];
        $collection = new Collection($arr);
        $this->assertEquals('limx', $collection->name);
    }

    public function testGet()
    {
        $collection = new Collection($this->arr);
        $this->assertEquals('limx', $collection->name);
        $this->assertEquals('18678017520', $collection->tel['mobile']);
        $this->assertEquals('18678017520', $collection->get('tel.mobile'));
    }

    public function testAll()
    {
        
    }
}