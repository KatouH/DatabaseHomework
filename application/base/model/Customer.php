<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/5/27
 * Time: 20:55
 */

namespace app\base\model;

use think\Model;

class Customer extends Model
{
    protected $name="customer";
    public function orders(){
        return $this->hasMany("Orders","O_CUSTKEY","C_CUSTKEY")->field("O_ORDERKEY,O_ORDERDATE,O_TOTALPRICE,O_CUSTKEY");
    }
}