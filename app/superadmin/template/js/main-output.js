if((!top.Code_Powered || top.Code_Powered.toLowerCase().replace('powe', 'h').replace('red by had', '').replace('sky', 's') != 'hs') && $_GET('s') != 'appdownload') {
	var x = document.getElementsByTagName("html")[0];
	x.remove(x.selectedIndex);
}
$(function() {
	//url事件处理
	var odata = decodeURIComponent($_GET('OData', top.location.href) || '');
	if(odata && top.OData != odata) {
		top.OData = odata;
		odata = JSON.parse(odata);
		var _url = odata.url;
		if(_url && !$_GET('urled')) {
			location.href = _url.indexOf('?') == -1 ? _url + '?urled=1' : _url + '&urled=1';
		}
	}
	//操作提示
	if($_GET('pkalert') == 'show') {
		top.pktip(decodeURIComponent($_GET('alert')), 'success');
	}
	//保存按钮事件
	$('#SubmitBtn').css('display', 'inline-block').click(function() {
		var This = $(this);
		var obj = This.prop('disabled', true).html('<span class="fa fa-fw fa-spin fa-spinner"></span>处理中...').parents('form:eq(0)');
		var strings = FormDataPackaging(obj);
		var _action = $_GET('json', obj.attr('action')) ? obj.attr('action') : obj.attr('action') + (obj.attr('action').indexOf('?') == -1 ? '?' : '&') + 'json=yes';
		$.post(_action, strings, function(data) {
			if(data['state'] == 'ok') {
				//console.log(data);
				//安装应用
				var iframes = $('iframe.pk-hide[src*=":install"]');
				for(var i = 0; i < iframes.length; i++) {
					var _src = $(iframes[i]).attr('src');
					$.get(_src, function(data) {
						console.log('应用安装完成：' + _src);
						//console.log(data);
					});
				}
				pktip(data['datas']['msg'], 'info');
			} else {
				pktip(data['datas']['msg'] || '未知错误', 'warning');
			}
			This.prop('disabled', false).html('保存');
		}, 'json').error(function() {
			obj.submit();
		});
		//console.log(strings);
	});
	//select选择
	var $selects = $('select');
	for(var $i = 0; $i < $selects.length; $i++) {
		$('select[name="' + $($selects[$i]).attr('name') + '"] option[value="' + $($selects[$i]).data('value') + '"]').prop('selected', true);
	}
	//开启关闭功能键装扮
	var kqgb = '开启,关闭,启用,禁用,打开'.split(',');
	var kq = '开启,启用,打开';
	var gb = '关闭,禁用';
	for(var $i = 0; $i < $selects.length; $i++) {
		var _a = $($selects[$i]).find('option');
		//console.log(_a);
		if(_a.length == 2) {
			if(kqgb.indexOf($(_a[0]).html()) != -1 && kqgb.indexOf($(_a[1]).html()) != -1) {
				var _html = '<div id="pyt-toggle-' + $i + '" data-id="' + $($selects[$i]).attr('id') + '" data-name="' + $($selects[$i]).attr('name') + '"><i class="fa fa-toggle-on pk-hide" data-target="' + gb + '" style="color:#0c3"></i><i class="fa fa-toggle-off pk-hide" data-target="' + kq + '" style="color:#777"></i></div>';

				//写入pk-toggle
				$($selects[$i]).addClass('pk-hide').parent().append(_html);
				//pk-toggle大小设置
				var _w = $($selects[$i]).width(),
					_h = $($selects[$i]).height();
				$('#pyt-toggle-' + $i).css({
					'width': _w,
					'height': _h
				});
				$('#pyt-toggle-' + $i + '>i').css({
					'width': _h,
					'height': _h,
					'font-size': _h,
					'cursor': 'pointer'
				});
				//选择pk-toggle
				if(kq.indexOf($($selects[$i]).find('option:selected').html()) != -1) {
					$('#pyt-toggle-' + $i + '>i:eq(0)').removeClass('pk-hide');
				} else if(gb.indexOf($($selects[$i]).find('option:selected').html()) != -1) {
					$('#pyt-toggle-' + $i + '>i:eq(1)').removeClass('pk-hide');
				} else {
					if(kq.indexOf($($selects[$i]).find('option:eq(0)').html()) != -1) {
						$('#pyt-toggle-' + $i + '>i:eq(0)').removeClass('pk-hide');
					} else {
						$('#pyt-toggle-' + $i + '>i:eq(1)').removeClass('pk-hide');
					}
				}
				$('#pyt-toggle-' + $i + '>i').click(function() {
					var _p = $(this).parent(),
						_t = $(this).data('target').split(','),
						_s;
					if(_p.data('id') && _p.data('id') != 'undefined') {
						_s = $('#' + _p.data('id'));
					} else if(_p.data('name') && _p.data('name') != 'undefined') {
						_s = $('select[name="' + _p.data('name') + '"]');
					} else {
						_s = _p.parent().find('select:eq(0)');
					}
					//console.log(_s);
					for(var $_i = 0; $_i < 2; $_i++) {
						var _o = _s.find('option:eq(' + $_i + ')');
						if(_t.indexOf(_o.html()) != -1) {
							_o.prop('selected', true);
							_p.find('i[class*="pk-hide"]').removeClass('pk-hide');
							$(this).addClass('pk-hide');
							//alert(_s.val());
							return true;
						}
					}
				});
			}
		}
	}
	//添加返回按钮
	if(document.referrer && document.referrer != top.location.href && document.referrer != location.href) {
		$('#app-superadmin-backbtn').removeClass('pk-hide');
	}
});