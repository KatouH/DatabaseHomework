<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/5/27
 * Time: 21:02
 */

namespace app\base\model;

use think\Model;

class Supplier extends Model
{
    protected $name="supplier";

    public function nation(){
        return $this->hasOne("Nation","N_NATIONKEY","S_NATIONKEY")->field("N_NAME");
    }
}