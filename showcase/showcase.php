<?php
/* ====================
[BEGIN_COT_EXT]
Code=showcase
Hooks=standalone
[END_COT_EXT]
==================== */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

// Table name preset
if (!isset($db_showcase))
{
	$db_showcase = $db_x . 'showcase';
}

// Required files
require_once cot_incfile('tags', 'plug');
require_once cot_incfile('comments', 'plug');
require_once cot_incfile('ratings', 'plug');

// STW JavaScript
cot_rc_link_file('http://www.shrinktheweb.com/scripts/pagepix.js');

// Import a domain ID
$id = (int) cot_import('id', 'G', 'INT');

if ($id > 0)
{
	// Display domain details

	// First get domain data
	$res = $db->query("SELECT s.*, u.user_name
		FROM $db_showcase AS s
			LEFT JOIN $db_users AS u ON u.user_id = s.sc_owner
		WHERE sc_id = $id");
	if ($res->rowCount() != 1)
	{
		// 404 not found
		cot_die_message(404);
	}
	$row = $res->fetch();
	$res->closeCursor();
	$domain = $row['sc_domain'];

	$usr_edit = $usr['auth_write']
		&& ($usr['isadmin'] || $usr['id'] == $row['sc_owner']);

	if ($a == 'update' && $usr_edit)
	{
		$in = sc_import($domain);
		if ($in === false)
		{
			// Oops, the domain is no longer valid
			cot_error('Invalid_domain');
		}
		else
		{
			// Update database record and variables
			if ($db->update($db_showcase, $in, "sc_id = $id"))
			{
				$row['sc_title'] = $in['sc_title'];
				$row['sc_desc'] = $in['sc_desc'];
				$row['sc_date'] = $in['sc_date'];
				// Update tags
				$tags = cot_tag_parse(cot_import('tags', 'P', 'TXT'));
				$old_tags = cot_tag_list($id, 'showcase');
				$kept_tags = array();
				$new_tags = array();
				// Find new tags, count old tags that have been left
				$cnt = 0;
				foreach($tags as $tag)
				{
					$p = array_search($tag, $old_tags);
					if($p !== false)
						$kept_tags[$cnt++] = $old_tags[$p];
					else
						$new_tags[] = $tag;
				}
				// Remove old tags that have been removed
				$rem_tags = array_diff($old_tags, $kept_tags);
				foreach($rem_tags as $tag)
					cot_tag_remove($tag, $id, 'showcase');
				// Add new tags
				$ncnt = count($new_tags);
				if ($cfg['plugin']['tags']['limit'] > 0
					&& $ncnt > $cfg['plugin']['tags']['limit'] - $cnt)
					$lim = $cfg['plugin']['tags']['limit'] - $cnt;
				else
					$lim = $ncnt;
				for($i = 0; $i < $lim; $i++)
					cot_tag($new_tags[$i], $id, 'showcase');
				
				cot_message('Updated_successfully');
			}
			else
				cot_error('Database_error');
		}
	}

	if ($a == 'remove' && $usr_edit)
	{
		// Item removal
		if ($db->delete($db_showcase, "sc_id = $id"))
		{
			$db->delete($db_com, "com_id = $id AND com_area = 'showcase'");
			$db->delete($db_rated, "rated_code = '$id' AND rated_area = 'showcase'");
			$db->delete($db_ratings, "rating_code = '$id' AND rating_area = 'showcase'");
			cot_tag_remove_all($id, 'showcase');
			cot_redirect(cot_url('plug', 'e=showcase', '', true));
			exit;
		}
		else
			cot_error('Database_error');
	}

	if ($a == 'validate' && $usr['isadmin'])
	{
		$db->update($db_showcase, array('sc_active' => 1), "sc_id = $id");
		$row['sc_active'] = 1;
	}
	
	$out['subtitle'] = $L['Site_showcase'] . ' - ' . $domain;

	// Display the item and comments
	$t = new XTemplate(cot_tplfile('showcase.display', 'plug'));

	cot_display_messages($t);

	sc_display($domain, $usr_edit, true);

	$t->assign(array(
		'TITLE' => '<a href="' . cot_url('plug', 'e=showcase') . '">'
			. $L['Site_showcase'] . '</a>: ' . $domain,
		'ACTION' => cot_url('plug', "e=showcase&id=$id&a=update")
	));
}
else
{
	// Display main page

	// Add a new item if submitted
	if ($a == 'add' && $usr['auth_write'])
	{
		// Import data
		$in = sc_import();
		if ($in === false)
			cot_error('Invalid_domain');
		else
		{
			// Check if exists
			$query = "SELECT COUNT(*) FROM `$db_showcase`
				WHERE sc_domain = '{$in['sc_domain']}'";
			$count = $db->query($query)->fetchColumn();
			if ($count > 0)
				cot_error('Domain_registered');
			else
			{
				// Register in database
				if ($db->insert($db_showcase, $in))
				{
					// Add tags
					$item_id = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
					$tags = cot_tag_parse(cot_import('tags', 'P', 'TXT'));
					$cnt = 0;
					foreach($tags as $tag)
					{
						cot_tag($tag, $item_id, 'showcase');
						if($cfg['plugin']['tags']['limit'] > 0
							&& ++$cnt == $cfg['plugin']['tags']['limit'])
							break;
					}
					cot_message('Added_successfully');
				}
				else
					cot_error('Database_error');
			}
		}
	}

	// Import parameters
	$tag = cot_import('t', 'G', 'TXT');
	$o = cot_import('o', 'G', 'ALP');
	list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['plugin']['showcase']['per_page']);

	// Assemble the query
	if (!empty($tag))
	{
		$t_query = cot_tag_parse_query($tag, 's.sc_id');
		$t_from = "LEFT JOIN $db_tag_references AS r ON r.tag_item = s.sc_id";
		$t_where = "AND r.tag_area = 'showcase' AND ($t_query)";
	}

	if ($o == 'rate')
	{
		$r_fields = ', t.rating_average';
		$r_join = "LEFT JOIN $db_ratings AS t
				ON t.rating_code = CAST(s.sc_id AS CHAR) AND rating_area = 'showcase'";
		$order = 'rating_average DESC';
	}
	else
		$order = 'sc_date DESC';

	$totalitems = $db->query("SELECT COUNT(*)
		FROM `$db_showcase` AS s $t_from
		WHERE sc_active = 1 $t_where")->fetchColumn();

	$query = "SELECT s.*, u.user_name $r_fields
		FROM `$db_showcase` AS s
			LEFT JOIN $db_users AS u ON u.user_id = s.sc_owner
			$t_from
			$r_join
		WHERE sc_active = 1
			$t_where
		ORDER BY $order
		LIMIT $d, " . $cfg['plugin']['showcase']['per_page'];
	$res = $db->query($query);
	
	$out['subtitle'] = $L['Site_showcase'];

	// Display the items
	$t = new XTemplate(cot_tplfile('showcase', 'plug'));

	cot_display_messages($t);

	$perpage = $cfg['plugin']['showcase']['per_page'];
	$perrow = $cfg['plugin']['showcase']['per_row'];
	$i = 0;
	while ($row = $res->fetch())
	{
		sc_display($row['sc_domain']);
		$t->assign('ODDEVEN', cot_build_oddeven($i));
		$t->parse('MAIN.ROW.CELL');
		if (++$i % $perrow == 0)
			$t->parse('MAIN.ROW');
	}
	if ($i % $perrow != 0)
		$t->parse('MAIN.ROW');
	$res->closeCursor();

	// Build pagination
	if (!empty($tag))
	{
		$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		$url_t = '&t=' . $tag_u . $tl;
	}
	$pag_o = $o == 'rate' ? '&o=rate' : '';
	$pagenav = cot_pagenav('plug','e=showcase' . $url_t . $pag_o, $d, $totalitems, $perpage);

	// Build tag cloud
	$tcloud = cot_tag_cloud('showcase', $cfg['plugin']['tags']['order']);
	$tc_html = '<div class="tag_cloud">';
	foreach($tcloud as $tg => $cnt)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tg) : $tg;
		$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
		foreach($tc_styles as $key => $val)
		{
			if($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= '<a href="' . cot_url('plug', 'e=showcase&t='.$tag_u.$tl)
			. '" class="' . $dim . '">' . htmlspecialchars($tag_t) . '</a> ';
	}
	$tc_html .= '</div>';

	$t->assign(array(
		'TITLE' => '<a href="' . cot_url('plug', 'e=showcase') . '">'
			. $L['Site_showcase'] . '</a>'
			. (empty($tag) ? '' : ': ' . htmlspecialchars($tag)),
		'PAGEPREV' => $pagenav['prev'],
		'PAGENEXT' => $pagenav['next'],
		'PAGENAV' => $pagenav['main'],
		'TAGCLOUD' => $tc_html,
		'BYRATE_URL' => cot_url('plug','e=showcase&o=rate' . $url_t),
		'BYDATE_URL' => cot_url('plug','e=showcase' . $url_t)
	));

	
	if ($usr['auth_write'])
	{
		// Validation queue
		if (!$usr['isadmin']) $q_where = 'AND sc_owner = ' . $usr['id'];
		$res = $db->query("SELECT sc_id, sc_title FROM `$db_showcase`
			WHERE sc_active = 0 $q_where");
		if ($res->rowCount() > 0)
		{
			while ($row = $res->fetch())
			{
				$t->assign(array(
					'ITEM_LINK' => cot_url('plug', 'e=showcase&id='
						. $row['sc_id']),
					'ITEM_TITLE' => htmlspecialchars($row['sc_title'])
				));
				$t->parse('MAIN.VALIDATION.ROW');
			}
			$res->closeCursor();
			$t->parse('MAIN.VALIDATION');
		}

		// Display new domain submission form
		$t->assign('ACTION', cot_url('plug', 'e=showcase&a=add'));
		$t->parse('MAIN.FORM');
	}
}

/**
 * Imports an item from a POST form
 *
 * @global array $cfg Cotonti configuration
 * @global array $usr User account
 * @param string $domain Domain
 * @return mixed Input as associative array or false on error
 */
function sc_import($domain = '')
{
	global $cfg, $usr, $sys;

	if (empty($domain))
	{
		$domain = cot_import('domain', 'P', 'TXT');
		$domain = preg_replace('#[^\w\-\.]#', '', $domain);
		$in['sc_owner'] = $usr['id'];
	}

	if (empty($domain)) return false;

	// Check if it is a valid domain
	$data = @file_get_contents("http://$domain");
	if (!empty($data))
	{
		$in['sc_domain'] = $domain;
		$in['sc_title'] = cot_import('title', 'P', 'TXT');
		// Get title from page if empty
		if (empty($in['title'])
			&& preg_match('#<title>(.+?)</title>#i', $data, $mt))
			$in['sc_title'] = mb_substr($mt[1], 0,
				$cfg['plugin']['showcase']['length']);
		$in['sc_descr'] = cot_import('descr', 'P', 'TXT');
		$in['sc_date'] = $sys['now'];
		$in['sc_active'] = $usr['isadmin'] ? 1 : 0;
		return $in;
	}
	else
		return false;
}

/**
 * Prepares item display and assigns XTemplate tags
 *
 * @global XTemplate $t Template object
 * @global array $row SQL result row
 * @global array $cfg Cotonti configuration
 * @param string $domain Domain
 * @param bool $edit Display editable fields
 */
function sc_display($domain, $edit = false, $comments = false)
{
	global $t, $row, $cfg, $L, $usr;

	$title = htmlspecialchars($row['sc_title']);
	$url = 'http://' . $domain;
	$item_code = $row['sc_id'];
	$page_url = cot_url('plug', 'e=showcase&id=' . $item_code);

	$comments_link = cot_comments_link('plug', 'e=showcase&id=' . $item_code, 'showcase', $item_code);
	$comments_display = cot_comments_display('showcase', $item_code);
	$comments_count = cot_comments_count('showcase', $item_code);
	
	list ($ratings_display, $ratings_average) = cot_ratings_display('showcase', $item_code);

	// Clickable tag list
	$tags = cot_tag_list($item_code, 'showcase');
	if ($edit)
	{
		$tag_list = htmlspecialchars(implode(', ', $tags));
		$tag_list = '<input type="text" name="tags" value="'.$tag_list.'" />';
	}
	else
	{
		foreach ($tags as $tag)
		{
			if ($i++ > 0) $tag_list .= ', ';
			$tag_list .= cot_rc_link(cot_url('plug', 'e=showcase&t=' . $tag), $tag);
		}
	}

	// Assign tags
	$t->assign(array(
		'ITEM_ID' => $row['sc_id'],
		'ITEM_URL' => $url,
		'ITEM_LINK' => $page_url,
		'ITEM_DOMAIN' => $row['sc_domain'],
		'ITEM_TITLE' => $edit ?
			'<input type="text" name="title" maxlength="'
				. $cfg['plugin']['showcase']['length'] . '" value="'
				. $title . '" />' : $title,
		'ITEM_IMAGE' => <<<HTM
<script type="text/javascript">
stw_pagepix('$url', '{$cfg['plugin']['showcase']['access_key']}', 'lg', 0);
</script>
HTM
		,
		'ITEM_OWNER' => cot_build_user($row['sc_owner'], $row['user_name']),
		'ITEM_DESCR' => $edit ?
			'<textarea name="descr" maxlength="255">'
				. htmlspecialchars($row['sc_descr']) . '</textarea>'
			: htmlspecialchars($row['sc_descr']),
		'ITEM_COMMENTS' => $comments_link,
		'ITEM_COMMENTS_COUNT' => $comments_count,
		'ITEM_COMMENTS_DISPLAY' => $comments_display,
//		'ITEM_RATINGS' => $ratings_display,
		'ITEM_TAGS' => $tag_list,
		'ITEM_SAVE' => $edit ?
			'<input type="submit" value="' . $L['Submit'] . '" />' : '',
		'ITEM_REMOVE' => $edit ? '<a href="'
			. cot_url('plug', 'e=showcase&id=' . $row['sc_id'] . '&a=remove')
			. '" onclick="return confirm(\'' . $L['Ensure_delete'] . '\')">'
			. $L['Delete'] . '</a>' : '',
		'ITEM_VALIDATE' => $usr['isadmin'] && !$row['sc_active'] ? cot_rc_link(cot_url('plug', 'e=showcase&id=' . $row['sc_id'] . '&a=validate'), $L['Validate']) : ''
	));
}
?>
