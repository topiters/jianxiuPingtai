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
class SupperlierAction extends BaseAction {

    public function __construct() {
        parent::__construct();
        $USER = session('WST_USER');
        if ($USER['userType'] != 2) {
            $data["msg"] = '你还不是供应商,没有权限操作!';
            $data = array('status' => self::API_PERMISSION_NO_OPERATION , 'msg' => $data);
            $this->stringify($data);
        }
    }

    /**
     *
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
}


?>