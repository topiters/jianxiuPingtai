<?php
 namespace Api\Action;;
/**
*  供应商控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月18日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class SupperlierAction extends BaseAction{
	
	public function __construct(){
		parent::__construct();
		$USER = session('WST_USER');
		if($USER['userType']!=2){
			$data["msg"] = '你还不是供应商,没有权限操作!';
			$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);
			$this->stringify($data);
	
	
		}
	
	
	}
};
?>