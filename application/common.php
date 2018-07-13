<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function s($msg=null, $data = null){
    throw new think\exception\HttpResponseException(json([
        "code"=>200,
        "data" => $data,
        "msg" => mlang($msg)
    ]));
}

function e($code = 0, $msg, $data = null){
    throw new think\exception\HttpResponseException(json(
        ["code"=>400+$code,
            "data" => $data,
            "msg" => mlang($msg)
        ]));
}

function mlang($str){
    return is_null($str)?null:lang($str);
}

function return_type($type){
    switch ($type){
        case "customer":
            return new \app\base\model\Customer();
            break;
        case "lineitem":
            return new \app\base\model\Lineitem();
            break;
        case "nation":
            return new \app\base\model\Nation();
            break;
        case "orders":
            return new \app\base\model\Orders();
            break;
        case "part":
            return new \app\base\model\Part();
            break;
        case "partsupp":
            return new \app\base\model\Partsupp();
            break;
        case "region":
            return new \app\base\model\Region();
            break;
        case "supplier":
            return new \app\base\model\Supplier();
            break;
        default:
            return null;
            break;
    }
}

function getNewKey($type,&$keyName,&$foreignKey,&$foreignTable,&$foreignName){
    switch ($type){
        case "customer":
            $keyName="C_CUSTKEY";
            $foreignKey="C_NATIONKEY";
            $foreignTable=new \app\base\model\Nation();
            $foreignName="N_NATIONKEY";
            return (new \app\base\model\Customer())->max('C_CUSTKEY')+1;
            break;
        case "nation":
            $keyName="N_NATIONKEY";
            $foreignKey="N_REGIONKEY";
            $foreignTable=new \app\base\model\Region();
            $foreignName="R_REGIONKEY";
            return (new \app\base\model\Nation())->max('N_NATIONKEY')+1;
            break;
        case "orders":
            $keyName="O_ORDERKEY";
            $foreignKey="O_CUSTKEY";
            $foreignTable=new \app\base\model\Customer();
            $foreignName="C_CUSTKEY";
            return (new \app\base\model\Orders())->max('O_ORDERKEY')+1;
            break;
        case "part":
            $keyName="P_PARTKEY";
            return (new \app\base\model\Part())->max('P_PARTKEY')+1;
            break;
        case "region":
            $keyName="R_REGIONKEY";
            return (new \app\base\model\Region())->max('R_REGIONKEY')+1;
            break;
        case "supplier":
            $keyName="S_SUPPKEY";
            return (new \app\base\model\Supplier())->max('S_SUPPKEY')+1;
            break;
        /*case "lineitem":
            return new \app\base\model\Lineitem();
            break;
        case "partsupp":
            return new \app\base\model\Partsupp();
            break;*/
        default:
            return null;
            break;
    }
}