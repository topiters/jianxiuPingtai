$(function() {
		var HEIGHT = $(document).height();
		$(".jx-alert1").height(HEIGHT);
		$(".jj-alert1 , .jj-alert2 ,.qd-alert1 ,.qd-alert2").height(HEIGHT);
		//	限制名称的字数
		$(".mingcheng").each(function() {
			var maxwidth = 8;
			if($(this).text().length > maxwidth) {
				$(this).text($(this).text().substring(0, maxwidth));
				$(this).html($(this).html() + '…');
			}
		});
		//		倒计时
		updateEndTime();
	})
	//倒计时函数
function updateEndTime() {
	var date = new Date();
	var time = date.getTime(); //当前时间距1970年1月1日之间的毫秒数
	$(".settime").each(function(i) {
		var endDate = this.getAttribute("endTime"); //结束时间字符串
		//转换为时间日期类型
		var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/, function(a) {
			return parseInt(a, 10) - 1;
		}).match(/\d+/g) + ')');

		var endTime = endDate1.getTime(); //结束时间毫秒数
		//当前时间和结束时间之间的秒数
		var lag = (endTime - time) / 1000;
		if(lag > 0) {
			var second = Math.floor(lag % 60);
			var minite = Math.floor(lag / 60 % 60);
			var hour = Math.floor(lag / 3600);
			hh = checkTime(hour);
			mm = checkTime(minite);
			ss = checkTime(second);
			$(this).html(hh + ":" + mm + ":" + ss);
		} else {
			$(this).removeClass("settime");
			//					alert($(this).attr("id"));
			$(this).html("已结束啦!");
		}
	});
	setTimeout("updateEndTime()", 1000);
}

function checkTime(i) {
	if(i < 10) {
		i = "0" + i;
	}
	return i;
}