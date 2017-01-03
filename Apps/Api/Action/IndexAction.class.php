<?php
namespace Api\Action;
/**
 * ============================================================================
 * 
 * 官网地址:http://www.wstmall.net
 * 联系QQ:707563272
 * ============================================================================
 * 首页控制器
 */
class IndexAction extends BaseAction {
	/**
	 * 获取首页信息
	 * 
	 */
    public function index(){
   		$ads = D('Api/Ads');
   		//$areaId2 = $this->getDefaultCity();
   		//获取分类
		//$gcm = D('Api/GoodsCats');
		//$catList = $gcm->getGoodsCatsAndGoodsForIndex($areaId2);
		//$this->assign('catList',$catList);
   		//分类广告
   		//$catAds = $ads->getAdsByCat($areaId2);
   		//$this->assign('catAds',$catAds);
   		//$this->assign('ishome',1);
   		/* if(I("changeCity")){
   			echo $_SERVER['HTTP_REFERER'];
   		}else{
   		//	$this->display("default/index");
   		} */
    }
    /**
     * 广告记数
     */
    public function access(){
    	$ads = D('Api/Ads');
    	$ads->statistics((int)I('id'));
    }
    /**
     * 切换城市
     */
    public function changeCity(){
    	$m = D('Api/Areas');
    	$areaId2 = $this->getDefaultCity();
    	$provinceList = $m->getProvinceList();
    	$cityList = $m->getCityGroupByKey();
    	$area = $m->getArea($areaId2);
    	$this->assign('provinceList',$provinceList);
    	$this->assign('cityList',$cityList);
    	$this->assign('area',$area);
    	$this->assign('areaId2',$areaId2);
    	$this->display("default/change_city");
    }
    /**
     * 跳到用户注册协议
     */
    public function toUserProtocol(){
    	$this->display("default/user_protocol");
    }
    
    /**
     * 修改切换城市ID
     */
    public function reChangeCity(){
    	$this->getDefaultCity();
    }
    
}