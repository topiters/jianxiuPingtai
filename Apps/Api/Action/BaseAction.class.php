<?php
namespace Api\Action;
/**
*  xx控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月3日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
use Think\Controller;
class BaseAction extends Controller {
	//请求成功,并返回数据
	const API_REQUEST_SUCCESS   = 200;
	//已经登录提示
	const API_LOGIN_ALREADY     = 201;
	//登录成功提示
	const API_LOGIN_SUCCESS     = 202;
	//登录信息错误
	const API_LOGIN_ERROR       = 203;
	//登录账号不能为空
	const API_LOGIN_ACCOUNT_ERROR = 204;
	//登录密码不能为空
	const API_LOGIN_PSWD_ERROR = 205;
	//注册密码不一致
	const API_REG_PSWD_ERROR = 206;
	//输入错误
	const API_INPUT_ERROR       = 301;
	//数据库错误
	const API_DB_ERROR          = 303;
	//验证码错误
	const API_SCODE_ERROR       = 304;
	//验证码或密码不匹配
	const API_NOTSAME_ERROR     = 305;
	//手机号已存在
	const API_PHONE_EXIST_ERROR = 306;
	//错误的手机号
	const API_PHONE_ERROR       = 307;
	//优惠卷
	const API_COUPON_ERROR      = 308;
	//该验证码已经过期
	const API_CODE_EXPIRES    = 309;
	//
	const API_COUPON_NONUM      = 310;
	//
	const API_COUPON_LIMITED    = 311;
	//
	const API_COUPON_OWNSHIP    = 312;
	//
	const API_COUPON_NO_EXSITS    = 313;
	//404
	const API_PAGE_NO_EXSITS    = 404;
	//未登录或登录状态不正确！
	const API_LOGIN_NO_REG    = 314;
	//未登录或登录状态不正确！
	const API_REG_NO_FIND    = 315;
	//创建失败
	const API_ADD_FALSE    = 316;
	//修改失败
	const API_EDIT_FALSE    = 317;
	//删除失败
	const API_DELETE_FALSE    = 318;
	//无权限操作
	const API_PERMISSION_NO_OPERATION   = 319;
	//表单传值错误
	const API_FROM_FALSE   = 320;
	//数据不存在
	const API_DETAIL_NO_EXSITS    = 321;
	//没有有效的支付记录！
	const API_LOGS_NO_PAYS    = 322;
	//两次密码输入不一致！
	const API_PWD_NO_AGREE    = 323;
	//原密码输入不正确！
	const API_PWD_NO_FALSE    = 324;
	//您已经关注过该了！
	const API_FAVOR_IS_TRUE   = 325;
	//您未关注！
	const API_FAVOR_IS_FALSE  = 326;
	//验证码为空
	const API_SCODE_EMPTY     = 327;
	//验证码不匹配
	const API_SCODE_NOTSAME   = 328;
	//用户名不存在
	const API_USER_NOT_EXISTS = 329;
	const API_USER_NO_ACCESS  = 330;
	//请求的数据不存在。。。
	const API_DATA_NOT_EXISTS  = 331;
	
	public function __construct(){
		parent::__construct();
		//初始化系统信息
		$m = D('Api/System');
		$GLOBALS['CONFIG'] = $m->loadConfigs();
		//var_dump($GLOBALS['CONFIG']);
		//WSTAutoByCookie();
		//$this->assign("WST_USER",session('WST_USER'));
	//	$this->assign("WST_IS_LOGIN",(session('WST_USER.userId')>0)?1:0);
		   $areas= D('Api/Areas');
	   $areaId2 = $this->getDefaultCity();
	   $currArea = $areas->getArea($areaId2);
	//var_dump($currArea);
		//$this->assign('currArea',$currArea);
   		//$this->assign('searchType',(int)I("searchType",1));
   		//$this->assign('CONF',$GLOBALS['CONFIG']);
   		//$this->assign('WST_REFERE',$_SERVER['HTTP_REFERER']);
		//$this->footer(); //加入底部
	}
	
	/**
	 * 空操作处理
	 */
    public function _empty($name){
        $this->assign('msg',"你的思想太飘忽，系统完全跟不上....");
        $this->display('default/sys_msg');
    }
	/**
     * ajax程序验证,只要不是会员都返回-999
     */
    public function isUserLogin() {
    	$USER = session('WST_USER');
		if (empty($USER) || ($USER['userId']=='')){
			if(IS_AJAX){
				$this->ajaxReturn(array('status'=>-999,'url'=>'Users/login'));
			}else{
				$this->redirect("Users/login");
			}
		}
	}
	/**
	 * 商家ajax登录验证
	 */
	public function isShopLogin(){
		$USER = session('WST_USER');
		if (empty($USER) || $USER['userType']==0){
			if(IS_AJAX){
				$this->ajaxReturn(array('status'=>-999,'url'=>'Shops/login'));
			}else{
				$this->redirect("Shops/login");
			}
		}
	}
	/**
	 * 用户登录验证-主要用来判断会员和商家共同功能的部分
	 */
	public function isLogin($userType = 'Users'){
		$USER = session('WST_USER');
		if (empty($USER)){
			if(IS_AJAX){
			    $this->ajaxReturn(array('status'=>-999,'url'=>$userType.'/login'));
			}else{
				$this->redirect($userType."/login");
			}
		}
   }
   /**
    * 检查登录状态
    */
   public function checkLoginStatus(){
   	   $USER = session('WST_USER');
	   if (empty($USER)){
	   	    die("{status:-999}");
	   }else{
	   		die("{status:1}");
	   }
   }
   /**
	 * 验证模块的码校验
	 */
	public function checkVerify($type){
		if(stripos($GLOBALS['CONFIG']['captcha_model'],$type) !==false) {
			$verify = new \Think\Verify();
			return $verify->check(I('verify'));
		}else{
			return true;
		}
		return false;
	}
	
    /**
     * 核对单独的验证码
	 * $re = false 的时候不是ajax返回
	 * @param  boolean $re [description]
	 * @return [type]      [description]
	 */
	public function checkCodeVerify($re = true){
		$code = I('code');
		$verify = new \Think\Verify(array('reset'=>false));    
		$rs =  $verify->check($code);		
		if ($re == false) return $rs;
		else $this->ajaxReturn(array('status'=>(int)$rs));
	}
    /**
	 * 单个上传图片
	 */
    public function uploadPic(){
	   $config = array(
		        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
		        'exts'          =>  array('jpg','png','gif','jpeg'), //允许上传的文件后缀
		        'rootPath'      =>  './Upload/', //保存根路径
		        'driver'        =>  'LOCAL', // 文件上传驱动
		        'subName'       =>  array('date', 'Y-m'),
		        'savePath'      =>  I('dir','uploads')."/"
		);
	   	$dirs = explode(",",C("WST_UPLOAD_DIR"));
	   	if(!in_array(I('dir','uploads'), $dirs)){
	   		echo '非法文件目录！';
	   		return false;
	   	}

		$upload = new \Think\Upload($config);
		$rs = $upload->upload($_FILES);
		$Filedata = key($_FILES);
		if(!$rs){
			$this->error($upload->getError());
		}else{
			$images = new \Think\Image();
			$images->open('./Upload/'.$rs[$Filedata]['savepath'].$rs[$Filedata]['savename']);
			$newsavename = str_replace('.','_thumb.',$rs[$Filedata]['savename']);
			$vv = $images->thumb(I('width',300), I('height',300))->save('./Upload/'.$rs[$Filedata]['savepath'].$newsavename);
		    if(C('WST_M_IMG_SUFFIX')!=''){
		        $msuffix = C('WST_M_IMG_SUFFIX');
		        $mnewsavename = str_replace('.',$msuffix.'.',$rs[$Filedata]['savename']);
		        $mnewsavename_thmb = str_replace('.',"_thumb".$msuffix.'.',$rs[$Filedata]['savename']);
			    $images->open('./Upload/'.$rs[$Filedata]['savepath'].$rs[$Filedata]['savename']);
			    $images->thumb(I('width',700), I('height',700))->save('./Upload/'.$rs[$Filedata]['savepath'].$mnewsavename);
			    $images->thumb(I('width',250), I('height',250))->save('./Upload/'.$rs[$Filedata]['savepath'].$mnewsavename_thmb);
			}
			//$rs[$Filedata]['savepath'] = "Upload/".$rs[$Filedata]['savepath'];
			//$rs[$Filedata]['savethumbname'] = $newsavename;
			$rs['savepath'] = "Upload/".$rs[$Filedata]['savepath'];
			$rs['savethumbname'] = $mnewsavename_thmb;
			$rs['status'] = 1;
			
			//echo json_encode($rs);
			return $rs;

		}	
    }
	
    
    /*
     * 多文件上传
     * 
     */
    public  function uploads(){
    	
    	
    	$config = array(
    			'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
    			'exts'          =>  array('jpg','png','gif','jpeg'), //允许上传的文件后缀
    			'rootPath'      =>  './Upload/', //保存根路径
    			'driver'        =>  'LOCAL', // 文件上传驱动
    			'subName'       =>  array('date', 'Y-m'),
    			'savePath'      =>  I('dir','uploads')."/"
    	);
    	$dirs = explode(",",C("WST_UPLOAD_DIR"));
    	if(!in_array(I('dir','uploads'), $dirs)){
    		echo '非法文件目录！';
    		return false;
    	}
    	
    	$upload = new \Think\Upload($config);
    	$info =$upload->upload($files='');
    	if(!$info) {// 上传错误提示错误信息  
    		$this->error($upload->getError());
    	   }else{// 上传成功 获取上传文件信息    
    			/* foreach($info as $file){   
    				echo $file['savepath'].$file['savename']; 
    			} */ 
    			
    			return  $info;
    			
    		}
    	
    }
	/**
	 * 产生验证码图片
	 * 
	 */
	public function getVerify(){
		// 导入Image类库
    	$Verify = new \Think\Verify();
    	$Verify->length   = 4;
    	$Verify->entry();
    }
   /**
	 * 页尾参数初始化
	 */
	public function footer(){
		$m = D('Api/Friendlinks');
		$friendLikds = $m->getFriendLinks();
		$this->assign('friendLikds',$friendLikds);
		$m = D('Api/Articles');
		$helps = $m->getHelps();
		$this->view->assign("helps",$helps);
	}
	/**
	 * 设置所在城市
	 */
	public function setDefaultCity($cityId){
		setcookie("areaId2", $cityId, time()+3600*24*90);
	}
	/**
	 * 定位所在城市
	 */
	public function getDefaultCity(){
		$areas= D('Api/Areas');
		return $areas->getDefaultCity();
	}
	

	/**
	 * 返回所有参数
	 */
	function WSTAssigns(){
		$params = I();
		$this->assign("params",$params);
	}
	
	//序列化数据
	protected function stringify($data=null)
	{
		if(!$data) return false;
		exit(json_encode($data));
	}
	//反序列化数据
	protected function parse($data=null)
	{
		if(!$data)return false;
		return json_decode($data, true);
	}
	
	
	
	
}