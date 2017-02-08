<?php
 namespace Api\Action;;
/**
*  检修企业控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月18日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class ManufacturerAction extends BaseAction{
	
	public function __construct(){
		parent::__construct();
		$USER = session('WST_USER');
		if($USER['userType']!=3){
			$data["msg"] = '你还不是检修企业,没有权限操作!';
			$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);
			$this->stringify($data);
	
	
		}
	
	
	}
	
	
	
	
	//完善公司信息
	public  function addByUser(){
		$USER = session('WST_USER');
		$userId=$USER['userId'];
		$manufacturersOne=M('manufacturer')->where(array('userId'=>$userId))->find();
		if(!$manufacturersOne){
			$data["msg"] = '你还不是业主,没有权限操作!';
			$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);
		}
		$manufacturers=M('manufacturer');
		$datas=array();
		//$manufacturerImg=I('manufacturerImg');//公司logo
		if($_FILES['manufacturerImg']){
			$info=$this->uploadPic();
			if($info['status']==1){
				$manufacturerImg=$info['savename'].$info['savethumbname'];
			}
		}
		$manufacturerCompany=$_POST['manufacturerCompany'];//公司名称
		$goodsCatId1=$_POST['goodsCatId1'];//行业分类
		$manufacturerTotal=$_POST['manufacturerTotal'];//注册金额
		$manufacturerInfo=$_POST['manufacturerInfo'];//简介
		$manufacturerArea=$_POST['manufacturerArea'];//公司所在城市
		$manufacturerAddr=$_POST['manufacturerAddr'];//公司地址
		$latitude=$_POST['latitude'];  //经度
		$longitude=$_POST['longitude']; //纬度
		$manufacturerHost=$_POST['manufacturerHost'];//企业法人
		$manufacturerUrl=$_POST['manufacturerUrl'];//网址
		$manufacturerIdentimg=$_POST['manufacturerIdentimg'];//公司证明
		$datas['manufacturerImg']=$manufacturerImg;
		$datas['manufacturerCompany']=$manufacturerCompany;
		$datas['goodsCatId1']=$goodsCatId1;
		$datas['manufacturerTotal']=$manufacturerTotal;
		$datas['manufacturerInfo']=$manufacturerInfo;
		$datas['manufacturerHost']=$manufacturerHost;
		$datas['manufacturerArea']=$manufacturerArea;
		$datas['manufacturerAddr']=$manufacturerAddr;
		$datas['latitude']=$latitude;
		$datas['longitude']=$longitude;
		$manufacturerIdentimg=array();
		//附件
		if($_FILES){
			$manufacturerIdentimgInfo=$this->uploads();
			 
			if($manufacturerIdentimgInfo){
				foreach ($manufacturerIdentimgInfo  as $k=>$v){
					$imgs[]=$v['savepath'].$v['savethumbname'];
				}
			}
		}
		$datas['manufacturerIdentimg']=serialize($manufacturerIdentimg);
		$datas['createTime']=time();
	
		$manufacturerId=(int)$manufacturersOne['manufacturerId'];//当前业主的id
		$result=M('manufacturer')->where(array('manufacturerId'=>$manufacturerId))->save($datas);
		if($result!==false){
			$data["msg"] ='数据保存成功';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$data);
			$this->stringify($data);
			 
		}else{
			$data["msg"] = '数据没有更新，请更新后保存...!';
			$data = array('status'=>self::API_UPDATE_FALSE,'msg'=>$data);
			$this->stringify($data);
		}
	
	}
		
	
	
	
	
	
	
	
	
	
	
	
};
?>