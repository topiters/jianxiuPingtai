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

}