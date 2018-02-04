<?php
if (!defined('puyuetian'))
	exit('403');

//==========================获取左侧导航信息======================================
$tpath = "{$_G['SYSTEM']['PATH']}/app/";
$appnavs = '';
$scan = scandir($tpath);
foreach ($scan as $name) {
	if ($name && $name != '.' && $name != '..' && filetype($tpath . $name) == 'dir') {
		if (file_exists($tpath . $name . '/config.xml') && !file_exists($tpath . $name . '/install.json') && (file_exists($tpath . $name . '/setting.html') || file_exists($tpath . $name . '/setting.hst'))) {
			$xml = simplexml_load_string(file_get_contents($tpath . $name . '/config.xml'));
			$tname = $xml -> name;
			$appnavs .= "<li><a href='index.php?c=app&a=superadmin:index&s=app&t={$name}'>{$tname}</a></li>";
		}
	}
}

if (!$_G['GET']['T']) {
	//==========================获取本地所有的应用======================================
	$scan = scandir($tpath);
	foreach ($scan as $name) {
		if ($name && $name != '.' && $name != '..' && filetype($tpath . $name) == 'dir') {
			if (file_exists($tpath . $name . '/config.xml')) {
				$xml = simplexml_load_string(file_get_contents($tpath . $name . '/config.xml'));
				$tname = $xml -> name;
				$author = $xml -> author;
				$link = $xml -> link;
				$description = $xml -> description;
				if (file_exists($tpath . $name . '/install.json')) {
					$var = 'newinstallhtml';
					$ahtml = "
					<a class='pk-text-success pk-hover-underline' href='javascript:' onclick='pkalert(\"安装该插件后后台将被自动刷新，请保存当前设置，确认继续？\",\"提示\",\"pktip(\\\"正在安装请稍后...\\\",\\\"loading\\\",0);location.href=\\\"index.php?c=app&a=superadmin:index&s=app&t={$name}&ml=install\\\"\")'>安装</a>
					";
				} else {
					$var = 'oldinstallhtml';
					$ahtml = "
					<a class='pk-text-danger pk-hover-underline' href='javascript:' onclick='pkalert(\"卸载该插件后后台将被自动刷新，请保存当前设置，确认继续？\",\"提示\",\"pktip(\\\"正在卸载请稍后...\\\",\\\"loading\\\",0);location.href=\\\"index.php?c=app&a=superadmin:index&s=app&t={$name}&ml=uninstall\\\"\")'>卸载</a>
					<a target='_blank' class='pk-text-primary pk-hover-underline' href='index.php?c=app&a=superadmin:index&s=app&t={$name}&ml=json'>导出JSON数据</a>
					";
				}
				$$var .= "
<div class='pk-row pk-padding-top-15 pk-padding-bottom-15' style='border-bottom:solid 1px #eee'>
	<label class='pk-w-sm-3 pk-text-right' style='padding-top:2px'>
		<img src='app/{$name}/logo.png' onerror=\"this.src='app/superadmin/template/img/app.png'\" style='width:48px;height:48px'>
	</label>
	<div class='pk-w-sm-8 pk-text-default pk-text-sm'>
		<div class='pk-text-truncate pk-text-bold'>{$tname} （目录：{$name}，作者：<a target='_blank' class='pk-hover-underline' href='{$link}'>{$author}</a>）</div>
		<div class='pk-text-xs pk-text-truncate'>{$description}</div>
		<div class='pk-text-xs pk-text-truncate'>{$ahtml}</div>
	</div>
</div>
";
			}
		}
	}
	$tsetpath = 'superadmin:app-ma';
} else {
	//==========================安装应用======================================
	if ($_G['GET']['ML'] == 'install') {
		chkinstall('app', $_G['GET']['T']);
		$fpath = "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/install.json";
		if (file_exists($fpath)) {
			$jsondata = json_decode(file_get_contents($fpath), TRUE);
			if ($jsondata) {
				$iarray = array();
				foreach ($jsondata as $key => $value) {
					//数据校验
					if (substr($key, 0, strlen('app_' . $_G['GET']['T'])) == 'app_' . $_G['GET']['T']) {
						$iarray['setname'] = $key;
						$iarray['setvalue'] = $value;
						$_G['TABLE']['SET'] -> newData($iarray);
					}
				}
				if (!rename($fpath, "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/uninstall.json")) {
					$alert = '安装或升级失败，无写入权限！';
				} else {
					if (mysql_error()) {
						$alert = '插件升级成功！';
					} else {
						$alert = '插件安装成功！';
					}
					$alert .= '<br>注意：页面即将被自动刷新';
					$_G['RND'] .= '&refresh=1';
				}
			} else {
				$alert = '安装数据解析出错！';
			}
		} else {
			$alert = '安装失败，未找到安装数据！';
		}
		header('Location:index.php?c=app&a=superadmin:index&s=app&alert=' . urlencode($alert) . '&pkalert=show&rnd=' . $_G['RND']);
		exit('<script>top.location.href="index.php?c=app&a=superadmin:index&PIndex=5&CIndex=0"</script>');
		//==========================卸载应用======================================
	} elseif ($_G['GET']['ML'] == 'uninstall') {
		$sysapps = array('superadmin', 'verifycode', 'systememail', 'puyuetianeditor', 'hadskycloudserver');
		if (in_array(strtolower($_G['GET']['T']), $sysapps)) {
			header('Location:index.php?c=app&a=superadmin:index&s=app&alert=' . urlencode('不可卸载系统默认应用！') . '&pkalert=show&rnd=' . $_G['RND']);
			exit();
		}
		$fpath = "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/uninstall.json";
		if (file_exists($fpath)) {
			$jsondata = json_decode(file_get_contents($fpath), TRUE);
			if ($jsondata) {
				$iarray = array();
				foreach ($jsondata as $key => $value) {
					//数据校验
					if (substr($key, 0, strlen('app_' . $_G['GET']['T'])) == 'app_' . $_G['GET']['T']) {
						$_G['TABLE']['SET'] -> delData('setname', $key);
					}
				}
				if (rename($fpath, "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/install.json")) {
					$alert = '恭喜，卸载完成！';
					$alert .= '<br>注意：页面即将被自动刷新';
					$_G['RND'] .= '&refresh=1';
				} else {
					$alert = '卸载失败，无写入权限！';
				}
			} else {
				$alert = '卸载数据解析出错！';
			}
		} else {
			$alert = '卸载失败，未找到卸载数据！';
		}
		header('Location:index.php?c=app&a=superadmin:index&s=app&alert=' . urlencode($alert) . '&pkalert=show&rnd=' . $_G['RND']);
		exit('<script>top.location.href="index.php?c=app&a=superadmin:index&PIndex=5&CIndex=0"</script>');
		//==========================导出模板数据======================================
	} elseif ($_G['GET']['ML'] == 'json') {
		$jsonarray = array();
		foreach ($_G['SET'] as $key => $value) {
			if (substr($key, 0, strlen(strtoupper('app_' . $_G['GET']['T']))) == strtoupper('app_' . $_G['GET']['T'])) {
				$jsonarray[strtolower($key)] = $value;
			}
		}
		header('Content-type:text/json');
		exit(json_encode($jsonarray));
		//==========================加载应用设置======================================
	} else {
		$tsetpath = "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/setting.html";
		if (!file_exists($tsetpath)) {
			$tsetpath = "{$_G['SYSTEM']['PATH']}/app/{$_G['GET']['T']}/setting.hst";
		}
	}
}
$contenthtml = template($tsetpath, TRUE);
