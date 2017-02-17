<?php
namespace Api\Action;

/**
 *  供应商控制器
 * ==============================================
 * 版权所有 2010-2016 http://www.chunni168.com
 * ----------------------------------------------
 * 这不是一个自由软件，未经授权不许任何使用和传播。
 * ==============================================
 * @date   : 2017年1月18日
 * @author : top_iter 2504585798@qq.com
 * @version:1.0
 */
class SupplierAction extends BaseAction {

    public function __construct() {
        parent::__construct();
        $USER = session('WST_USER');
//        dump($USER);die;
        if ($USER['userType'] != 2) {
            $data["msg"] = '你还不是供应商,没有权限操作!';
            $data = array('status' => self::API_PERMISSION_NO_OPERATION , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 判断供应商身份
     */
    public function index() {
        $data = array('status' => self::API_REQUEST_SUCCESS);
        $this->stringify($data);
    }
    /**
     * 供应商信息完善
     */
    public function addByUser() {
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $shopsOne = M('users')->where(array('userId' => $userId,'userType'=>2))->find();
        if (!$shopsOne) {
            $data["msg"] = '你还不是供应商,没有权限操作!';
            $data = array('status' => self::API_PERMISSION_NO_OPERATION , 'msg' => $data);
        }
        $datas = array();
        //$supplierImg=I('supplierImg');//公司logo
        if ($_FILES['supplierImg']) {
            $info = $this->uploadPic();
            if ($info['status'] == 1) {
                $shopImg = $info['savename'] . $info['savethumbname'];
            }
        }
        $shopCompany = $_POST['supplierCompany'];//公司名称
        $goodsCatId1 = $_POST['goodsCatId1'];//行业分类
        $shopTotal = $_POST['supplierTotal'];//注册金额
        $shopInfo = $_POST['supplierInfo'];//简介
        $shopArea = $_POST['supplierArea'];//公司所在城市
        $shopAddr = $_POST['supplierAddr'];//公司地址
        $latitude = $_POST['latitude'];  //经度
        $longitude = $_POST['longitude']; //纬度
        $shopHost = $_POST['supplierHost'];//企业法人
        $shopUrl = $_POST['supplierUrl'];//网址
        $shopIdentimg = $_POST['supplierIdentimg'];//公司证明
        $datas['supplierImg'] = $shopImg;
        $datas['supplierCompany'] = $shopCompany;
        $datas['goodsCatId1'] = $goodsCatId1;
        $datas['supplierTotal'] = $shopTotal;
        $datas['supplierUrl'] = $shopUrl;
        $datas['supplierInfo'] = $shopInfo;
        $datas['supplierHost'] = $shopHost;
        $datas['supplierArea'] = $shopArea;
        $datas['supplierAddr'] = $shopAddr;
        $datas['latitude'] = $latitude;
        $datas['longitude'] = $longitude;
        $shopIdentimg = array();
        //附件
        if ($_FILES) {
            $supplierIdentimgInfo = $this->uploads();
            if ($supplierIdentimgInfo) {
                foreach ($supplierIdentimgInfo as $k => $v) {
                    $imgs[] = $v['savepath'] . $v['savethumbname'];
                }
            }
        }
        $datas['supplierIdentimg'] = serialize($shopIdentimg);
        $datas['createTime'] = date('Y-m-d H:i:s',time());


        $result = M('supperlier')->where(array('userId' => $userId))->save($datas);
        if ($result != false) {
            $data["msg"] = '数据保存成功';
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $data);
            $this->stringify($data);

        } else {
            $data["msg"] = '数据没有更新，请更新后保存...!';
            $data = array('status' => self::API_UPDATE_FALSE , 'msg' => $data);
            $this->stringify($data);
        }

    }

    /**
     * 供应商报价
     */
    public function offer() {
        $USER = session('WST_USER');
        $goodsId = I('goodsId');
//        $result = D('offer')->where("goodsId = $goodsId and userId = {$USER['userId']}")->count();
//        if ($result > 0) {
//            $data["msg"] = '您已对该采购报价,不能再次报价!';
//            $data = array('status' => self::API_ADD_FALSE , 'msg' => $data);
//            $this->stringify($data);
//        }
        $goodsModel = D('Api/goods');
        $result = $goodsModel->gooodsPurchaseDetails();
        $time = date('Y-m-d H:i:s' , time());
        $t = $result[0]['endTime'];
        if ($time > $t) {
            $data["msg"] = '该采购已结束!';
            $data = array('status' => self::API_ADD_FALSE , 'msg' => $data);
            $this->stringify($data);
        }
        $money = I('money');
        $data = array();
        $data['goodsId'] = $goodsId;
        $data['userId'] = $USER['userId'];
        $data['money'] = $money;
        $data['offerTime'] = date('Y-m-d H:i:s' , time());
        $result = D('offer')->add($data);
        if ($result != false) {
            $data["msg"] = '报价成功!';
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $data);
            $this->stringify($data);
        } else {
            $data["msg"] = '添加失败!';
            $data = array('status' => self::API_ADD_FALSE , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 我的报价
     */
    public function myOffer() {
        $Model = D('Api/supplier');
        $result = $Model->getOfferList();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 附近的供应商
     */
    public function nearby() {
        $Model = D('Api/supplier');
        $result = $Model->getList();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 供应商详情
     */
    public function detail() {
        $id = I('supplierId');
        $result = D('supperlier')->where("supplierId = $id")->find();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 我的留言
     */
    public function myMessage() {
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $sId = D('supperlier')->field('userId,supplierId')->where("userID = $userId")->find();
        $result = D('supplier_message')->where("supplierId = {$sId['supplierId']}")->order("createTime desc")->select();
        //未读更新为已读
        D('supplier_message')->where("supplierId = {$sId['supplierId']}")->setField('isCheck' , 2);
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 消息页报价成功的订单
     */
    public function successOffer() {
        $Model = D('Api/supplier');
        $result = $Model->getSuccessOfferList();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 消息列表页计数
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
        $Model = D('Api/supplier');
        $offer = $Model->getSuccessOfferList();
        $result['offer'] = count($offer);
        //新订单
        //todo sql语句待修改
        $order = D('order')->where()->count();
        $result['order'] = (int)$order;
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 订单列表
     */
    public function order() {
        $Model = D('Api/supplier');
        $result = $Model->orderList();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 确认发货
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
        D('orders')->where("shopId = {$sId['supplierId']} and orderId = $oId")->save($datas);
    }

}
?>