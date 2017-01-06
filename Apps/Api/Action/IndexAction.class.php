<?php
namespace Api\Action;
/**
* 首页控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月3日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class IndexAction extends BaseAction {
	/**
	 * 获取首页信息
	 * 
	 */
    public function index(){
    	$ads = D('Api/Ads');
    	$areas= D('Api/Areas');
    	$goods= D('Api/Goods');
    	$areaId2 = $this->getDefaultCity();
    	$currArea = $areas->getArea($areaId2);
    	//var_dump($areaId2);
        $catAds = $ads->getAdsByCat($areaId2);//获取广告
       // var_dump($areaId2);
       $indexNewsGoods= $goods->getNewList($areaId2);//新品
       //var_dump($indexNewsGoods);
        //exit;
    	//header("Content-Type:application/javascript");
        $data = array();
        $data['cityName']=$currArea['areaName'];//获取地区
        $data['adv']     =$catAds['-4'];
        $data['newGoods']=$indexNewsGoods ;
    	$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$data);
    	$this->stringify($data);
    	
    	
   		
   		//
   		//获取分类
		//$gcm = D('Api/GoodsCats');
		//$catList = $gcm->getGoodsCatsAndGoodsForIndex($areaId2);
		//$this->assign('catList',$catList);
   		//分类广告
   		
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