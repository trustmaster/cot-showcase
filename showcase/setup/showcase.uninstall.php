<?php
/**
 * Removes all showcase data
 *
 * @package showcase
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $db;

require_once cot_incfile('tags', 'plug');
require_once cot_incfile('comments', 'plug');
require_once cot_incfile('ratings', 'plug');

$db->delete($db_tag_references, "tag_area = 'showcase'");
$db->delete($db_com, "com_area = 'showcase'");
$db->delete($db_rated, "rated_area = 'showcase'");
$db->delete($db_ratings, "rating_area = 'showcase'");
?>
