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
        $supplierImg=I('supplierImg');//公司logo
        $shopCompany = $_POST['supplierCompany'];//公司名称
        $goodsCatId1 = $_POST['goodsCatId1'];//行业分类
        $shopTotal = $_POST['supplierTotal'];//注册金额
        $shopInfo = $_POST['supplierInfo'];//简介
        $shopTel = $_POST['supplierTel'];//电话
        $shopArea = $_POST['supplierArea'];//公司所在城市
        $shopAddr = $_POST['supplierAddr'];//公司地址
        $latitude = $_POST['latitude'];  //经度
        $longitude = $_POST['longitude']; //纬度
        $shopHost = $_POST['supplierHost'];//企业法人
        $shopUrl = $_POST['supplierUrl'];//网址
        $datas['supplierImg'] = $supplierImg;
        $datas['supplierCompany'] = $shopCompany;
        $datas['goodsCatId1'] = $goodsCatId1;
        $datas['supplierTotal'] = $shopTotal;
        $datas['supplierUrl'] = $shopUrl;
        $datas['supplierTel'] = $shopTel;
        $datas['supplierInfo'] = $shopInfo;
        $datas['supplierHost'] = $shopHost;
        $datas['supplierArea'] = $shopArea;
        $datas['supplierAddr'] = $shopAddr;
        $datas['latitude'] = $latitude;
        $datas['longitude'] = $longitude;
        $supplierIdentimg = I("supplierIdentimg");//附件
        if ($supplierIdentimg) {
            $datas['supplierIdentimg'] = serialize($supplierIdentimg);
        }
        $datas['createTime'] = date('Y-m-d H:i:s' , time());
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
        if (I('supplierId')){
            $id = I('supplierId');
            $where = "supplierId = $id";
        }else{
            $USER = session('WST_USER');
            $userId = $USER['userId'];
            $where = "userId = $userId";
        }

        $result = D('supperlier')->where($where)->find();
        $result['supplierIdentimg'] = unserialize($result['supplierIdentimg']);
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
        $Model = D('Api/supplier');
        $result = $Model->messageCount();
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
        $Model = D('Api/supplier');
        $result = $Model->orderSure();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '添加失败!';
            $data = array('status' => self::API_ADD_FALSE , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     * 供应商回复评价
     */
    public function commentBack() {
        $Model = D('Api/supplier');
        $result = $Model->commentBack();
        if ($result) {
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '添加失败!';
            $data = array('status' => self::API_ADD_FALSE , 'msg' => $data);
            $this->stringify($data);
        }
    }
}
?>