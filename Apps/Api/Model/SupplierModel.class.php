<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/13 0013
 * Time: 14:35
 */

namespace Api\Model;


class SupplierModel extends BaseModel{

    public function getList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;//当前页
        //TODO 因还没有接入地图接口暂不进行距离计算
        $sql = "SELECT * FROM __PREFIX__supperlier  where  supplierFlag=1 ";
        if (I('keyword')) {
            $keyword = I('keyword');
            $order = " and supplierCompany like '%$keyword%' ";
            $sql .=$order;
        }
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

    public function offerRecord() {
        $user = session('WST_USER');
        $goodsId = I('goodsId');
        $sql = "SELECT * FROM __PREFIX__offer  where  goodsId=$goodsId AND userId = {$user['userId']}";
        $result = D('offer')->query($sql);
        return $result;
    }

    public function getOfferList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;//当前页
        $user = session('WST_USER');
        $sql = "SELECT * FROM __PREFIX__offer as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId where  userId = {$user['userId']}";
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

    public function getSuccessOfferList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;//当前页
        $user = session('WST_USER');
        $sql = "SELECT * FROM __PREFIX__offer as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId where  userId = {$user['userId']} AND isCheck = 1";
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

    public function orderList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;
        $user = session('WST_USER');
        //TODO sql语句待修改
        $sql = "SELECT * FROM __PREFIX__orders as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId where  userId = {$user['userId']} AND isCheck = 1";
        //供应商没发货的
        if (I('unSend')) {
            $sql .= " and orderStatus = 1";
        }
        //供应商已发货但业主还未确认的
        if (I('sent')) {
            $sql .= " and orderStatus = 2";
        }
        //业主已确认待评价的
        if (I('unComment')) {
            $sql .= " and orderStatus = 3";
        }
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

}