<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/5/26
 * Time: 17:57
 */

namespace app\api\controller;

use app\base\controller\Api;
use app\base\model\Orders;
use app\base\model\Partsupp;

class Deal extends Api
{
    //select
    /**
     * @param int $page
     */
    public function select($page = 0){
        $selects=input('post.select');
        $where= input('post.where');
        $type=input('post.type');
        $data=return_type($type);
        if(is_null($where)&&is_null($selects)){
            e(1,"缺少查询条件");
        }
        $total_page=intval($data->where($where)->count()/10+1);
        $partlist = $data->where($where)->field($selects)->limit($page*10,10)->select();
        s($total_page,$partlist);
    }

    public  function insert(){
        $inputs=input('post.');$type=input('post.type');$data=return_type($type);$keyName=null;$foreignName=null;
        $foreignTable=null;$foreignKey=null;
        $dataId=getNewKey($type,$keyName,$foreignKey,$foreignTable,$foreignName);
        if(!is_null($keyName)){
            if(!is_null($foreignKey)&&$inputs[$foreignKey]<=$foreignTable->max($foreignName)){
                $inputs[$keyName]=$dataId;
                $data->allowField($keyName.','.$inputs['field'])->save($inputs);
            }
            else if(is_null($foreignKey)){
                $inputs[$keyName]=$dataId;
                $data->allowField($keyName.','.$inputs['field'])->save($inputs);
            }
            else e(1,"err");
        }
        else {
            if($type=="partsupp"){
                //没有做post 数据是PS_PARTKEY,PS_SUPPKEY是否为null的判断，前端要求为must
                if($inputs['PS_PARTKEY']<=((new \app\base\model\Part())->max('P_PARTKEY'))&&$inputs["PS_SUPPKEY"]<=((new \app\base\model\Supplier())
                        ->max('S_SUPPKEY'))){
                    if(is_null($data->where(["PS_PARTKEY"=>$inputs['PS_PARTKEY'],"PS_SUPPKEY"=>$inputs["PS_SUPPKEY"]])->find())){
                        $data->allowField($inputs['field'])->save($inputs);
                        s("a",$data);}
                    else e(1,"err1");}
                else e(1,"err2");}
            else if($type=="lineitem"){
                //没有做post 数据是PS_PARTKEY,PS_SUPPKEY是否为null的判断，前端要求为must
                if(is_null((new Orders())->where(["O_ORDERKEY"=>$inputs['L_ORDERKEY']])->find()))  e(1,"hfdkfa");
                $lineitemnum=$data->where(["L_ORDERKEY"=>$inputs['L_ORDERKEY']])->max("L_LINENUMBER")+1;
                if(!is_null((new Partsupp())->where(["PS_PARTKEY"=>$inputs['L_PARTKEY'],"PS_SUPPKEY"=>$inputs['L_SUPPKEY']])->find())){
                    $data->L_LINENUMBER=$lineitemnum;
                    $data->allowField($inputs['field'].",L_LINENUMBER")->save($inputs);
                    s("hha",$data);}
                else e(1,"err3");}}
        s("success");
    }

    public function delete(){
        $inputs=input('post.');
        $type=input('post.type');
        $data=return_type($type);
        $data->where($inputs['where'])->delete();
        s("success");
    }

    public function update(){
        $inputs=input('post.');
        $type=input('post.type');
        $data=return_type($type);
        $data=$data->where($inputs['where'])->find();
        $data->allowField($inputs['field'])->save($inputs);
        s("success");
    }
}