<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/5/26
 * Time: 17:54
 */

namespace app\base\model;

use think\Model;

class Part extends Model
{
    protected $table='part';

    function partsupp(){
        return $this->belongsTo('Partsupp','P_PARTKEY','PS_PARTKEY');
    }

}