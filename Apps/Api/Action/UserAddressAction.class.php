<?php
 namespace Api\Action;
/**
 * ==============================================
 * 版权所有 2010-2016 http://www.chunni168.com
 * ----------------------------------------------
 * 这不是一个自由软件，未经授权不许任何使用和传播。
 * ==============================================
 * 会员地址控制器
 */
class UserAddressAction extends BaseAction{

    public function index() {
        $m = D('Api/UserAddress');
        $object = array();
        if ((int)I('id' , 0) > 0) {
            $object = $m->get();
        } else {
            $object = $m->getList();
        }
        if ($object != false) {
//            $data["msg"] = '';
            $object = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $object);
            $this->stringify($object);
        } else {
            $object["msg"] = '数据请求失败,请重试!';
            $object = array('status' => self::API_UPDATE_FALSE , 'msg' => $object);
            $this->stringify($object);
        }
    }

	/**
	 * 跳到新增/编辑页面
	 */
	public function toEdit(){
		$this->isUserLogin();
	    $m = D('Api/UserAddress');
    	$object = array();
    	if((int)I('id',0)>0){
    		$object = $m->get();
    	}else{
    		$object = $m->getModel();
    	}
    	//获取地区信息
		$m = D('Home/Areas');
		$this->assign('areaList',$m->getProvinceList());
    	$this->assign('object',$object);
    	$this->assign("umark","addressQueryByPage");
		$this->view->display('default/users/useraddress/edit');
	}
	/**
	 * 新增/修改操作
	 */
	public function edit(){
        //		$this->isUserLogin();
        $m = D('Api/UserAddress');
    	$rs = array();
    	if((int)I('id',0)>0){
    		$rs = $m->edit();
    	}else{
    		$rs = $m->insert();
    	}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 删除操作
	 */
	public function del(){
//		$this->isUserLogin();
		$m = D('Api/UserAddress');
    	$rs = $m->del();
    	$this->ajaxReturn($rs);
	}

    /**
     * 设置为默认的地址
     */
    public function setDefault() {
        $m = D('Api/UserAddress');
        $rs = $m->setDefault();
        $this->ajaxReturn($rs);
    }
	/**
	 * 分页查询
	 */
	public function queryByPage(){
		$this->isLogin();
		$USER = session('WST_USER');
		$m = D('Api/UserAddress');
    	$list = $m->queryByList($USER['userId']);
    	$this->assign('List',$list);
    	$this->assign("umark","addressQueryByPage");
        $this->display("default/users/useraddress/list");
	}
	/**
	 * 获取用户地址
	 */
	public function getUserAddress(){
		$this->isUserLogin();
		$m = D('Api/UserAddress');
		$address = $m->getUserAddressInfo();	
		$addressInfo = array();
		$addressInfo["status"] = 1;
		$addressInfo["address"] = $address;
		$this->ajaxReturn($addressInfo);	
	}
	
	/**
	 * 获取区县
	 */
	public function getDistricts(){
		
		$m = D('Api/UserAddress');
		$areaId2 = (int)I("areaId2");
		$communitys = $m->getDistricts($areaId2);	
		$this->ajaxReturn($communitys);
			
	}
	
	/**
	 * 获取社区
	 */
	public function getCommunitys(){
		
		$m = D('Api/UserAddress');
		$districtId = (int)I("districtId");
		$communitys = $m->getCommunitys($districtId);	
		$this->ajaxReturn($communitys);
			
	}
	
	/**
	 * 获取区县
	 */
	public function getDistrictsOption(){
		
		$m = D('Api/UserAddress');
		$areaId2 = (int)I("areaId2");
		$communitys = $m->getDistrictsOption($areaId2);	
		$this->ajaxReturn($communitys);
			
	}
	
	/**
	 * 获取社区
	 */
	public function getCommunitysOption(){
		
		$m = D('Api/UserAddress');
		$districtId = (int)I("districtId");
		$communitys = $m->getCommunitysOption($districtId);	
		$this->ajaxReturn($communitys);
			
	}
	
	/**
	 * 获取店铺配送区县
	 */
	public function getShopDistricts(){
	
		$m = D('Api/UserAddress');
		$areaId2 = (int)I("areaId2");
		$shopId = (int)I("shopId");
		$communitys = $m->getShopDistricts($areaId2,$shopId);
		$this->ajaxReturn($communitys);
			
	}
	
	/**
	 * 获取店铺配送社区
	 */
	public function getShopCommunitys(){
	
		$m = D('Api/UserAddress');
		$districtId = (int)I("districtId");
		$shopId = (int)I("shopId");
		$communitys = $m->getShopCommunitys($districtId,$shopId);
		$this->ajaxReturn($communitys);
			
	}
	
};
?>