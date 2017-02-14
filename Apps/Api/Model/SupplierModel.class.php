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
        $sql = "SELECT * FROM __PREFIX__supperlier  where  supplierFlag=1 ";
        if (I('keyword')) {
            $keyword = I('keyword');
            $order = " and supplierCompany like '%$keyword%' ";
            $sql .=$order;
        }
        $result = D('offer')->query($sql);
        return $result;
    }

    public function offerRecord() {
        $user = session('WST_USER');
        //采购进行中的
        $goodsId = I('goodsId');
        $sql = "SELECT * FROM __PREFIX__offer  where  goodsId=$goodsId AND userId = {$user['userId']}";
        $result = D('offer')->query($sql);
        return $result;
    }

    public function getOfferList() {
        $user = session('WST_USER');
        $sql = "SELECT * FROM __PREFIX__offer as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId where  userId = {$user['userId']}";
        $result = D('offer')->query($sql);
        return $result;
    }
}