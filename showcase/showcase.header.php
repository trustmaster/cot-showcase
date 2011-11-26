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
		$notice_seperator = !empty($out['notices']) ? ' - ' : '';
		$out['notices'] .= $notice_seperator . cot_rc_link(cot_url('plug', 'e=showcase'), $L['Showcase'].': '.$sc_cnt);
	}
}

?>
