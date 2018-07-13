<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/5/27
 * Time: 21:01
 */

namespace app\base\model;

use think\Model;

class Partsupp extends Model
{
    protected $name="partsupp";

    public function part(){
        return $this->hasOne("Part","P_PARTKEY","PS_PARTKEY")->field("P_PARTKEY,P_MFGR");
    }

    public function supplier(){
        return $this->hasOne("Supplier","S_SUPPKEY","PS_SUPPKEY");
    }
}