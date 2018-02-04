<?php
if (!defined('puyuetian'))
	exit('403');

$_G['GET']['SORTID'] = Cnum($_G['GET']['SORTID']);
//本版块今日信息
if ($_G['GET']['SORTID']) {
	$_G['TEMP']['TODAYCOUNT'] = getReadCount($_G['GET']['SORTID'], 'today');
	$_G['TEMP']['READCOUNT'] = getReadCount($_G['GET']['SORTID']);
} else {
	$_G['TEMP']['TODAYCOUNT'] = $_G['TABLE']['READ'] -> getCount('where `del`=false and `posttime`>' . strtotime(date('Y-m-d 00:00:00', time())) . ' and `posttime`<' . strtotime(date('Y-m-d 23:59:59', time()))) + $_G['TABLE']['REPLY'] -> getCount('where `del`=false and `posttime`>' . strtotime(date('Y-m-d 00:00:00', time())) . ' and `posttime`<' . strtotime(date('Y-m-d 23:59:59', time())));
	$_G['TEMP']['READCOUNT'] = $_G['TABLE']['READ'] -> getCount(array('del' => FALSE)) + $_G['TABLE']['REPLY'] -> getCount(array('del' => FALSE));
}
//推荐文章
$wzids = $_G['TEMP']['WZHTML'] = '';
if (Cnum($_G['GET']['SORTID'])) {
	//热文
	$rw = $_G['TABLE']['READ'] -> getDatas(0, 5, 'where `del`=false and `high`=0 and `sortid`=' . $_G['GET']['SORTID'] . ' order by `looknum` desc');
	foreach ($rw as $value) {
		$wzids .= ',' . $value['id'];
	}
	//精华
	$rw = $_G['TABLE']['READ'] -> getDatas(0, 5, 'where `del`=false and `high`=1 and `sortid`=' . $_G['GET']['SORTID'] . ' order by `id` desc');
	foreach ($rw as $value) {
		$wzids .= ',' . $value['id'];
	}
	$wzids = substr($wzids, 1);
} else {
	$wzids = $_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_TJWZ'];
}
$wzids = explode(',', $wzids);
foreach ($wzids as $value) {
	$readdata = $_G['TABLE']['READ'] -> getData(Cnum($value));
	if ($readdata) {
		$_G['TEMP']['WZHTML'] .= '<li><a target="_blank" href="' . ReWriteURL('read', "id={$value}&page=1") . '" title="' . htmlspecialchars($readdata['title'], ENT_QUOTES) . '">' . strip_tags($readdata['title']) . '</a></li>';
	}
}
//子版块
$_G['TEMP']['CFHTML'] = '';
$childforumsdata = $_G['TABLE']['READSORT'] -> getDatas(0, 0, "where `pid`={$_G['GET']['SORTID']} and `show`=1 order by `rank`");
foreach ($childforumsdata as $childforumdata) {
	$childforumdata['readcount'] = getReadCount($childforumdata['id']);
	$childforumdata['todayreadcount'] = getReadCount($childforumdata['id'], 'today');
	if ($childforumdata['url']) {
		$cforumgourl = $childforumdata['url'];
	} else {
		$cforumgourl = ReWriteURL('list', "sortid={$childforumdata['id']}&page=1");
	}
	$_G['TEMP']['CFHTML'] .= '
		<div class="pk-row pk-padding-top-10 pk-padding-bottom-10" style="height:62px;border-bottom:solid 1px #ddd">
			<div class="pk-w-md-4 pk-text-right">
				<img class="pk-float-right" src="' . $childforumdata['logourl'] . '" onerror="this.src=\'template/default/img/forum.png\';this.onerror=\'\'" alt="" style="max-width:100%;max-height:42px">
			</div>
			<div class="pk-w-md-8 pk-padding-0 pk-text-truncate pk-text-default">
				<div class="pk-text-truncate pk-text-sm" style="height:22px;padding-top:2px">
					<a class="pk-text-primary pk-hover-underline" href="' . $cforumgourl . '">' . $childforumdata['title'] . '</a>
				</div>
				<div class="pk-row pk-text-xs" style="height:20px;padding-top:0px;color:#777">
					<div class="pk-w-md-5 pk-text-truncate pk-padding-0">今日：<span class="pk-text-danger">' . $childforumdata['todayreadcount'] . '</span></div>
					<div class="pk-w-md-7 pk-text-truncate pk-padding-0">文章：<span class="">' . $childforumdata['readcount'] . '</span></div>
				</div>
			</div>
		</div>
		';
}
//版主
$_G['TEMP']['BZS'] = '';
if ($_G['GET']['SORTID']) {
	$data = $_G['TABLE']['READSORT'] -> getData($_G['GET']['SORTID']);
	if ($data) {
		$bzs = explode(',', $data['adminuids']);
		foreach ($bzs as $id) {
			$bzdata = $_G['TABLE']['USER'] -> getData($id);
			if ($bzdata) {
				if ($bzdata['sex'] == 'b') {
					$xb = '<span class="pk-text-primary fa fa-mars"></span>';
				} elseif ($bzdata['sex'] == 'g') {
					$xb = '<span class="pk-text-danger fa fa-venus"></span>';
				} else {
					$xb = '<span class="pk-text-default fa fa-intersex"></span>';
				}
				$_G['TEMP']['BZS'] .= '
			<div class="pk-w-md-6 pk-padding-top-15 pk-padding-bottom-15 pk-text-center">
				<a target="_blank" class="pk-hover-underline" href="' . ReWriteURL('user', "id={$bzdata['id']}&page=1") . '" style="color:#555">
					<img class="pk-radius-all" src="userhead/' . $bzdata['id'] . '.png" style="width:56px;height:56px;" onerror="this.src=\'userhead/0.png\';this.onerror=\'\'" alt="" />
					<div class="pk-text-truncate pk-text-xs">' . $xb . '&nbsp;' . $bzdata['nickname'] . '</div>
				</a>
			</div>';
			}
		}
	}
} else {
	$bzdata = $_G['TABLE']['USER'] -> getData(1);
	if ($bzdata['sex'] == 'b') {
		$xb = '<span class="pk-text-primary fa fa-mars"></span>';
	} elseif ($bzdata['sex'] == 'g') {
		$xb = '<span class="pk-text-danger fa fa-venus"></span>';
	} else {
		$xb = '<span class="pk-text-default fa fa-intersex"></span>';
	}
	$_G['TEMP']['BZS'] .= '
			<div class="pk-w-md-6 pk-padding-top-15 pk-padding-bottom-15 pk-text-center">
				<a target="_blank" class="pk-hover-underline" href="' . ReWriteURL('user', "id={$bzdata['id']}&page=1") . '" style="color:#555">
					<img class="pk-radius-all" src="userhead/' . $bzdata['id'] . '.png" style="width:56px;height:56px;" onerror="this.src=\'userhead/0.png\';this.onerror=\'\'" alt="" />
					<div class="pk-text-truncate pk-text-xs">' . $xb . '&nbsp;' . $bzdata['nickname'] . '</div>
				</a>
			</div>';
}
