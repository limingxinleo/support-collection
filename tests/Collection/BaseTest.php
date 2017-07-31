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

    protected $list = [
        ['id' => 1, 'name' => 'limx'],
        ['id' => 2, 'name' => 'Agnes'],
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
        $collection = new Collection($this->arr);
        $this->assertEquals($this->arr, $collection->all());
    }

    public function testOnly()
    {
        $collection = new Collection($this->arr);
        $this->assertArrayNotHasKey('tel', $collection->only(['name', 'sex']));
    }

    public function testExcept()
    {
        $collection = new Collection($this->arr);
        $this->assertArrayNotHasKey('name', $collection->except(['name']));
    }

    public function testMerge()
    {
        $collection = new Collection($this->arr);
        $new = $collection->merge(['name2' => 'limx2']);
        $this->assertArrayHasKey('name', $new);
        $this->assertArrayHasKey('name2', $new);
    }

    public function testHas()
    {
        $collection = new Collection($this->arr);
        $this->assertTrue($collection->has('name'));
    }

    public function testFirst()
    {
        $collection = new Collection($this->list);
        $this->assertEquals($this->list[0], $collection->first());
    }

    public function testLast()
    {
        $collection = new Collection($this->list);
        $this->assertEquals($this->list[1], $collection->last());
    }

    public function testAdd()
    {
        $collection = new Collection($this->arr);
        $arr = $this->arr;

        $collection->add('name2', 'Agnes');
        $arr['name2'] = 'Agnes';
        $this->assertEquals($arr, $collection->all());
    }

    public function testSet()
    {
        $collection = new Collection($this->arr);
        $arr = $this->arr;

        $collection->set('name', 'Agnes');
        $arr['name'] = 'Agnes';
        $this->assertEquals($arr, $collection->all());
    }

    public function testForget()
    {
        $collection = new Collection($this->arr);
        $arr = $this->arr;

        $collection->forget('name');
        unset($arr['name']);
        $this->assertEquals($arr, $collection->all());
    }

    public function testFilter()
    {
        $collection = new Collection($this->arr);

        $res = $collection->filter(function ($item) {
            return $item == 'limx';
        });

        $this->assertEquals(['name' => 'limx'], $res->all());
    }

    public function testWhere()
    {
        $collection = new Collection($this->list);

        $res = $collection->where('id', 1)->first();

        $this->assertEquals(['id' => 1, 'name' => 'limx'], $res);
    }
}