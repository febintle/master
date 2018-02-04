<?php
if (!defined('puyuetian'))
	exit('403');

if (($_G['GET']['C'] == 'edit' || $_G['GET']['C'] == 'read') && $_G['GET']['PYTEDITORLOAD'] != 'no') {
	$_G['SET']['EMBED_HEAD'] .= '<link rel="stylesheet" href="app/puyuetianeditor/template/css/PytEditor.css">';
	$_G['SET']['EMBED_FOOT'] .= '
		<script>
			if($(window).width()>1000){
				//PC
				' . $_G['SET']['APP_PUYUETIANEDITOR_PC' . strtoupper($_G['GET']['C']) . 'CONFIG'] . ';
			}else{
				//phone
				' . $_G['SET']['APP_PUYUETIANEDITOR_PHONE' . strtoupper($_G['GET']['C']) . 'CONFIG'] . ';
			}
		</script>
		<script src="app/puyuetianeditor/template/js/PytEditor.js"></script>
		<script>
			$(function() {
				InitPuyuetianEditor("textarea[name=content]",function(){
					$("#PytTiandouName").html("'.$_G['SET']['TIANDOUNAME'].'");
					$("#PytJifenName").html("'.$_G['SET']['JIFENNAME'].'");
				});
			});
		</script>
	';
}
