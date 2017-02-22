<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/13 0013
 * Time: 14:35
 */

namespace Api\Model;


class SupplierModel extends BaseModel{

    /**
     * @return array
     * 供应商列表
     */
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

    /**
     * @return mixed
     * 报价记录
     */
    public function offerRecord() {
        $user = session('WST_USER');
        $goodsId = I('goodsId');
        $sql = "SELECT * FROM __PREFIX__offer  where  goodsId=$goodsId AND userId = {$user['userId']}";
        $result = D('offer')->query($sql);
        return $result;
    }

    /**
     * @return array
     * 获取我的报价列表
     */
    public function getOfferList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;//当前页
        $user = session('WST_USER');
        $sql = "SELECT oo.*,goodsName,goodsImg,goodsStock,typeSn FROM __PREFIX__offer as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId JOIN __PREFIX__goods_type t ON gg.attrCatId = t.typeId where  userId = {$user['userId']} ORDER BY offerTime DESC ";
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

    /**
     * @return array
     * 获取我的成功报价列表
     */
    public function getSuccessOfferList() {
        $pcurr = (int)I("pcurr") ? (int)I("pcurr") : 1;//当前页
        $user = session('WST_USER');
        $sql = "SELECT oo.*,goodsName,goodsImg,goodsStock,typeSn FROM __PREFIX__offer as oo JOIN __PREFIX__goods as gg ON gg.goodsId = oo.goodsId JOIN __PREFIX__goods_type t ON gg.attrCatId = t.typeId where  userId = {$user['userId']} AND isCheck = 2 ORDER BY offerTime DESC ";
        $result = $this->pageQuery($sql , $pcurr , 5);
        return $result;
    }

    /**
     * @return array
     * 供应商订单列表页
     */
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

    /**
     * @return array
     * 消息页计数
     */
    public function messageCount() {
        //新留言
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $sId = D('supperlier')->field('userId,supplierId')->where("userID = $userId")->find();
        $message = D('supplier_message')->where("supplierId = {$sId['supplierId']} and isCheck = 1")->count();
        $result = array();
        $result['message'] = (int)$message;
        //成功报价
        $offer = $this->getSuccessOfferList();
        $result['offer'] = count($offer);
        //新订单
        //todo sql语句待修改
        $order = D('order')->where()->count();
        $result['order'] = (int)$order;
        return $result;
    }

    /**
     * @return mixed
     * 确认订单
     */
    public function orderSure() {
        $oId = I('orderId');
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $sId = D('supperlier')->field('userId,supplierId')->where("userID = $userId")->find();
        //更新订单状态
        $datas = array();
        $datas['orderStatus'] = 1;
        // TODO 这里暂时将shopId定义为供应商ID 后续再讨论是否修改
        $result = D('orders')->where("shopId = {$sId['supplierId']} and orderId = $oId")->save($datas);
        return $result;
    }

    /**
     * @return mixed
     * 回复评价
     */
    public function commentBack() {
        $cId = I('orderId');
        $comment = I('comment');
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $datas = array();
        $datas['orderId'] = $cId;
        $datas['content'] = $comment;
        $datas['userId'] = $userId;
        $datas['type'] = 2;  //若有类型 则1为发布的评价  2为回复评价
        $datas['createTime'] = date('Y-m-d H:i:s' , time());
        $result = D('comment')->add($datas);
        return $result;
    }
}