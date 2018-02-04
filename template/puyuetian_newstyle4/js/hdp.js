/*
	名称：幻灯片JS
	作者：蒲乐天
	联系：QQ632827168
	基于：jquery
*/
$(function() {
	//幻灯片总数
	var $hdpcount = $('#hdp>a').length;
	//切换速度
	var $hdpspeed = 4000;
	if($hdpcount) {
		//初始化,添加a链接,添加数字按钮
		var $hdpas = $('#hdp>a');
		var $hdpspan = '';
		$('#hdp').append('<div></div><div></div>');
		for(var $i = 0; $i < $hdpcount; $i++) {
			$('#hdp>div:eq(1)').append('<a target="_blank" href="' + $hdpas[$i].href + '" title="' + $hdpas[$i].title + '">' + $hdpas[$i].title + '</a>');
			if(!$i) {
				$hdpspan = '<span class="active">1</span>&nbsp;';
				$('#hdp>a:eq(0)').addClass('active');
				$('#hdp>div:eq(1)>a:eq(0)').addClass('active');
			} else {
				$hdpspan += '<span>' + ($i + 1) + '</span>&nbsp;';
			}
		}
		$('#hdp>div:eq(1)').append('<div>' + $hdpspan + '</div>');
		//自动切换
		setInterval(function() {
			//获取下一个图片标号
			var $hdpcurrent = parseInt($('#hdp>div:eq(1)>div:eq(0)>span.active').html());
			//防止溢出
			if($hdpcurrent == $hdpcount || $hdpcurrent > $hdpcount || $hdpcurrent < 0 || !$hdpcurrent) {
				$hdpcurrent = 0;
			}
			$('#hdp>a').removeClass('active');
			$('#hdp>div:eq(1)>a').removeClass('active');
			$('#hdp>div:eq(1)>div:eq(0)>span').removeClass('active');
			$('#hdp>a:eq(' + $hdpcurrent + ')').addClass('active');
			$('#hdp>div:eq(1)>a:eq(' + $hdpcurrent + ')').addClass('active');
			$('#hdp>div:eq(1)>div:eq(0)>span:eq(' + $hdpcurrent + ')').addClass('active');
		}, $hdpspeed);
		//为数字按钮添加事件
		$('#hdp>div:eq(1)>div:eq(0)>span').mouseover(function() {
			//获取当前图片标号
			var $hdpcurrent = parseInt($(this).html()) - 1;
			$('#hdp>a').removeClass('active');
			$('#hdp>div:eq(1)>a').removeClass('active');
			$('#hdp>div:eq(1)>div:eq(0)>span').removeClass('active');
			$('#hdp>a:eq(' + $hdpcurrent + ')').addClass('active');
			$('#hdp>div:eq(1)>a:eq(' + $hdpcurrent + ')').addClass('active');
			$('#hdp>div:eq(1)>div:eq(0)>span:eq(' + $hdpcurrent + ')').addClass('active');
		});
	}
});