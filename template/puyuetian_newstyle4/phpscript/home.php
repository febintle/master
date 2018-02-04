<?php
if (!defined('puyuetian'))
	exit('403');

//幻灯片
$sql = '';
if ($_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_HDPSHOWSORTIDS']) {
	$ids = explode(',', $_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_HDPSHOWSORTIDS']);
	$sql = ' (';
	foreach ($ids as $id) {
		if (Cnum($id)) {
			$sql .= '`sortid`=' . $id . ' or ';
		}
	}
	if ($sql != ' (') {
		$sql = substr($sql, 0, strlen($sql) - 4);
		$sql .= ') and';
	} else {
		$sql = '';
	}
	//exit('where' . $sql . ' `del`=false order by `id` desc');
}
$sliderdatas = $_G['TABLE']['READ'] -> getDatas(0, 500, 'where' . $sql . ' `del`=false order by `id` desc');
$i = 0;
$_G['TEMP']['HIGHREAD'] = $_G['TEMP']['SLIDE1'] = '';
foreach ($sliderdatas as $sliderdata) {
	if (preg_match_all('#<img.*?src="(.*?)".*?alt="(.*?)".*?\>#', $sliderdata['content'], $match)) {
		$noimglist = 'emotion';
		foreach ($match[1] as $key => $value) {
			if (!InArray($noimglist, $match[2][$key])) {
				$i++;
				$_G['TEMP']['SLIDE1'] .= '<a target="_blank" href="' . ReWriteURL('read', "id={$sliderdata['id']}&page=1") . '" title="' . strip_tags($sliderdata['title']) . '"><img src="' . $value . '" alt="Image"></a>';
				if ($i >= Cnum($_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_SLIDECOUNT'], 5)) {
					break 2;
				}
				break;
			}
		}
	}
}
unset($sliderdatas);
//精彩导读
$readdatas = $_G['TABLE']['READ'] -> getDatas(0, 5, 'where `high`=1 and `del`=false order by `id` desc');
foreach ($readdatas as $key => $value) {
	$_G['TEMP']['HIGHREAD'] .= '<li>&nbsp;<a target="_blank" href="' . ReWriteURL('read', "id={$value['id']}&page=1") . '" title="' . strip_tags($value['title']) . '">' . strip_tags($value['title']) . '</a></li>';
}
//最新动态
$sql = $_G['TEMP']['NA2'] = '';
if ($_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_NEWACTIVEIDS']) {
	$ids = explode(',', $_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_NEWACTIVEIDS']);
	$sql = ' (';
	foreach ($ids as $id) {
		if (Cnum($id)) {
			$sql .= '`sortid`=' . $id . ' or ';
		}
	}
	if ($sql != ' (') {
		$sql = substr($sql, 0, strlen($sql) - 4);
		$sql .= ') and';
	} else {
		$sql = '';
	}
	//exit('where' . $sql . ' `del`=false order by `id` desc');
}
$readdatas = $_G['TABLE']['READ'] -> getDatas(0, 11, 'where' . $sql . ' `del`=false order by `id` desc');
foreach ($readdatas as $key => $value) {
	if (!$key) {
		$_G['TEMP']['NA1I'] = $value['id'];
		$_G['TEMP']['NA1T'] = strip_tags($value['title']);
		if (strpos($value['content'], '<p class="PytReplylook">') !== FALSE || $value['replyafterlook']) {
			$_G['TEMP']['NA1C'] = '此文有部分隐藏内容无法预览，请点击打开查看';
		} else {
			$_G['TEMP']['NA1C'] = strip_tags($value['content']);
		}
	} else {
		$sortdata = $_G['TABLE']['READSORT'] -> getData($value['sortid']);
		if (!$sortdata['title']) {
			$sortdata['title'] = '未分类';
		}
		$userdata = $_G['TABLE']['USER'] -> getData($value['uid']);
		if (!$userdata['nickname']) {
			$userdata['nickname'] = '无昵称';
		}
		$_G['TEMP']['NA2'] .= '<div class="pk-row"><div class="pk-w-md-10"><a target="_blank" href="' . ReWriteURL('list', "sortid={$value['sortid']}&page=1") . '">[' . strip_tags($sortdata['title']) . ']</a><a target="_blank" href="' . ReWriteURL('read', "id={$value['id']}&page=1") . '" title="' . strip_tags($value['title']) . '">' . strip_tags($value['title']) . '</a></div><div class="pk-w-md-2 pk-padding-left-0"><a target="_blank" href="' . ReWriteURL('user', "id={$value['uid']}&page=1") . '" title="' . strip_tags($userdata['nickname']) . '">' . strip_tags($userdata['nickname']) . '</a></div></div>';
	}
}
//图文热点，默认30天
$i = 0;
$_G['TEMP']['TWRD'] = '';
$time1 = time() - (86400 * Cnum($_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_TWRDDAYS'], 30, TRUE, 1));
$readdatas = $_G['TABLE']['READ'] -> getDatas(0, 500, 'where `posttime`>' . $time1 . ' and `del`=false order by `fs` desc');
foreach ($readdatas as $readdata) {
	if (preg_match_all('#<img.*?src="(.*?)".*?alt="(.*?)".*?\>#', $readdata['content'], $match)) {
		$noimglist = 'emotion';
		foreach ($match[1] as $key => $value) {
			if (!InArray($noimglist, $match[2][$key])) {
				$i++;
				$_G['TEMP']['TWRD'] .= '<a target="_blank" href="' . ReWriteURL('read', "id={$readdata['id']}&page=1") . '" title="' . strip_tags($readdata['title']) . '"><img src="' . $value . '"></a><p class="pk-text-center"><a target="_blank" href="' . ReWriteURL('read', "id={$readdata['id']}&page=1") . '" title="' . strip_tags($readdata['title']) . '">' . strip_tags($readdata['title']) . '</a></p>';
				if ($i >= 2) {
					break 2;
				}
				break;
			}
		}
	}
}
//版块展示
$_G['TEMP']['BKHTML'] = '';
$bks = explode(',', $_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_BKS']);
foreach ($bks as $id) {
	$bkdata = $_G['TABLE']['READSORT'] -> getData($id);
	if ($bkdata) {
		$readdatas = $_G['TABLE']['READ'] -> getDatas(0, 100, 'where `sortid`=' . $id . ' and `del`=false order by `id` desc');
		$i = 0;
		$tp = '';
		foreach ($readdatas as $readdata) {
			if (preg_match_all('#<img.*?src="(.*?)".*?alt="(.*?)".*?\>#', $readdata['content'], $match)) {
				$noimglist = 'emotion';
				foreach ($match[1] as $key => $value) {
					if (!InArray($noimglist, $match[2][$key])) {
						$i++;
						$tp = '<div onclick="window.open(\'' . ReWriteURL('read', "id={$readdata['id']}&page=1") . '\',\'_blank\')">
							<img src="' . $value . '" alt="">
							<span>' . strip_tags($readdata['title']) . '</span>
						</div>';
						if ($i >= 1) {
							$cid = $readdata['id'];
							break 2;
						}
						break;
					}
				}
			}
		}
		$i = 0;
		$readdatas = $_G['TABLE']['READ'] -> getDatas(0, 6, 'where `sortid`=' . $id . ' and `del`=false order by `id` desc');
		$wz = '';
		foreach ($readdatas as $key => $readdata) {
			if ($readdata['id'] != $cid && $i < 5) {
				$i++;
				$wz .= '<li><a target="_blank" href="' . ReWriteURL('read', "id={$readdata['id']}&page=1") . '" title="' . strip_tags($readdata['title']) . '">' . strip_tags($readdata['title']) . '</a></li>';
			}
		}
		if (!$tp) {
			$tp = '<div><img src="template/puyuetian_newstyle4/img/noimg.jpg" alt=""><span>无最新图片</span></div>';
		}
		$_G['TEMP']['BKS'] .= '<div class="pk-w-md-6"><div class="pk-row newstyle4-bkbt"><div class="pk-w-md-8 pk-padding-0 pk-text-truncate">' . strip_tags($bkdata['title']) . '</div><div class="pk-w-md-4 pk-padding-0 pk-text-right"><a target="_blank" href="' . ReWriteURL('list', "sortid={$bkdata['id']}&page=1") . '">更多&raquo;</a></div></div><div class="pk-row newstyle4-bktp"><div class="pk-w-md-5 pk-padding-0">' . $tp . '</div><div class="pk-w-md-7 pk-padding-right-0"><ul>' . $wz . '</ul></div></div></div>';
	}
}
