<?php
if (!defined('puyuetian'))
	exit('403');

if (!$_G['USER']['ID']) {
	exit(json_encode(array('state' => 'no', 'msg' => '请登录')));
}
if (!$_G['GET']['UID'] || $_G['GET']['UID'] == $_G['USER']['ID']) {
	exit(json_encode(array('state' => 'no', 'msg' => '不能自己给自己发消息')));
}
$content = Cstr($_POST['content'], FALSE, FALSE, 1, Cnum($_G['SET']['POSTMESSAGEMAXNUM'], 255, TRUE, 1));
if (!$content) {
	exit(json_encode(array('state' => 'no', 'msg' => '内容为空或超出最大限制')));
}
$ss = time() - Cnum(JsonData($_G['USER']['DATA'], 'lasttimemessageposttime')) - 5;
if ($ss < 0) {
	exit(json_encode(array('state' => 'no', 'msg' => '操作太快，请稍后一会再操作')));
}
$udata = $_G['TABLE']['USER'] -> getData($_G['GET']['UID']);
if (!$udata) {
	exit(json_encode(array('state' => 'no', 'msg' => '不存在的用户')));
}
if (strpos($udata['friends'], "_{$_G['USER']['ID']}_") === FALSE) {
	NewMessage($_G['GET']['UID'], '陌生人<a class="pk-text-bold" target="_blank" href="index.php?c=user&id=' . $_G['USER']['ID'] . '">[' . $_G['USER']['NICKNAME'] . ']</a>：' . strip_tags($content), 0, 2);
} else {
	NewMessage($_G['GET']['UID'], $content, $_G['USER']['ID']);
}
//记录最后一次发布时间
$array['id'] = $_G['USER']['ID'];
$array['data'] = JsonData($_G['USER']['DATA'], 'lasttimemessageposttime', time());
$_G['TABLE']['USER'] -> newData($array);
exit(json_encode(array('state' => 'ok', 'msg' => '发送成功')));
