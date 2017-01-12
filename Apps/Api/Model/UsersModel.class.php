<?php
namespace Api\Model;
/**
*  用户model控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月6日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class UsersModel extends BaseModel {
	
     /**
	  * 获取用户信息
	  */
     public function get($userId=0){

	 	$userId = intval($userId?$userId:I('id',0));
		$user = $this->where("userId=".$userId)->find();
		if(!empty($user) && $user['userType']==1){
			//加载业主信息
		 	$sp = M('shops');
		 	$shops = $sp->where('userId='.$user['userId']." and shopFlag=1")->find();
		 	if(!empty($shops))$user = array_merge($shops,$user);
		}
		
		
		
		return $user;
	 }
	 
	/**
	  * 获取用户信息
	  */
     public function getUserInfo($loginName,$loginPwd){
		$loginPwd = md5($loginPwd);
	 	$rs = $this->where(" loginName ='%s' AND loginPwd ='%s' ",array($loginName,$loginPwd))->find();
	    return $rs;
	 }
	 
	/**
	  * 获取用户信息
	  */
     public function getUserById($obj){
		$userId = (int)$obj["userId"];
	 	$rs = $this->where(" userId ='%s' ",array($userId))->find();
	    return $rs;
	 }
	 
 	/**
	  * 查询登录名是否存在
	  */
	 public function checkLoginKey($loginName,$id = 0,$isCheckKeys = true){
	 	$loginName = WSTAddslashes(($loginName!='')?$loginName:I('loginName'));
	 	$rd = array('status'=>-1);
	 	if($loginName=='')return $rd;
	 	if($isCheckKeys){
		 	if(!WSTCheckFilterWords($loginName,$GLOBALS['CONFIG']['limitAccountKeys'])){
		 		$rd['status'] = -2;
		 		return $rd;
		 	}
	 	}
	 	$sql = " (loginName ='%s' or userPhone ='%s' or userEmail='%s') and userFlag=1 ";

	    if($id>0){
	 		$sql.=" and userId!=".$id;
	 	}
	 	$rs = $this->where($sql,array($loginName,$loginName,$loginName))->count();
	    if($rs==0){
	    	$rd['status'] = 1;
	    }
	    return $rd;
	 }
	 
	 /**
	  * 查询并加载用户资料
	  */
	 public function checkAndGetLoginInfo($key){
	 	if($key=='')return array();
	 	$sql = " (loginName ='%s' or userPhone ='%s' or userEmail='%s') and userFlag=1 and userStatus=1 ";
	 	$keyArr = array($key,$key,$key);
	 	$rs = $this->where($sql,$keyArr)->find();
	    return $rs;
	 }
	 
    /**
	 * 用户登录验证
	 */
	public function checkLogin(){
		$rv = array('status'=>-1);//账号不存在
		$loginName = WSTAddslashes(I('loginName'));
		$userPwd = WSTAddslashes(I('loginPwd'));
		//$rememberPwd = I('rememberPwd');
		$sql ="SELECT * FROM __PREFIX__users WHERE (loginName='".$loginName."' OR userEmail='".$loginName."' OR userPhone='".$loginName."') AND userFlag=1 and userStatus=1 ";
		$rss = $this->query($sql);
		//[loginSecret] => 2269
        //    [loginPwd] => 2b4422f9a8032911f9e70198758c1c8a
		if(!empty($rss)){
			
			$rs = $rss[0];
			//print_r(md5($userPwd.$rs['loginSecret']));exit;
			if($rs['loginPwd']!=md5($userPwd.$rs['loginSecret'])){ $rv = array('status'=>-2);return $rv;}//密码错误
			if($rs['userStatus']!=1){ $rv = array('status'=>-3);return $rv;}//禁用
			
			if($rs['userFlag'] == 1 && $rs['userStatus']==1){
				
				$data = array();
				$data['lastTime'] = date('Y-m-d H:i:s');
				$data['lastIP'] = get_client_ip();
		    	$this->where(" userId=".$rs['userId'])->data($data)->save();
		    	
		    	//如果是业主则加载业主信息
		    	if($rs['userType']==1){
		    		$s = M('shops');
			 		  $shops = $s->where('userId='.$rs['userId']." and shopFlag=1")->find();
			 		  if(!empty($shops))$rs = array_merge($shops,$rs);
		    	}
		    	
		    	//如果是检修厂商
		    	if($rs['userType']==2){
		    		$super = M('supperlier');
		    		$superOne = $super->where('userId='.$rs['userId']." and supperlierFlag=1")->find();
		    		if(!empty($superOne))$rs = array_merge($superOne,$rs);
		    	}
		    	
		    	//如果是检修企业
		    	if($rs['userType']==3){
		    		$manu = M('manufacturer');
		    		$manuOne = $super->where('userId='.$rs['userId']." and manufacturerFlag=1")->find();
		    		if(!empty($manuOne))$rs = array_merge($manu,$rs);
		    	}
		    	//如果是检修工程师
		    	if($rs['userType']==4){
		    		$manu = M('engineer');
		    		$manuOne = $super->where('userId='.$rs['userId']." and engineerFlag=1")->find();
		    		if(!empty($manuOne))$rs = array_merge($manu,$rs);
		    	}
		    	
		    	
		    	
		    	//记录登录日志
				$data = array();
				$data["userId"] = $rs['userId'];
				$data["loginTime"] = date('Y-m-d H:i:s');
				$data["loginIp"] = get_client_ip();
				M('log_user_logins')->add($data);
				
			    $rv = $rs;
			  
				//记住密码
				setcookie("loginName", $loginName, time()+3600*24*90);
				if($rememberPwd == "on"){
					$datakey = md5($rs['loginName'])."_".md5($rs['loginPwd']);
					$key = C('COOKIE_PREFIX')."_".$rs['loginSecret'];
					//加密
					$base64 = new \Think\Crypt\Driver\Base64();
					$loginKey = $base64->encrypt($datakey, $key);		
					setcookie("loginPwd", $loginKey, time()+3600*24*90);
				}else{		
					setcookie("loginPwd", null);
				}
				
				
				
				
			}
		}
		
	
		return $rv;
	}
	
	/**
	 * 根据cookie自动登录
	 */
	public function autoLoginByCookie(){
		$loginName = WSTAddslashes($_COOKIE['loginName']);
		$loginKey = $_COOKIE['loginPwd'];
		if($loginKey!='' && $loginName!=''){
			$sql ="SELECT * FROM __PREFIX__users WHERE (loginName='".$loginName."' OR userEmail='".$loginName."' OR userPhone='".$loginName."') AND userFlag=1 and userStatus=1 ";
		    $rs = $this->queryRow($sql);
		    if(!empty($rs) && $rs['userFlag'] == 1 && $rs['userStatus']==1){
		    	//用数据库的记录记性加密核对
			    $datakey = md5($rs['loginName'])."_".md5($rs['loginPwd']);
				$key = C('COOKIE_PREFIX')."_".$rs['loginSecret'];
					
				$base64 = new \Think\Crypt\Driver\Base64();
				$compareKey = $base64->encrypt($datakey, $key);
				//验证成功的话则补上登录信息
				if($compareKey==$loginKey){
					$data = array();
				    $data['lastTime'] = date('Y-m-d H:i:s');
				    $data['lastIP'] = get_client_ip();
				    $m = M('users');
		    	    $m->where(" userId=".$rs['userId'])->data($data)->save();
		    	    //如果是店铺则加载店铺信息
		    	    if($rs['userType']>=1){
		    		     $s = M('shops');
			 		     $shops = $s->where('userId='.$rs['userId']." and shopFlag=1")->find();
			 		     $shops["serviceEndTime"] = str_replace('.5',':30',$shops["serviceEndTime"]);
		                 $shops["serviceEndTime"] = str_replace('.0',':00',$shops["serviceEndTime"]);
		                 $shops["serviceStartTime"] = str_replace('.5',':30',$shops["serviceStartTime"]);
		                 $shops["serviceStartTime"] = str_replace('.0',':00',$shops["serviceStartTime"]);
			 		     $rs = array_merge($shops,$rs);
		    	    }
		    	    //记录登录日志
				    $data = array();
				    $data["userId"] = $rs['userId'];
				    $data["loginTime"] = date('Y-m-d H:i:s');
				    $data["loginIp"] = get_client_ip();
				    M('log_user_logins')->add($data);
				    session('WST_USER',$rs);
				}
			}
		}
	}
	 
	/**
	 * 会员注册
	 */
    public function regist(){
    	$rd = array('status'=>-1);	   
    	$udata = array();
    	$udata['loginName'] =WSTAddslashes(I(" "));
    	$udata['loginPwd'] = I("loginPwd");
    	//$data['reUserPwd'] = I("reUserPwd");
    	//$data['protocol'] = (int)I("protocol");
    	$loginName = $udata['loginName'];
     
       
    	/* if($data['loginPwd']!=$data['reUserPwd']){
    		$rd['status'] = -3;
    		$rd['msg'] = '两次输入密码不一致!';
    		return $rd;
    	}
    	if($data['protocol']!=1){
    		$rd['status'] = -6;
    		$rd['msg'] = '必须同意使用协议才允许注册!';
    		return $rd;
    	} */
    	foreach ($udata as $v){
    		if($v ==''){
    			$rd['status'] = -7;
    			$rd['msg'] = '注册信息不完整!';
    			return $rd;
    		}
    	}
	   
	
	    //检测账号，邮箱，手机是否存在
	    $udata["loginSecret"] = rand(1000,9999);
	    $udata['loginPwd'] = md5($udata['loginPwd'].$udata['loginSecret']);
	    $udata['userType'] = I('userType');
	    $udata['userName'] = I('userName');
	    $udata['userQQ'] = I('userQQ');
	    $udata['userPhone'] = I('userPhone');
	    //$data['userScore'] = I('userScore');
		//$data['userEmail'] = I("userEmail");
	    $udata['createTime'] = date('Y-m-d H:i:s');
	    $udata['userFlag'] = 1;
	    
	   
		$rs = $this->add($udata);
		if(false !== $rs){
			$rd['status']= 1;
			$rd['userId']= $rs;
	    	$data = array();
	    	$data['lastTime'] = date('Y-m-d H:i:s');
	    	$data['lastIP'] = get_client_ip();
	    	$this->where(" userId=".$rd['userId'])->data($data)->save();
	    	//业主
	    	if($udata['userType']==1){
	    		$shopinfo['userId']=$rs;
	    		M("shops")->add($shopinfo);	
	    	}
	    	
	    	//供应商
	    	if($udata['userType']==2){
	    		$shopinfo['userId']=$rs;
	    		M("supperlier")->add($shopinfo);
	    	}
	    	
	    //	3=>检修企业,4=>检修工程师
	    	
	    	if($udata['userType']==3){
	    		$manufacturerinfo['userId']=$rs;
	    		M("manufacturer")->add($manufacturerinfo);
	    	}
	    	
	    	if($udata['userType']==4){
	    		$engineerinfo['userId']=$rs;
	    		M("engineer")->add($engineerinfo);
	    	}
	    	
	    	//记录登录日志
		 	$data = array();
			$data["userId"] = $rd['userId'];
			$data["loginTime"] = date('Y-m-d H:i:s');
			$data["loginIp"] = get_client_ip();
			$m = M('log_user_logins');
			$m->add($data);
	    	
	    } 
		return $rd;
	}
	
	/**
	 * 随机生成一个账号
	 */
	public function randomLoginName($loginName){
		$chars = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		//简单的派字母
		foreach ($chars as $key =>$c){
			$crs = $this->checkLoginKey($loginName."_".$c);
			if($crs['status']==1)return $loginName."_".$c;
		}
		//随机派三位数值
		for($i=0;$i<1000;$i++){
			$crs = $this->checkLoginKey($loginName."_".$i);
			if($crs['status']==1)return $loginName."_".$i;
		}
		return '';
	}
	
	/**
	 * 查询用户手机是否存在
	 */
    public function checkUserPhone($userPhone){
    	
    	$rd = array('status'=>-3);
		$sql =" userFlag=1 and userPhone='".$userPhone."'";
		$rs = $this->where($sql)->count();
	
	    if($rs==1){
	    	$rd['status'] = 1;
	    }//存在
	    return $rd;
	}
	
	/**
	 * 修改用户密码
	 */
	public function editPass($id){
		$rd = array('status'=>-1);
		$data = array();
		$data["loginPwd"]= I("newPass");//新密码
		$rePwd = I("rePass");//确认密码
		  $oldPass     = I("oldPass");//旧密码
		  
		 if($rePwd!=$data["loginPwd"]){ 
		 	  $rd['status']= 3;
		 } 
		  
		if($this->checkEmpty($data,true)){
			$rs = $this->where('userId='.$id)->find();
			//核对密码
			if($rs['loginPwd']==md5(I("oldPass").$rs['loginSecret'])){
				$data["loginPwd"] = md5(I("newPass").$rs['loginSecret']);
				$rs = $this->where("userId=".$id)->save($data);
				if(false !== $rs){
					$rd['status']= 1;//
				}
			}else{
				$rd['status']= -2;//密码不正确
			}
		}
		return $rd;
	}
	
	/**
	 * 修改用户资料
	 */
	public function editUser($obj){
		$rd = array('status'=>-1);
		$userPhone = I("userPhone");
		$userEmail = I("userEmail");
		$userId = (int)$obj["userId"];
	    //检测账号是否存在
        $crs = $this->checkLoginKey($userPhone,$userId,false);
        if($crs['status']!=1){
	    	$rd['status'] = -2;
	    	return $rd;
	    }
	    //检测邮箱是否存在
        $crs = $this->checkLoginKey($userEmail,$userId,false);
        if($crs['status']!=1){
	    	$rd['status'] = -3;
	    	return $rd;
	    }
		$data = array();
		$data["userName"] = I("userName");
		$data["userQQ"] = I("userQQ");
		$data["userPhone"] = $userPhone;
		$data["userSex"] = (int)I("userSex",0);
		$data["userEmail"] = $userEmail;
		$data["userPhoto"] = I("userPhoto");
		$rs = $this->where(" userId=".$userId)->data($data)->save();
	    if(false !== $rs){
			$rd['status']= 1;
			$WST_USER = session('WST_USER');
			$WST_USER['userName'] = $data["userName"];
			$WST_USER['userQQ'] = $data["userQQ"];
			$WST_USER['userSex'] = $data["userSex"];
			$WST_USER['userPhone'] = $data["userPhone"];
			$WST_USER['userEmail'] = $data["userEmail"];
			$WST_USER['userPhoto'] = $data["userPhoto"];
			session('WST_USER',$WST_USER);
		}
		return $rd;
	}
	
	/**
	 * 重置用户密码
	 */
	public function resetPass(){
		$rs = array('status'=>-1);
    	$reset_userId = (int)session('REST_userId');
    	if($reset_userId==0){
    		$rs['msg'] = '无效的用户！';
    		return $rs;
    	}
    	$user = $this->where("userId=".$reset_userId." and userFlag=1 and userStatus=1")->find();
    	if(empty($user)){
    		$rs['msg'] = '无效的用户！';
    		return $rs;
    	}
    	$loginPwd = I('loginPwd');
    	if(trim($loginPwd)==''){
    		$rs['msg'] = '无效的密码！';
    		return $rs;
    	}
    	$data['loginPwd'] = md5($loginPwd.$user["loginSecret"]);
    	$rc = $this->where("userId=".$reset_userId)->save($data);
    	if(false !== $rc){
    	    $rs['status'] =1;
    	}
    	session('REST_userId',null);
    	session('REST_Time',null);
    	session('REST_success',null);
    	session('findPass',null);
    	return $rs;
	}
	
	/**
	 * 检测第三方帐号是否已注册
	 */
	public function checkThirdIsReg($userFrom,$openId){
		$openId = WSTAddslashes($openId);
		$sql = "select userId, userName from __PREFIX__users where userFrom=$userFrom and openId='$openId'";
		$row = $this->queryRow($sql);
		if($row["userId"]>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 第三方注册
	 */
    public function thirdRegist($obj){
    	$rd = array('status'=>-1);	   
    	
    	$data = array();
    	$data['loginName'] = $this->randomLoginName(time());
	    $data["loginSecret"] = rand(1000,9999);
	    $data['loginPwd'] = "";
	    $data['userType'] = 0;
	    $data['userName'] = WSTAddslashes($obj["userName"]);
	    $data['userQQ'] = "";
	    $data['userPhoto'] = WSTDownFile($obj["userPhoto"],'./Upload/users/'.date("Y-m/"));
	    $data['createTime'] = date('Y-m-d H:i:s');
	    $data['userFlag'] = 1;
	    $data['userFrom'] = $obj["userFrom"];
	    $data['openId'] = WSTAddslashes($obj["openId"]);
	   
		$rs = $this->add($data);
		if(false !== $rs){
			$rd['status']= 1;
			$rd['userId']= $rs;
	    	$data = array();
	    	$data['lastTime'] = date('Y-m-d H:i:s');
	    	$data['lastIP'] = get_client_ip();
	    	$this->where(" userId=".$rd['userId'])->data($data)->save();	 		
	    	//记录登录日志
		 	$data = array();
			$data["userId"] = $rd['userId'];
			$data["loginTime"] = date('Y-m-d H:i:s');
			$data["loginIp"] = get_client_ip();
			$m = M('log_user_logins');
			$m->add($data);
			
			$user = self::get($rd['userId']);
			if(!empty($user))session('WST_USER',$user);
	    } 
		return $rd;
	}
	/**
	 * 第三方登录
	 */
	public function thirdLogin($obj){
		$rd = array('status'=>-1);
		$openId = WSTAddslashes($obj['openId']);
		$sql = "select * from __PREFIX__users where userStatus=1 and userFlag=1 and userFrom=".$obj['userFrom']." and openId='".$openId."'";
		$row = $this->queryRow($sql);
		if($row["userId"]>0){
			if($row['userType']==1){
				$s = M('shops');
			 	$shops = $s->where('userId='.$row['userId']." and shopFlag=1")->find();
			    if(!empty($shops))$row = array_merge($shops,$row);			
			}
			session('WST_USER',$row);
			$rd["status"] = 1;
			//修改最后登录时间
		    $ldata = array();
		    $ldata['lastTime'] = date('Y-m-d H:i:s');
		    $ldata['lastIP'] = get_client_ip();
		    $this->where('userId='.$row['userId'])->save($ldata);
			//记录登录日志
			$data = array();
			$data["userId"] = $row['userId'];
			$data["loginTime"] = date('Y-m-d H:i:s');
			$data["loginIp"] = get_client_ip();
			M('log_user_logins')->add($data);
		}
		return $rd;
	}
	
}