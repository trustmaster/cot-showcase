<?php
/* ====================
[BEGIN_COT_EXT]
Code=showcase
Hooks=header.main
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

// Table name preset
if (!isset($db_showcase))
{
	$db_showcase = $db_x . 'showcase';
}


if (cot_auth('plug', 'showcase', 'A'))
{
	$sc_cnt = $db->query("SELECT COUNT(*) FROM `$db_showcase`
		 WHERE sc_active = 0")->fetchColumn();
	if($sc_cnt > 0)
	{
		require_once cot_langfile('showcase', 'plug');
		if (!is_array($out['notices_array'])) $out['notices_array'] = array();
		$notice_item = array();
		$notice_item[0] = cot_url('plug', 'e=showcase');
		$notice_item[1] = cot_declension($sc_cnt, 'Showcase_sites') . ' ' . $L['For_validation'];
		$out['notices_array'][] = $notice_item;
	}
}
