/**
 * **********************************首页**********************************
 */
	var gallery = mui('.mui-slider');
	gallery.slider({
		interval: 2000 //自动轮播周期，若为0则不自动播放，默认为0；
	});
	$(function() {
		//限制字符个数
		$(".cut_str , .mingcheng").each(function() {
			var maxwidth = 10;
			if($(this).text().length > maxwidth) {
				$(this).text($(this).text().substring(0, maxwidth));
				$(this).html($(this).html() + '…');
			}
		});
		function preventDefaultFn(event) {
			event.preventDefault();
		}

		$('.jx-alert1').click(function() {
			$(this).fadeOut(500);
			$('body').off('touchmove', preventDefaultFn);
			$('.jx-alert1-t').fadeOut(500);
			$('body').removeAttr('style');
		})
		$(".cha").click(function() {
			$(".jx-alert1").hide();
		});
/**
 * 发布按钮判断是否登陆过
 */
		var mphones = localStorage.getItem("mphone");
		var loginPwds = localStorage.getItem("loginPwd");
		$("#issue").click(function(){
			console.log(mphones+loginPwds);
			if( mphones != "" && loginPwds != ""){
				$('.jx-alert1').fadeIn(500);
				$('.jx-alert1-t').fadeIn(500);
				$('body').css('overflow', 'hidden');
				$('body').on('touchmove', preventDefaultFn);
			}else{
				mui.alert('未登录，您还没有权限。请登录...', '提示', function() {
					mui.openWindow({
					    url:"b-login2.html",
					});
				});
			}
		});

/**
 * 判断是否登陆过，localStorage是否有值
 */
		var messages = document.getElementById('messages'); //消息
		var jianxiu = document.getElementById('jianxiu'); //检修
		var gongyingshang = document.getElementById('gongyingshang'); //供应商
		var yezhu = document.getElementById('yezhu'); //业主
		var box1 = document.getElementById('box1'); //登录
		var box2 = document.getElementById('box2'); //登录  
		mui.ajax(myUrl+'index.php?m=Api&c=Users&a=checkLogin', {
			data: {
				loginName: mphones,
				loginPwd: loginPwds
			},
			dataType: 'json', //服务器返回json格式数据
			type: 'post', //HTTP请求类型 
			timeout: 60000, //超时时间设置为10秒；                
			success: function(data) {
				if(data.status == 200) {
//					mui.toast('登录成功');
					var userType = data.msg.userType;
					if(userType==1){
						messages.style.display = "block";
						yezhu.style.display = "block";
						box1.style.display = "none";
						box2.style.display = "none";	
					}else if(userType==2){
						messages.style.display = "block";
						gongyingshang.style.display = "block";
						box1.style.display = "none";
						box2.style.display = "none";
					}else if(userType==3 || userType==4){
						messages.style.display = "block";
						jianxiu.style.display = "block";
						box1.style.display = "none";
						box2.style.display = "none";
					}
				} else {
					mui.toast(data.msg.msg);
				}
			}
		});
		
//		if( mphones != "" && loginPwds != ""){
//			messages.style.display = "block";
//			jianxiu.style.display = "block";
//			box1.style.display = "none";
//			box2.style.display = "none";
//		}else{
//			messages.style.display = "none";
//			jianxiu.style.display = "none";
//			box1.style.display = "block";
//			box2.style.display = "block";
//		}
	});
/**
 * 轮播图数据请求
 */
	var myUrl="http://test.cnceshi.com/";
	mui.ajax(myUrl+'index.php?m=Api&c=Index&a=index',{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:60000,//超时时间设置为10秒；
		success:function(data){
			//服务器返回响应，根据响应结果，分析是否获取数据成功；
			if( data.status == 200 ){
				var sliderImgs=document.getElementsByClassName("sliderImgs");
				for(var i=0; i<sliderImgs.length;i++){
					var imgs=data.msg.adv;
					for(var j=0 ;j<imgs.length;j++){
						sliderImgs[j].setAttribute("src",myUrl+imgs[j].adFile);
						sliderImgs[j].parentNode.href=imgs[j].adURL;
					}
				}
			}else{
				alert("数据请求失败，请刷新页面")
			} 
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			console.log(type);
		}
	});
/**
 * 上拉加载，配合init代码
 * 首页推荐任务
 */
	var rootnode = $('[dataGaoFour = "rootcon"]');
	var childnode = $('[dataGaoOne = "rootchild"]').clone();
	$('[dataGao = "rootchild"]').remove();
	var i = 1;
 	function pullFresh() {
 		setTimeout(function() {
		    //业务逻辑代码，比如通过ajax从服务器获取新数据；
		    mui.ajax(myUrl+'index.php?m=Api&c=goods&a=getGoodsDetails',{
				data:{
					p: i
				},
				dataType:'json',//服务器返回json格式数据
				type:'post',//HTTP请求类型
				timeout:60000,//超时时间设置为10秒；
				success:function(data){
					//服务器返回响应，根据响应结果，分析是否获取数据成功；
					if( data.status == 200 ){
						console.log(JSON.stringify(data.msg.root))
						$commonObj.loadListData({
							sourceDataList : data.msg.root,
							dataListRootNode : rootnode,
							dataListChildNode : childnode,
							dataAttrName : "dataGao",
							dataHandleFn : function(){
//								for(var i=0;i<mySrc1.length;i++){
//									for (var j=0;j<mySrc.length;j++){
//										var myStr = myURL+mySrc1[j].innerHTML; 
//										mySrc[j].setAttribute("src",myStr);
//									}	
//								}
//							//点击事件
//								for(var j=0;j<mylist.length;j++){
//									mylist[j].addEventListener('tap',function(){
//										var id = $(this).children("p").html();
//										mui.openWindow({
//									    	url:"d-jiangbiao-dai.html?id="+id
//										})
//									});	
//								}
							}
						})
						i++;
					}else{
						alert("数据请求失败，请刷新页面")
					} 
				},
				error:function(xhr,type,errorThrown){
					//异常处理；
					console.log(type);
				}
			});
	//	     加载完新数据后，必须执行如下代码，true表示没有更多数据了：
		    var count = 0;
		    mui('#pullRef').pullRefresh().endPullupToRefresh((++count > 2));
	    },1000)
	}
 	

//采购任务跳转进入详情页面
	document.getElementById('buyingTask').addEventListener('tap',function() {
		mui.openWindow({
		    url:"a-caigourenwu.html",
		});
	});
//检修任务跳转进入详情页面
	document.getElementById('maintenanceTask').addEventListener('tap',function() {
		mui.openWindow({
		    url:"a-jianxiurenwu.html",
		});
	});
//高价悬赏跳转进入详情页面
	document.getElementById('highPrice').addEventListener('tap',function() {
		mui.openWindow({
		    url:"a-gaojiaxuanshang.html",
		});
	});
//附近GO跳转进入详情页面
	document.getElementById('nearby').addEventListener('tap', function() {
		mui.openWindow({
		    url:"a-fujin-yezhu01.html",
		});
	});
	
	$(function() {
		var t;
		t = setInterval(function() {
			var ul = $(".list");
			var liHeight = ul.find("li:last").height();
			ul.animate({
				marginTop: 0
					//						liHeight + "px"
			}, 1000, function() {
				ul.find("li:last").prependTo(ul);
				ul.find("li:first").hide();
				ul.css({
					marginTop: 0
				});
				ul.find("li:first").fadeIn(800);
			});
		}, 3000);
	});
/**
 * **********************************消息***登录**********************************
 */
	var messages = document.getElementById('messages'); //消息
	var jianxiu = document.getElementById('jianxiu'); //检修
	var gongyingshang = document.getElementById('gongyingshang'); //供应商
	var yezhu = document.getElementById('yezhu'); //业主
	var box1 = document.getElementById('box1'); //登录
	var box2 = document.getElementById('box2'); //登录
	(function($, doc) {
		$.plusReady(function() {
			var loginbtn = document.getElementById('l_btn'); //点击登录按钮
			var forgetpasswd = document.getElementById('forgetpwd'); //点击忘记密码
			var register = document.getElementById('reg_btn'); //注册
			var p_login = document.getElementById('p_login'); //手机登录
			var weixinlogin = document.getElementById('weixinlogin'); //微信登录
			var regphone = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
			//点击登录
			var myUrl = "http://test.cnceshi.com/";
			loginbtn.addEventListener('tap', function() {
				var mphone = document.getElementById('phone').value; //手机号
				var loginPwd = document.getElementById('paslogin').value; //密码
				localStorage.setItem("mphone",mphone);
				localStorage.setItem("loginPwd",loginPwd);
				if(mphone == "" || loginPwd == "") {
					mui.toast('请将信息填写完整')
					return false;
				} else if(!regphone.test(mphone)) {
					mui.toast('手机号错误！');
					return false;
				} else {
					mui.ajax(myUrl+'index.php?m=Api&c=Users&a=checkLogin', {
						data: {
							loginName: mphone,
							loginPwd: loginPwd
						},
						dataType: 'json', //服务器返回json格式数据
						type: 'post', //HTTP请求类型 
						timeout: 60000, //超时时间设置为10秒；                
						success: function(data) {
							if(data.status == 200) {
								mui.toast('登录成功');
								var userType = data.msg.userType;
								if(userType==1){
									messages.style.display = "block";
									yezhu.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";	
								}else if(userType==2){
									messages.style.display = "block";
									gongyingshang.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";
								}else if(userType==3 || userType==4){
									messages.style.display = "block";
									jianxiu.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";
								}
							} else {
								mui.toast(data.msg.msg);
							}
						}
					});
				}

			});
			//点击忘记密码
			//				forgetpasswd.addEventListener('tap', function() {
			//					$.openWindow({
			//						url: 'forgetpasswd.html',
			//						id: 'forgetpasswd',
			//						preload: true,
			//						show: {
			//							aniShow: 'pop-in'
			//						}
			//					});
			//				});
			//手机登录
			p_login.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login3.html'
				});
			});
			//点击注册
			register.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login5.html'
				});
			});
			//QQ登录
			qqlogin.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login1.html'
				});
			});
			//微信登录
			weixinlogin.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login4.html'
				});
			});
		});

	})(mui, document);
/**
 * **********************************个人中心***登录**********************************
 */
	(function($, doc) {
		$.plusReady(function() {
			var loginbtn1 = document.getElementById('l_btn1'); //点击登录按钮
			var forgetpasswd1 = document.getElementById('forgetpwd1'); //点击忘记密码
			var register1 = document.getElementById('reg_btn1'); //注册
			var p_login1 = document.getElementById('p_login1'); //手机登录
			var weixinlogin1 = document.getElementById('weixinlogin1'); //微信登录
			var qqlogin1 = document.getElementById('qqlogin1'); //QQ登录
			var regphone1 = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
			//点击登录
			var myUrl = "http://test.cnceshi.com/";
			loginbtn1.addEventListener('tap', function() {
				var mphone1 = document.getElementById('phone1').value; //手机号
				var loginPwd1 = document.getElementById('paslogin1').value; //密码
				if(mphone1 == "" || loginPwd1 == "") {
					mui.toast('请将信息填写完整')
					return false;
				} else if(!regphone1.test(mphone1)) {
					mui.toast('手机号错误！');
					return false;
				} else {
					mui.ajax(myUrl+'index.php?m=Api&c=Users&a=checkLogin', {
						data: {
							loginName: mphone1,
							loginPwd: loginPwd1
						},
						dataType: 'json', //服务器返回json格式数据
						type: 'post', //HTTP请求类型 
						timeout: 60000, //超时时间设置为10秒；                
						success: function(data) {
							if(data.status == 200) {
								localStorage.setItem("mphone",mphone1);
								localStorage.setItem("loginPwd",loginPwd1);
								mui.toast('登录成功');
								var userType = data.msg.userType;
								if(userType==1){
									messages.style.display = "block";
									yezhu.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";	
								}else if(userType==2){
									messages.style.display = "block";
									gongyingshang.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";
								}else if(userType==3 || userType==4){
									messages.style.display = "block";
									jianxiu.style.display = "block";
									box1.style.display = "none";
									box2.style.display = "none";
								}
							} else {
								mui.toast(data.msg.msg);
							}
						}
					});
				}

			});
			//点击忘记密码
			//				forgetpasswd.addEventListener('tap', function() {
			//					$.openWindow({
			//						url: 'forgetpasswd.html',
			//						id: 'forgetpasswd',
			//						preload: true,
			//						show: {
			//							aniShow: 'pop-in'
			//						}
			//					});
			//				});
			//手机登录
			p_login1.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login3.html'
				});
			});
			//点击注册
			register1.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login5.html'
				});
			});
			//QQ登录
			qqlogin1.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login1.html'
				});
			});
			//微信登录
			weixinlogin1.addEventListener('tap', function() {
				mui.openWindow({
					url: 'b-login4.html'
				});
			});
		});

	})(mui, document);
/**
 * **********************************业主**********************************
 * 个人信息
 */
	$("#myFabu").click(function() {
		$(".jx-alert122").fadeIn(500);
		var hh=$(window).height();
		$(".jx-alert122").css("height",hh+"px") 
	});
	$(".cha").click(function() {
		$(".jx-alert122").fadeOut(500);
	});
		
/**
 * 各页面跳转
 */
//我的询价跳转进入详情页面
	document.getElementById('myXunjia').addEventListener('tap', function() {
		mui.openWindow({
		    url:"d-myxujia1.html",
		});
	});
//我的钱包跳转进入详情页面
	document.getElementById('myWallet').addEventListener('tap', function() {
		mui.openWindow({
		    url:"d-mywallet.html",
		});
	});
//地址管理跳转进入详情页面
	document.getElementById('myAddress').addEventListener('tap', function() {
		mui.openWindow({
		    url:"d-adress2.html",
		});
	});
//设置跳转进入详情页面
	document.getElementById('mySetting').addEventListener('tap', function() {
		mui.openWindow({
		    url:"d-set.html",
		});
	});
//个人信息跳转进入详情页面
	document.getElementById('myMessage').addEventListener('tap', function() {
		mui.openWindow({
		    url:"d-message-qiye.html",
		});
	});
