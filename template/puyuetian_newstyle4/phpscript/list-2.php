<?php
if (!defined('puyuetian'))
	exit('403');

global $readdata, $readsortdata, $replyuserdata, $replydata, $imgshtml, $lgtime;
//
!Cnum($_G['GET']['SORTID']) && $readsortdata['title'] ? $_G['TEMP']['READSORTTITLE'] = $readsortdata['title'] : $_G['TEMP']['READSORTTITLE'] = '';
//图标加载
if ($readdata['high']) {
	$readdata['title'] = '<img src="template/puyuetian_newstyle4/img/high.gif" style="width:13px" alt="精华" title="精华">&nbsp;' . $readdata['title'];
}
if ($readdata['top']) {
	$readdata['title'] = '<img src="template/puyuetian_newstyle4/img/top.gif" style="width:13px" alt="置顶" title="置顶">&nbsp;' . $readdata['title'];
}
if (strpos($readdata['content'], '<img src="') !== FALSE) {
	$readdata['title'] .= '&nbsp;<img src="template/puyuetian_newstyle4/img/image.gif" alt="有图" title="有图" style="width:13px">';
}
if (strpos($readdata['content'], '<a class="pk-text-primary pk-hover-underline" target="_blank" href="index.php?c=app&amp;a=puyuetianeditor:index&amp;s=showfile&amp;id=') !== FALSE) {
	$readdata['title'] .= '&nbsp;<img src="template/puyuetian_newstyle4/img/attachment.gif" alt="附件" title="附件" style="width:13px">';
}
//显示情况
$_G['GET']['ORDER'] == 'activetime' ? $_G['TEMP']['CONTENT'] = "{$replyuserdata['nickname']}：" . EqualReturn(strip_tags($replydata['content'], ''), '', '[Image]') : $_G['TEMP']['CONTENT'] = EqualReturn(strip_tags($readdata['content']), '', '[Image]');
//图片读取
$i = 0;
$_G['TEMP']['IMGS'] = '';
$imgshtml = array();
$noimglist = 'emotion';
if (preg_match_all('#<img.*?src="(.*?)".*?alt="(.*?)".*?\>#', $readdata['content'], $match)) {
	foreach ($match[1] as $key => $value) {
		if (!InArray($noimglist, $match[2][$key])) {
			if ($i > 3)
				break;
			$i++;
			$imgshtml[$i] = '
<div class="pk-w-sm-(-i-) pk-overflow-hidden pk-text-center" style="height:125px">
<img class="ImageLoading pk-max-width-all pk-cursor-pointer" src="' . $value . '" alt="" onclick="LookImage(this)" />
</div>';
		}
	}
	$i = count($imgshtml);
	if ($i) {
		$i = 12 / $i;
		foreach ($imgshtml as $value) {
			$_G['TEMP']['IMGS'] .= str_replace('(-i-)', $i, $value);
		}
	}
}
//动态时间显示
$readlistorder = Cstr($_G['SET']['READLISTORDER'], 'activetime', TRUE, 1, 255);
if ($readlistorder != 'posttime')
	$readlistorder = 'activetime';
$lgtime = time() - Cnum($readdata[$readlistorder]);
if ($lgtime < 60) {
	$lgtime = '<span class="pk-text-danger">' . '刚刚</span>';
} elseif ($lgtime < 3600) {
	$lgtime = '<span class="pk-text-danger">' . (int)($lgtime / 60) . '分钟前</span>';
} elseif ($lgtime < 86400) {
	$lgtime = '<span class="pk-text-danger">' . (int)($lgtime / 3600) . '小时前</span>';
} else {
	$lgtime = (int)($lgtime / 86400) . '天前';
}
$_G['TEMP']['READADMINLINK'] = '';
//版主检测
$bkdata = $_G['TABLE']['READSORT'] -> getData($readdata['sortid']);
if (InArray(getUserQX(), 'superman')) {
	if ($readdata['top']) {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="pkalert(&quot;确认取消该文章的置顶？&quot;,&quot;提示&quot;,&quot;window.open(\'index.php?c=admincmd&table=read&field=top&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',\'pk-di\');pkalert(\'取消成功\')&quot;)">取消置顶</a>&nbsp;';
	} else {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="pkalert(&quot;确认设置该文章置顶？&quot;,&quot;提示&quot;,&quot;window.open(\'index.php?c=admincmd&table=read&field=top&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',\'pk-di\');pkalert(\'设置成功\')&quot;)">设为置顶</a>&nbsp;';
	}
	if ($readdata['high']) {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="pkalert(&quot;确认取消该文章的精华？&quot;,&quot;提示&quot;,&quot;window.open(\'index.php?c=admincmd&table=read&field=high&value=0&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',\'pk-di\');pkalert(\'取消成功\')&quot;)">取消精华</a>&nbsp;';
	} else {
		$_G['TEMP']['READADMINLINK'] .= '<a href="javascript:" onclick="pkalert(&quot;确认设置该文章精华？&quot;,&quot;提示&quot;,&quot;window.open(\'index.php?c=admincmd&table=read&field=high&value=1&id=' . $readdata['id'] . '&chkcsrfval=' . $_G['CHKCSRFVAL'] . '\',\'pk-di\');pkalert(\'设置成功\')&quot;)">设为精华</a>&nbsp;';
	}
}
if (InArray(getUserQX(), 'admin') || (InArray($bkdata['adminuids'], $_G['USER']['ID']) && $_G['USER']['ID'])) {
	$_G['TEMP']['READADMINLINK'] .= '
<a href="index.php?c=edit&type=read&id=' . $readdata['id'] . '">编辑</a>
<a href="javascript:" onclick="pkalert(&quot;确认删除该文章？&quot;,&quot;提示&quot;,&quot;delread(\'' . $readdata['id'] . '\',\'read\',function(){$(\'#listdivbox-' . $readdata['id'] . '\').remove()});&quot;)">删除</a>
';
}
