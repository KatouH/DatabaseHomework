<?php
/**
 * Created by PhpStorm.
 * User: 何东
 * Date: 2018/6/19
 * Time: 1:02
 */

namespace app\api\controller;


use app\base\controller\Api;
use app\base\model\Customer;
use app\base\model\Orders;
use app\base\model\Partsupp;

class search extends Api
{
    function mincsq(){
        $inputs=input('post.');
        $type=$inputs["type"];
        $size=$inputs["size"];
        $region=$inputs["region"];
        $pwhere=array();
        $rwhere=array();
        $pswhere=array();
        $pwhere["p.P_TYPE"]=array("eq",$type);
        $pwhere["p.P_SIZE"]=array("eq",$size);
        $rwhere["r.R_NAME"]=array("eq",$region);
        $minCost=(new Partsupp())->table("region r,nation n,supplier s,partsupp ps,part p")
            ->where("r.R_REGIONKEY=n.N_REGIONKEY and n.N_NATIONKEY=s.S_NATIONKEY and s.S_SUPPKEY=ps.PS_SUPPKEY and ps.PS_PARTKEY=p.P_PARTKEY")
            ->where($pwhere)->where($rwhere)
            ->min("ps.PS_SUPPLYCOST");
        $pswhere["ps.PS_SUPPLYCOST"]=array("eq",$minCost);
        $total_page=intval((new Partsupp())->table("region r,nation n,supplier s,partsupp ps,part p")
            ->where("r.R_REGIONKEY=n.N_REGIONKEY and n.N_NATIONKEY=s.S_NATIONKEY and s.S_SUPPKEY=ps.PS_SUPPKEY and ps.PS_PARTKEY=p.P_PARTKEY")
            ->where($pwhere)->where($rwhere)->where($pswhere)->count()/10+1);
        $partsupp=(new Partsupp())->table("region r,nation n,supplier s,partsupp ps,part p")
            ->where("r.R_REGIONKEY=n.N_REGIONKEY and n.N_NATIONKEY=s.S_NATIONKEY and s.S_SUPPKEY=ps.PS_SUPPKEY and ps.PS_PARTKEY=p.P_PARTKEY")
            ->where($pwhere)->where($rwhere)->where($pswhere)
            ->field("ps.*")
            ->select();
        foreach ($partsupp as $item){
            $item->part;
            $item->supplier->nation;
            $sortKey[]=$item["supplier"]["S_ACCTBAL"];
        }
        array_multisort($sortKey,SORT_DESC,$partsupp);
        s($total_page,$partsupp);
    }

    function opcq(){
        $inputs=input('post.');
        $startDate=$inputs["data1"];
        $endDate=$inputs["data2"];
        $where=array();
        $where["o.O_ORDERDATE"]=array(array("egt",$startDate),array("elt",$endDate));
        $order=(new Orders())->table("orders o,lineitem l")->where($where)->where("l.L_RECEIPTDATE>l.L_COMMITDATE and o.O_ORDERKEY=l.L_ORDERKEY")
            ->field(array("o.O_ORDERPRIORITY","count(distinct(o.O_ORDERKEY))"=>"countNum"))->group("o.O_ORDERPRIORITY")->order("o.O_ORDERPRIORITY")
            ->select();
        s("success",$order);
    }

    function rirq(){
        $inputs=input('post.');
        $startDate=$inputs["data1"];
        $endDate=$inputs["data2"];
        $where=array();
        $where["o.O_ORDERDATE"]=array(array("egt",$startDate),array("elt",$endDate));
        $total_page=intval((new Customer())->table("customer c,lineitem l,nation n,orders o")
            ->where("l.L_ORDERKEY=o.O_ORDERKEY and o.O_CUSTKEY=c.C_CUSTKEY and c.C_NATIONKEY=n.N_NATIONKEY")
            ->where($where)
            ->where("l.L_RETURNFLAG","=","R")
            ->field(array("c.C_NAME","c.C_ADDRESS","n.N_NAME","c.C_PHONE","c.C_ACCTBAL","c.C_COMMENT","sum(l.L_EXTENDEDPRICE*(1-l.L_DISCOUNT))"=>"revenueLost"))
            ->group("c.C_CUSTKEY")
            ->order("revenueLost","DESC")->count()/10+1);
        $customer=(new Customer())->table("customer c,lineitem l,nation n,orders o")
            ->where("l.L_ORDERKEY=o.O_ORDERKEY and o.O_CUSTKEY=c.C_CUSTKEY and c.C_NATIONKEY=n.N_NATIONKEY")
            ->where($where)
            ->where("l.L_RETURNFLAG","=","R")
            ->field(array("c.C_NAME","c.C_ADDRESS","n.N_NAME","c.C_PHONE","c.C_ACCTBAL","c.C_COMMENT","sum(l.L_EXTENDEDPRICE*(1-l.L_DISCOUNT))"=>"revenueLost"))
            ->group("c.C_CUSTKEY")
            ->order("revenueLost","DESC")
            ->limit(0,20)
            ->select();
        s($total_page,$customer);
    }

    function lvcq($page=0){
        $inputs=input('post.');
        $quantity=$inputs["quantity"];
        $total_page=intval((new Customer())->table("customer c,orders o")
            ->where("o.O_CUSTKEY=c.C_CUSTKEY")
            ->field(array("c.C_NAME","c.C_CUSTKEY","count(o.O_ORDERKEY)"=>"quantity"))
            ->group("o.O_CUSTKEY")
            ->having("count(o.O_ORDERKEY)>$quantity")
            ->order("quantity","DESC")->count()/10+1);
        $customer=(new Customer())->table("customer c,orders o")
            ->where("o.O_CUSTKEY=c.C_CUSTKEY")
            ->field(array("c.C_NAME","c.C_CUSTKEY","count(o.O_ORDERKEY)"=>"quantity"))
            ->group("o.O_CUSTKEY")
            ->having("count(o.O_ORDERKEY)>$quantity")
            ->order("quantity","DESC")
            ->limit($page*10,10)
            ->select();
        foreach ($customer as $item){
            $item->orders;
        }
        s($total_page,$customer);
    }
}