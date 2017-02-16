<?php
namespace Api\Action;
/**
*  业主控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月9日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class ShopsAction extends BaseAction {
	
public function __construct(){
		parent::__construct();
		  //$USER=D('users')->where(array('userId'=>49))->find();//测试写法
	     $USER = session('WST_USER');
		//var_dump($USER);
		if($USER['userType']!=1){
			$data["msg"] = '你还不是业主,没有权限操作!';
			$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);
			$this->stringify($data);
		}
		
		
	}
	//完善公司信息
public  function addByUser(){
	$USER = session('WST_USER');
	$userId=$USER['userId'];
	$shopsOne=M('shops')->where(array('userId'=>$userId))->find();
	if(!$shopsOne){
		$data["msg"] = '你还不是业主,没有权限操作!';
		$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);	
	}
	$shops=M('shops');
	$datas=array();
	//$shopImg=I('shopImg');//公司logo
	if($_FILES['shopImg']){
		$info=$this->uploadPic();
		if($info['status']==1){
		$shopImg=$info['savename'].$info['savethumbname'];
		}		
	}
	$shopCompany=$_POST['shopCompany'];//公司名称
	$goodsCatId1=$_POST['goodsCatId1'];//行业分类
	$shopTotal=$_POST['shopTotal'];//注册金额
	$shopInfo=$_POST['shopInfo'];//简介
	$shopArea=$_POST['shopArea'];//公司所在城市
	$shopAddr=$_POST['shopAddr'];//公司地址
	$latitude=$_POST['latitude'];  //经度
	$longitude=$_POST['longitude']; //纬度
	$shopHost=$_POST['shopHost'];//企业法人
	$shopUrl=$_POST['shopUrl'];//网址
	$shopIdentimg=$_POST['shopIdentimg'];//公司证明
	$datas['shopImg']=$shopImg;
    $datas['shopCompany']=$shopCompany;
    $datas['goodsCatId1']=$goodsCatId1;
    $datas['shopTotal']=$shopTotal;
    $datas['shopInfo']=$shopInfo;
    $datas['shopHost']=$shopHost;
    $datas['shopArea']=$shopArea;
    $datas['shopAddr']=$shopAddr;
    $datas['latitude']=$latitude;
    $datas['longitude']=$longitude;
    $shopIdentimg=array();
    //附件
    if($_FILES){
    	$shopIdentimgInfo=$this->uploads();
    	
    	if($shopIdentimgInfo){
    		foreach ($shopIdentimgInfo  as $k=>$v){
    			$imgs[]=$v['savepath'].$v['savethumbname'];
    		}
    		
    	}
    }
    $datas['shopIdentimg']=serialize($shopIdentimg);
    $datas['createTime']=time();
	
  $shopId=(int)$shopsOne['shopId'];//当前业主的id
 $result=M('shops')->where(array('shopId'=>$shopId))->save($datas);
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
	
	//任务发布页面
	public function addgoods(){

		$datas=array();
		$USER = session('WST_USER');
	$shopinfo=D("shops")->where(array('userId'=>$USER['userId']))->find();	
	if(!$shopinfo){//业主不存在
		$data["msg"] = '你还不是业主,没有权限操作!';
		$data = array('status'=>self::API_PERMISSION_NO_OPERATION,'msg'=>$data);
		$this->stringify($data);
		exit;
	    }	
		$goodsTaskId=I("post.goodsTaskId");//发布类型1.采购2.为检修
		 if(empty($goodsTaskId)){//发布类型不存在
			$data["msg"] = '发布类型未选择!';
			$data = array('status'=>self::API_INPUT_ERROR,'msg'=>$data);
			$this->stringify($data);
		}
		$datas['goodsTaskId']=$goodsTaskId;
		$goodsCatId1=$_POST['goodsCatId1'];
		if(!$goodsCatId1){//产品分类
			$data["msg"] = '产品分类未选择!';
			$data = array('status'=>self::API_INPUT_ERROR,'msg'=>$data);
			$this->stringify($data);
		}
		$datas['shopId']=$shopinfo['shopId'];//业主id
		$datas['goodsCatId1']=$goodsCatId1;// 产品分类
		//$datas['createTime']=date("Y-m-d H:i:s");
		$datas['createTimes']=date("Y-m-d");//发布时间
		$goodName=$_POST['goodName'];//产品名称
	 	if(empty($goodName)){//业主不存在
		
			$data["msg"] = '发布产品名称未填写!';
			$data = array('status'=>self::API_INPUT_ERROR,'msg'=>$data);
			$this->stringify($data);
		} 
		//$data['createTime']=time();
		//$goodsImg=I("goodsImg");//产品图
		//if($goodsImg){
			
		//}
		
		$goodsthumb=I("goodsThumbs");//多张图
		if($goodsthumb){
			$a=0;
	foreach($goodsthumb as $k=>$v){
		  $a++;
		if($a==1){
			$datas['goodsImg']=$v;//获取第一张
		}
	}
	   $datas['goodsThums']=serialize($goodsthumb);
		}
		
		
      $linkMan=I("linkMan");//联系人
	  $linkPhone=I("linkPhone");//手机号
	  $linkAddr=I("linkAddr");//地址
	   $datas['linkMan']=$linkMan;
	   $datas['linkPhone']=$linkPhone;
	   $datas['linkAddr']=$linkAddr;
		
		
		if($goodsTaskId==1){  //采购任务
		$goodsStock=$_POST['goodsStock'];//数量	
		if(empty($goodsStock)){//业主不存在
			$data["msg"] = '发布产品数量未填写!';
			$data = array('status'=>self::API_INPUT_ERROR,'msg'=>$data);
			$this->stringify($data);
		}
	    $attrCatId=$_POST['attrCatId'];//产品规格型号
	    if(empty($attrCatId)){//业主不存在
	    	$data["msg"] = '发布规格型号未选择!';
	    	$data = array('status'=>self::API_INPUT_ERROR,'msg'=>$data);
	    	$this->stringify($data);
	    } 
		$beginTime=I("beginTime");	//开始时间
		$endTime=I("endTime");//结束时间
		$goodsDesc=I("goodsDesc");//要求特殊
		
		$datas['goodsName']=$goodName;
		$datas['goodsStock']=$goodsStock;
		$datas['attrCatId']=$attrCatId;
		$datas['beginTime']=$beginTime;
		$datas['endTime']=$endTime;
		
		$datas['goodsDesc']=$goodsDesc;
		
		//$data['goodsImg']=$goodsImg;
		$res=D('goods')->add($datas);
		if($res){
			
			$data["msg"] = '数据添加成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$data);
			$this->stringify($data);
			
		}else{
    	$data["msg"] = '数据输入失败，请检查...!';
    	$data = array('status'=>self::API_ADD_FALSE,'msg'=>$data);
    	$this->stringify($data);	
    }
		 }elseif($goodsTaskId==2){
		$goodName=I("goodName");// 检修名称	
		$brandId=I("brandId");//	风机品牌厂家
		$attrCatId=I("attrCatId");//	设备型号
		$goodType=I("goodsType");//竞价1.0 2.枪弹
		
		if($goodType==2){
		$goodsworks=I("goodsworks");//工作人数	
		$shopPrice=I("shopPrice");// 检修报价
		$datas['shopPrice']=$shopPrice;
		}
	
		$goodDesc=I("goodDesc");//故障描述
		$datas['goodDesc']=$goodDesc;
	
		$repairId=I("repairId");//检修性质	
		$isVoice=I("isVoice");//是否发票1 是,0否
		$beginTime=I("beginTime");// 开始时间
		$endTime=I("endTime");// 结束时间
		$startTime=I("startTime");//故障发生时间
		$repairhistory=	I("repairhistory");//检修历史
		
		$datas['goodsName']=$goodName ;
		$datas['brandId']=$brandId ;
		$datas['attrCatId']=$attrCatId ;
		$datas['goodsType']=$goodType ;
		$datas['repairId']=$repairId ;
		$datas['beginTime']=$beginTime ;
		$datas['endTime']=$endTime ;
		$datas['startTime']=$startTime;
		$datas['repairhistory']=$repairhistory;
		//
		$res=D('goods')->add($datas);
		if($res){
				
			$data["msg"] = '数据添加成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$data);
			$this->stringify($data);
				
		}else{
    	$data["msg"] = '数据输入失败，请检查...!';
    	$data = array('status'=>self::API_ADD_FALSE,'msg'=>$data);
    	$this->stringify($data);	
    } 
	
		}
		
		
		
	}
	
	//发布检修或采购列表
	
	public  function  getshopgoodslist(){
		
		$shopsModel=D('Api/shops');
		$result=$shopsModel->getshopgoodslist();
		if($result['root']){
			//foreach ($result['root']  as $k=>$v){
				//if($result['root']['goodDesc']){
				//$result['root']['goodDesc']=WSTMSubstr($v['goodDesc'],0,10);
				//}
				/* 
				if($result['root']['createTime']){
					
					$time=$result['root']['createTime'];
					$result['root']['createTime']=date("Y-m-d",strtotime($time));
				}
				 */
				
			//}
			//exit;
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
			$this->stringify($data);
			
		}else{
			$data['msg']="暂时没有符合的数据...";
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
		}
		
		
		
		
	}
	
	
	//采购或检修详情页面  
	public function shopgoodsdetails(){
		  
		$result=D('Api/shops')->shopgoodsdetails();
		
		 if($result){
		 		
		 	$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
		 	$this->stringify($data);
		 		
		 }else{
		 	$data['msg']="暂时没有符合的数据...";
		 	$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
		 	$this->stringify($data);
		 		
		 		
		 }
		
		
		
		
	}
	
	//

	/**
     * 跳到业主首页面
     */
	public function toShopHome(){
		$mshops = D('Home/Shops');
		$shopId = (int)I('shopId');
		//如果沒有传店铺ID进来则取默认自营店铺
		if($shopId==0){
			$areaId2 = $this->getDefaultCity();
			$shopId = $mshops->checkSelfShopId($areaId2);
		}
		$shops = $mshops->getShopInfo($shopId);
		$shops["serviceEndTime"] = str_replace('.5',':30',$shops["serviceEndTime"]);
		$shops["serviceEndTime"] = str_replace('.0',':00',$shops["serviceEndTime"]);
		$shops["serviceStartTime"] = str_replace('.5',':30',$shops["serviceStartTime"]);
		$shops["serviceStartTime"] = str_replace('.0',':00',$shops["serviceStartTime"]);
		$this->assign('shops',$shops);

		if(!empty($shops)){		
			$this->assign('shopId',$shopId);
			$this->assign('ct1',(int)I("ct1"));
			$this->assign('ct2',(int)I("ct2"));
			$this->assign('msort',(int)I("msort",1));
			$this->assign('mdesc',I("mdesc",0));
			$this->assign('sprice',I("sprice"));//上架开始时间
			$this->assign('eprice',I("eprice"));//上架结束时间
			$this->assign('goodsName',urldecode(I("goodsName")));//上架结束时间
					
			$mshopscates = D('Home/ShopsCats');
			$shopscates = $mshopscates->getShopCateList($shopId);
			$this->assign('shopscates',$shopscates);
			
			$mgoods = D('Home/Goods');
			$shopsgoods = $mgoods->getShopsGoods($shopId);
			$this->assign('shopsgoods',$shopsgoods);
			//获取评分
			$obj = array();
			$obj["shopId"] = $shopId;
			$shopScores = $mshops->getShopScores($obj);
		
			$this->assign("shopScores",$shopScores);
			
			$m = D('Home/Favorites');
			$this->assign("favoriteShopId",$m->checkFavorite($shopId,1));
			$this->assign('actionName',ACTION_NAME);
		
			$this->assign('isSelf',$shops["isSelf"]);
		
		}
        $this->display("default/shop_home");
	}
	/**
     * 跳到店铺街
     */
	public function toShopStreet(){
		$areas= D('Home/Areas');
		$areaId2 = $this->getDefaultCity();
   		$areaList = $areas->getDistricts($areaId2);
   		$mshops = D('Home/Shops');
   		$obj = array();
   		if((int)cookie("bstreesAreaId3")){
   			$obj["areaId3"] = (int)cookie("bstreesAreaId3");
   		}else{
   			$obj["areaId3"] = ((int)I('areaId3')>0)?(int)I('areaId3'):$areaList[0]['areaId'];
   			cookie("bstreesAreaId3",$obj["areaId3"]);
   		}

  		$this->assign('areaId3',$obj["areaId3"]);
   		$this->assign('keyWords',I("keyWords"));
   		$this->assign('areaList',$areaList);
        $this->display("default/shop_street");
	}
	
	/**
     * 获取县区内的商铺
     */
	public function getDistrictsShops(){
   		$mshops = D('Home/Shops');
   		$obj["areaId3"] = (int)I("areaId3");
   		$obj["shopName"] = WSTAddslashes(I("shopName"));
   		$obj["deliveryStartMoney"] = (float)I("deliveryStartMoney");
   		$obj["deliveryMoney"] = (float)I("deliveryMoney");
   		$obj["shopAtive"] = (int)I("shopAtive");
   		cookie("bstreesAreaId3",$obj["areaId3"]);
   		
   		$dsplist = $mshops->getDistrictsShops($obj);
   		$this->ajaxReturn($dsplist);
	}
	
	/**
     * 获取社区内的商铺
     */
	public function getShopByCommunitys(){
		
   		$mshops = D('Home/Shops');
   		$obj["communityId"] = (int)I("communityId");
   		$obj["areaId3"] = (int)I("areaId3");
   		$obj["shopName"] = WSTAddslashes(I("shopName"));
   		$obj["deliveryStartMoney"] = (float)I("deliveryStartMoney");
   		$obj["deliveryMoney"] = (float)I("deliveryMoney");
   		$obj["shopAtive"] = (int)I("shopAtive",-1);
   		$ctplist = $mshops->getShopByCommunitys($obj);
   		$pages = $rslist["pages"];

   		$this->assign('ctplist',$pages);
       	$this->ajaxReturn($ctplist);
       	
	}
	
    /**
     * 跳到业主登录页面
     */
	public function login(){
		$USER = session('WST_USER');
		if(!empty($USER) && $USER['userType']==1){
			$this->redirect("Shops/index");
		}else{
            $this->display("default/shop_login");
		}
	}
	
	/**
	 * 业主登录验证
	 */
	public function checkLogin(){
		$rs = array('status'=>-2);
	    $rs["status"]= 1;
		if(!$this->checkVerify("4") && ($GLOBALS['CONFIG']["captcha_model"]["valueRange"]!="" && strpos($GLOBALS['CONFIG']["captcha_model"]["valueRange"],"3")>=0)){			
			$rs["status"]= -2;//验证码错误
		}else{
			$m = D('Home/Shops');
	   		$rs = $m->login();
	   		if($rs['status']==1){
	    		session('WST_USER',$rs['shop']);
	    		unset($rs['shop']);
	    	}
		}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 退出
	 */
	public function logout(){
		session('WST_USER',null);
		echo "1";
	}
	/**
	 * 跳到业主中心页面
	 */
	public function index(){
		$this->isShopLogin();
		$spm = D('Home/Shops');
		$data['shop'] = $spm->loadShopInfo(session('WST_USER.userId'));
		$obj["shopId"] = $data['shop']['shopId'];
		$details = $spm->getShopDetails($obj);
		$data['details'] = $details;
		
		$this->assign('shopInfo',$data);
		
		$this->display("default/shops/index");
	}
	/**
	 * 编辑业主资料
	 */
	public function toEdit(){
		
		if($_FILES){
			
			$info=$this->uploads();
			
			var_dump($info);
			
		}
		$m = D('Home/Shops');
		 
		
		//获取银行列表
		$m = D('Admin/Banks');
		$this->assign('bankList',$m->queryByList(0));
		//获取商品信息
		
		//$this->assign('object',$shop);
		$this->assign("umark","toEdit");
		$this->display("default/shops/edit_shop");
	}
	
	/**
	 * 设置业主资料
	 */
	public function toShopCfg(){
		$this->isShopLogin();
        $USER = session('WST_USER');
		//获取商品信息
		$m = D('Home/Shops');
		$this->assign('object',$m->getShopCfg((int)$USER['shopId']));
		$this->assign("umark","setShop");
		$this->display("default/shops/cfg_shop");
	}
	/**
	 * 查询店铺名称是否存在
	 */
	public function checkShopName(){
		$m = D('Home/Shops');
		$rs = $m->checkShopName(I('shopName'),(int)I('id'));
		echo json_encode($rs);
	}
	/**
	 * 新增/修改操作
	 */
	public function editShopCfg(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		$m = D('Home/Shops');
    	$rs = array('status'=>-1);
    	if($USER['shopId']>0){
    		$rs = $m->editShopCfg((int)$USER['shopId']);
    	}
    	$this->ajaxReturn($rs);
	}
	
   /**
	* 新增/修改操作
	*/
	public function edit(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		$m = D('Home/Shops');
    	$rs = array('status'=>-1);
    	if($USER['shopId']>0){
    		$rs = $m->edit((int)$USER['shopId']);
    	}
    	$this->ajaxReturn($rs);
	}
	
   /**
    * 跳到修改用户密码
    */
	public function toEditPass(){
		$this->isShopLogin();
		$this->assign("umark","toEditPass");
        $this->display("default/shops/edit_pass");
	}
	
	/**
	 * 申请开店
	 */
	public function toOpenShopByUser(){
		$this->isUserLogin();
		$USER = session('WST_USER');
		if(!empty($USER) && $USER['userType']==0){
			//获取用户申请状态
			$m = D('Home/Shops');
			$shop = $m->checkOpenShopStatus((int)$USER['userId']);
			
			if(empty($shop)){
				//获取商品分类信息
				$m = D('Home/GoodsCats');
				$this->assign('goodsCatsList',$m->queryByList());
				//获取地区信息
				$m = D('Home/Areas');
				$this->assign('areaList',$m->getProvinceList());
				//获取所在城市信息
		        $cityId = $this->getDefaultCity();
		        $area = $m->getArea($cityId);
		        $this->assign('area',$area);
				//获取银行列表
				$m = D('Home/Banks');
				$this->assign('bankList',$m->queryByList(0));
				$object = $m->getModel();
				$object['areaId1'] = $area['parentId'];
				$object['areaId2'] = $area['areaId'];
				$this->assign('object',$object);
				$this->display("default/users/open_shop");
			}else{
				if($shop["shopStatus"]==1){
					$shops = $m->loadShopInfo((int)$USER['userId']);
					$USER = array_merge($USER,$shops);
					session('WST_USER',$USER);
					$this->assign('msg','您的申请已通过，请刷新页面后点击右上角的"卖家中心"进入店铺界面.');
					$this->display("default/users/user_msg");
				}else{
					if($shop["shopStatus"]==-1){
						$this->assign('msg','您的申请审核不通过【原因：'.$shop["statusRemarks"].'】,请<a style="color:blue;" href="'.U('Home/Shops/toEditShopByUser').'"> 点击这里 </a>进行修改！');
					}else{
						$this->assign('msg','您的申请正在审核中...');
					}
					$this->display("default/users/user_msg");
				}
			}
		}else{
			$this->redirect("Shops/index");
		}
	}
	
	/**
	 * 申请开店
	 */
	public function toEditShopByUser(){
		$this->isUserLogin();
		$USER = session('WST_USER');
		if(!empty($USER) && $USER['userType']==0){
			//获取用户申请状态
			$sm = D('Home/Shops');
			$shop = $sm->checkOpenShopStatus((int)$USER['userId']);
				
			if($shop["shopStatus"]==-1){
				//获取商品分类信息
				$m = D('Home/GoodsCats');
				$this->assign('goodsCatsList',$m->queryByList());
				//获取地区信息
				$m = D('Home/Areas');
				$this->assign('areaList',$m->getProvinceList());
				//获取所在城市信息
				$cityId = $this->getDefaultCity();
				//$area = $m->getArea($cityId);
				//$this->assign('area',$area);
				//获取银行列表
				$m = D('Home/Banks');
				$this->assign('bankList',$m->queryByList(0));
				//$object = $m->getModel();
				$object = $sm->getShopByUser((int)$USER['userId']);

				$this->assign('object',$object);
				$this->display("default/users/open_shop");
			}
		}else{
			$this->redirect("Shops/index");
		}
	}
	
	/**
	 * 会员提交开店申请
	 */
	public function openShopByUser(){
		$this->isUserLogin();
		$rs = array('status'=>-1);
		if($GLOBALS['CONFIG']['phoneVerfy']==1){
			$verify = session('VerifyCode_userPhone');
			$startTime = (int)session('VerifyCode_userPhone_Time');
			$mobileCode = I("mobileCode");
			if((time()-$startTime)>120){
				 $rs['msg'] = '验证码已失效!';
			}
			if($mobileCode=="" || $verify != $mobileCode){
				$rs['msg'] = '验证码错误!';
			}
    	}else{
	    	if(!$this->checkVerify("1")){			
				$rs['msg'] = '验证码错误!';
			}
    	}
    	if($rs['msg']==''){
			$USER = session('WST_USER');
			$m = D('Home/Shops');
	    	$userId = (int)$USER['userId'];
	    	$shop = $m->getShopByUser($userId);
	    	if($shop['shopId']>0){
	    		
	    		$rs = $m->edit((int)$shop['shopId'],true);
	    	}else{
			 	//如果用户没注册则先建立账号
				if($userId>0){
			   	    $rs = $m->addByUser($userId);
			    	if($rs['status']>0)$USER['shopStatus'] = 0;
				}
	    	}
    	}
    	$this->ajaxReturn($rs);
	}
	
	
	/**
	 * 游客跳到开店申请
	 */
    public function toOpenShop(){
    	//获取商品分类信息
		$m = D('Home/GoodsCats');
		$this->assign('goodsCatsList',$m->queryByList());
		//获取省份信息
		$m = D('Home/Areas');
		$this->assign('areaList',$m->getProvinceList());
		//获取所在城市信息
		$cityId = $this->getDefaultCity();
		$area = $m->getArea($cityId);
		$this->assign('area',$area);
		//获取银行列表
		$m = D('Home/Banks');
		$this->assign('bankList',$m->queryByList(0));
		$object = $m->getModel();
		$this->assign('object',$object);
		$this->display("default/open_shop");

	}
	
    /**
	 * 游客提交开店申请
	 */
	public function openShop(){
		$m = D('Home/Shops');
    	$rs = array('status'=>-1);
    	if($GLOBALS['CONFIG']['phoneVerfy']==1){
	    	$verify = session('VerifyCode_userPhone');
			$startTime = (int)session('VerifyCode_userPhone_Time');
			$mobileCode = I("mobileCode");
			if((time()-$startTime)>120){
			    $rs['msg'] = '验证码已失效!';
		    }
			if($mobileCode=="" || $verify != $mobileCode){
				$rs['msg'] = '验证码错误!';
			}
    	}else{
	    	if(!$this->checkVerify("1")){			
				$rs['msg'] = '验证码错误!';
			}
    	}
    	if($rs['msg']==''){
			$rs = $m->addByVisitor();
			$m = D('Home/Users');
			$user = $m->get($rs['userId']);
			if(!empty($user))session('WST_USER',$user);
    	}
    	$this->ajaxReturn($rs);
	}

	/**
	 * 获取店铺搜索提示列表
	 */
	public function getKeyList(){
		$m = D('Home/Shops');
		$areaId2 = $this->getDefaultCity();
		$rs = $m->getKeyList($areaId2);
		$this->ajaxReturn($rs);
	}

    /**
     * 对供应商留言
     */
    public function message() {
        $USER = session('WST_USER');
        $userId = $USER['userId'];
        $id = I('supplierId');
        $datas = array();
        $datas['userId'] = $userId;
        $datas['supplierId'] = $id;
        $datas['content'] = I('content');
        $datas['createTime'] = date('Y-m-d H:i:s' , time());
        $result = D('supplier_message')->add($datas);
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