<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['GET']['T'])
	$contenthtml = template('superadmin:home-' . $_G['GET']['T'], TRUE);
else
	$contenthtml = template('superadmin:home-info', TRUE);
