var PytEditor;
var PytWindow;
var PytContent;
var PytContentValue;

function LoadPytEditor() {
	//iframe可编辑化
	PytWindow = document.getElementById("PytMainContent").contentWindow;
	PytEditor = PytWindow.contentDocument || PytWindow.document;
	PytEditor.write('<!DOCTYPE html><html style="height:100%"><head><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><meta name="renderer" content="webkit"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"><title>puyuetianEditor</title><meta name="author" content="puyuetian qq632827168"><meta name="website" content="http://www.hadsky.com"><meta http-equiv="Cache-Control" content="no-siteapp"><link rel="stylesheet" href="template/puyuetianUI/css/font-awesome.min.css"><link rel="stylesheet" href="template/puyuetianUI/css/puyuetian.css"><link rel="stylesheet" href="app/puyuetianeditor/template/css/PytEditor.css"><style>img{max-width: 100%}</style></head><body class="pk-word-break-all" style="height:100%"></body></html>');
	PytEditor.close();
	$(PytEditor.body).prop('spellcheck', false);
	PytEditor.body.contenteditable = true;
	if(PytEditor.designMode)
		PytEditor.designMode = "on";
	//表情驱动
	var emotionhtml = '';
	for(var $i = 1; $i < 33; $i++) {
		emotionhtml += '<img src="app/puyuetianeditor/template/img/emotion/' + $i + '.png" onclick="PytCmd(\'inserthtml\',false,\'<img width=\\\'32\\\' height=\\\'32\\\' src=\\\'app/puyuetianeditor/template/img/emotion/' + $i + '.png\\\' alt=\\\'emotion\\\'>\');PytSH(\'.PytDiv.Emotion\')">';
	}
	//自定义的表情列表
	for(var $i = 1; $i < 31; $i++) {
		emotionhtml += '<img src="app/puyuetianeditor/template/img/loveemotion/emoji-' + $i + '.png" onclick="PytCmd(\'inserthtml\',false,\'<img width=\\\'32\\\' height=\\\'32\\\' src=\\\'app/puyuetianeditor/template/img/loveemotion/emoji-' + $i + '.png\\\' alt=\\\'emotion\\\'>\');PytSH(\'.PytDiv.Emotion\')">';
	}
	emotionhtml += '<div class="pk-padding-top-10 pk-text-center"><button type="button" class="pk-btn pk-btn-default" onclick="PytSH(\'.PytDiv.Emotion\')">取消</button></div>';
	$('.PytDiv.Emotion').html(emotionhtml);
	//读取配置，开始加载按钮
	if(PytConfig) {
		var PytConfigArray = PytConfig.split(',');
		for($i = 0; $i < PytConfigArray.length; $i++) {
			$('#PytToolbar').append($('#PytToolbarBtns #Pyt' + PytConfigArray[$i] + 'Btn').parent('div').html());
		}
	}
	$('#PytToolbarBtns').html('');
	//赋予工具按钮点击特效及工具图标处理
	var PytToolbarSpans = $('#PytToolbar span');
	for($i = 0; $i < PytToolbarSpans.length; $i++) {
		$(PytToolbarSpans[$i]).attr('onclick', $(PytToolbarSpans[$i]).attr('onclick') + ';SthisBtn(this)').css({
			"background-image": "url(app/puyuetianeditor/template/img/toolicons/" + $(PytToolbarSpans[$i]).attr("id").toString().replace(/Pyt|Btn/g, '').toLowerCase() + ".png)"
		});
	}
}

//基本命令驱动
function PytCmd($p1, $p2, $p3) {
	PytEditor.body.focus();
	if(((PytEditor.selection && (navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) || (!!window.ActiveXObject || "ActiveXObject" in window)) && $p1 == 'inserthtml') {
		PytEditor.body.innerHTML += $p3;
	} else {
		PytEditor.execCommand($p1, $p2, $p3);
	}
}

function PytSH($id, event) {
	var e = event || window.event;
	if($($id).css('visibility') == 'hidden') {
		$('.PytDiv').css('visibility', 'hidden');
		$($id).css('visibility', 'visible');
	} else {
		$($id).css('visibility', 'hidden');
	}
	var $ls = 'Link,Emotion,Image,Video,Music,File,Myfiles,Replylook,Code';
	$ls = $ls.split(',');
	for(var i = 0; i < $ls.length; i++) {
		$('#PytToolbar #Pyt' + $ls[i] + 'Btn').removeClass('pk-active');
	}
	if($(window).width() > 1000) {
		var pw = $('#PytEditorDriveDiv').width();
		var dw = $(window).width();
		var l = e.pageX - ((dw - pw) / 2);
		if(l > (pw / 2)) {
			l = l - $($id).width();
		}
		$('.PytDiv').css({
			'left': l + 10,
			'top': 31
		});
	} else {
		var wh = $(window).height();
		var st = (wh - 260) / 2;
		$('.PytDiv').css({
			'left': 0,
			'top': st,
			'width': '100%',
			'position': 'fixed'
		});
	}
}

//查看html源码
function PytLookHtml() {
	if($('#PytMainContent2').css('display') == 'none') {
		$('#PytMainContent2').val(PytEditor.body.innerHTML);
		$('#PytMainContent').css('display', 'none');
		PytEditor.body.innerHTML = '';
		$('#PytMainContent2').css('display', '');
	} else {
		PytEditor.body.innerHTML = $('#PytMainContent2').val();
		$('#PytMainContent').css('display', '');
		$('#PytMainContent2').css('display', 'none');
		$('#PytMainContent2').val('');
	}
}

function PytInsertLink() {
	var url = trim($('#PytLinkUrl').val());
	var text = trim($('#PytLinkText').val());
	if(!url || url == 'http://') {
		pkalert('链接不能为空！');
		return false;
	}
	if(!text) {
		text = url;
	}
	PytCmd('inserthtml', false, '<a target="_blank" href="' + url + '">' + text + '</a>');
	$('#PytLinkUrl').val('http://');
	$('#PytLinkText').val('');
	PytSH('.PytDiv.Link');
}

function PytInsertImage() {
	var urls = $('#PytImagesBox img.pk-active');
	var w = parseInt($('#PytImageWidth').val());
	var h = parseInt($('#PytImageHeight').val());
	if(!urls.length) {
		pkalert('请选择要插入的图片后再点击！');
		return false;
	}
	if(!w) {
		w = '';
	} else {
		w = ' width="' + w + '"';
	}
	if(!h) {
		h = '';
	} else {
		h = ' height="' + h + '"';
	}
	for(var i = 0; i < urls.length; i++)
		PytCmd('inserthtml', false, '<img src="' + urls[i].src + '" alt="Image"' + w + h + ' />');
	//$('#PytImageUrl').val('http://');
	//$('#PytImageWidth,#PytImageHeight').val('');
	PytSH('.PytDiv.Image');
}

function PytInsertMusic() {
	var url = trim($('#PytMusicUrl').val());
	if(!url || url == 'http://') {
		pkalert('音频URL不能为空！');
		return false;
	}
	var cs = '';
	if($('input[name="PytMusicAutoplay"]').prop('checked')) {
		cs += ' autoplay';
	}
	if($('input[name="PytMusicLoop"]').prop('checked')) {
		cs += ' loop';
	}
	var suffix = url.split('.')[url.split('.').length - 1];
	if(suffix.length > 5) {
		suffix = 'mp3';
	}
	PytCmd('inserthtml', false, '<p><audio src="' + url + '" controls' + cs + '><source src="' + url + '" type="audio/' + suffix + '" /><embed src="' + url + '" /></audio></p><p>&nbsp;</p>');
	$('#PytMusicUrl').val('http://');
	PytSH('.PytDiv.Music');
}

function PytRemoveMarks() {
	var st = '';
	if(PytWindow.getSelection) {
		st = PytWindow.getSelection().getRangeAt(0);
	} else {
		st = PytEditor.selection.createRange().htmlText;
	}
	st = st.toString();
	st = st.replace(/<\/?.+?>/g, "");
	PytCmd('inserthtml', false, st);
}

function SthisBtn(me) {
	var $nos = 'PytUnlinkBtn,PytUndoBtn,PytRedoBtn';
	$nos = $nos.split(',');
	if($nos.indexOf($(me).attr('id')) > -1) {
		return false;
	}
	if($(me).hasClass('pk-active')) {
		$(me).removeClass('pk-active');
	} else {
		//$('#PytToolbar span').removeClass('pk-active');
		$(me).addClass('pk-active');
	}
}

function PytInsertFile() {
	var turl;
	var url = trim($('#PytFileUrl').val());
	var name = trim($('#PytFileName').val());
	var tiandou = parseInt($('#PytFileTiandou').val());
	var jifen = parseInt($('#PytFileJifen').val());
	if(!url || url == 'http://') {
		pkalert('文件URL不能为空！');
		return false;
	}
	if(!name) {
		name = url;
	}
	if(!tiandou) {
		tiandou = 0;
	}
	if(!jifen) {
		jifen = 0;
	}
	name = name.replace(/[\<\>]/g, '');
	url.indexOf('?') < 0 ? turl = url + '?' : turl = url + '&';
	turl += 'tiandou=' + tiandou + '&jifen=' + jifen + '&name=' + encodeURIComponent(name);
	PytCmd('inserthtml', false, '<a class="pk-text-primary pk-hover-underline" target="_blank" href="' + turl + '" title="点击进入下载"><span class="fa fa-download fa-fw"></span>' + name + '</a>');
	$('#PytFileUrl,#PytFileName,#PytFileTiandou,#PytFileJifen').val('');
	PytSH('.PytDiv.File');
}

function PytInsertCode() {
	var $code = $('#PytCodeTextarea').val();
	if($code) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode($code));
		$code = div.innerHTML;
		PytCmd('inserthtml', false, '<pre>' + $code + '</pre><br>');
		$('#PytCodeTextarea').val('');
		PytSH('.PytDiv.Code');
	} else {
		$('#PytCodeTextarea').focus();
	}
}

function PytInsertRemoteFile() {
	var url = prompt("请输入远程文件的链接地址", "");
	if(url === null || url === "") {
		return false;
	}
	$.getJSON('index.php?c=app&a=puyuetianeditor:index&s=remotelink', {
		"url": url,
		"chkcsrfval": $_USER['CHKCSRFVAL']
	}, function(data) {
		if(data['state'] == 'ok') {
			$('#PytFileUrl').val("index.php?c=app&a=puyuetianeditor:index&s=showfile&id=" + data['datas']['msg']);
			pktip('远程链接添加成功', 'success');
		} else {
			pkalert(data['datas']['msg']);
		}
	});
}

function PytInsertReplylook() {
	var $code = $('#PytReplylookTextarea').val();
	if($code) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode($code));
		$code = div.innerHTML;
		PytCmd('inserthtml', false, '<p class="PytReplylook">' + $code + '</p><br>');
		$('#PytReplylookTextarea').val('');
		PytSH('.PytDiv.Replylook');
	} else {
		$('#PytReplylookTextarea').focus();
	}
}

function PytInsertVideo() {
	var $code = $('#PytVideoUrl').val();
	if($code) {
		if($('#PytVideoType').val() == 'video') {
			var cs = '';
			if($('input[name="PytVideoAutoplay"]').prop('checked')) {
				cs += ' autoplay';
			}
			if($('input[name="PytVideoWidth"]').val()) {
				cs += ' width="' + $('input[name="PytVideoWidth"]').val() + '"';
			}
			if($('input[name="PytVideoHeight"]').val()) {
				cs += ' height="' + $('input[name="PytVideoHeight"]').val() + '"';
			}
			var suffix = $code.split('.')[$code.split('.').length - 1];
			if(suffix.length > 5) {
				suffix = 'mp4';
			}
			PytCmd('inserthtml', false, '<p><video src="' + $code + '" controls' + cs + ' style="max-width:100%"><source src="' + $code + '" type="video/' + suffix + '" /><embed src="' + $code + '" /></video></p><p>&nbsp;</p>');
		} else if($('#PytVideoType').val() == 'html') {
			PytCmd('inserthtml', false, '<p>' + $code + '</p>');
		} else {
			PytCmd('inserthtml', false, '<p><a target="_blank" class="pk-text-primary fa fa-video-camera" href="' + $code + '">&nbsp;点击观看视频</a></p>');
		}
		$('#PytVideoUrl').val('');
		PytSH('.PytDiv.Video');
	} else {
		$('#PytVideoUrl').focus();
	}
}

function PytSubmit() {
	if(!PytContent.val()) {
		pkalert('请先输入内容后再点击');
		return false;
	}
}

function InitPuyuetianEditor($id, $flashcode) {
	//编辑器初始化
	PytContent = $($id);
	PytContentValue = PytContent.val();
	var $parent = PytContent.parent();
	var $parentform = $($id).parents('form');
	if($parentform) {
		//$parentform.attr('onsubmit', 'return PytSubmit()');
		$parentform.attr('novalidate', true);
		if($parent) {
			$h = PytContent.height();
			PytContent.css('display', 'none');
			//PytContent.prop('required', false);
			$parent.append('<div id="PytEditorDriveDiv"></div>');
			$('#PytEditorDriveDiv').load('app/puyuetianeditor/template/PytEditorHtml.html', function(r, s, x) {
				if(s == 'success') {
					LoadPytEditor();
					$('#PytMainContent,#PytMainContent2').outerHeight($h);
					//原有内容的载入
					if((PytEditor.selection && (navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) || (!!window.ActiveXObject || "ActiveXObject" in window)) {
						setTimeout(function() {
							//IE就TM讨厌，还TM得延迟上，CNM
							$("#PytToolbar").append('<span id="PytIEoutBtn" class="fa fa-internet-explorer" title="更佳的体验" onclick="pkalert(\'建议您使用非IE浏览器获取更佳体验^o^\')" style="display:inline-block;color:#CC6666"></span> ');
							PytEditor.body.focus();
							PytCmd('inserthtml', false, PytContentValue);
						}, 1000);
					} else {
						if(PytContentValue)
							PytCmd('inserthtml', false, PytContentValue);
					}
					//加载完成后执行
					if($flashcode)
						$flashcode();
					//动态实时更新内容
					setInterval(function() {
						var html = $(PytEditor.body).html();
						var text = $('#PytMainContent2').val();
						if(html) {
							PytContent.val(html);
						} else {
							PytContent.val(text);
						}
					}, 200);
				}
			});
		}
	}
}