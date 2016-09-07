<?php
/**
 * Created by PhpStorm.
 * User: zhanghengyu
 * Date: 2016/9/7
 * Time: 9:43
 */
namespace HengYuZ\test;

include '../src/Dosql.php';

use HengYuZ\src\Dosql;


$dosql = new Dosql(['localhost' => 'localhost', 'username' => 'root', '', 'db_name' => 'test']);

$res = $dosql->select('users', '', ['name=' => 'zhu']);
/**
 * 打印查找后的结果$res数组
 */
//foreach ($res as $key => $item) {
//    $i = 1;
//    foreach ($item as $key2 => $value) {
//        if ($i % count($item) == 0)
//            echo $value . '<br/>';
//        else
//            echo $value . ' ';
//        $i++;
//    }
//}

//更新数据
$dosql->update('users', ['name=' => 'zhu'], ['name=' => 'zhy']);
//插入数据
$dosql->insert('users', ['123456', '12346513@qq.com']);
//删除数据
$dosql->delete('users', ['OR' => ['name=' => '123456', 'email=' => 'hello.com']]);
