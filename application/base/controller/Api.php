<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/6/19
 * Time: 1:03
 */

namespace app\base\controller;

use think\Controller;

class Api extends Controller
{
    protected $beforeActionList = ['crossOrigin'];

    protected function crossOrigin(){
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE,PUT');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            exit;
        }
    }
}