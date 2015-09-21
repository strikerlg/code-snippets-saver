<?php

require_once('config.php');
require_once('class.mysqli.php');

function get_category_color($cat) {
	switch ($cat) {
		case "plain-text":
			return "palette-silver-light";
			break;
		case "html":
			return "palette-red-light";
			break;
		case "css":
			return "palette-orange-light";
			break;
		case "javascript":
			return "palette-yellow-light";
			break;
		case "php":
			return "palette-blue-light";
			break;
		case "mysql":
			return "palette-green-light";
			break;
		case "dos":
			return "palette-black-light";
			break;
		case "csharp":
			return "palette-purple-light";
			break;
	}
}

function get_category_title($cat) {
	switch ($cat) {
		case "plain-text":
			return "Plain Text";
			break;
		case "html":
			return "HTML";
			break;
		case "css":
			return "CSS";
			break;
		case "javascript":
			return "JavaScript";
			break;
		case "php":
			return "PHP";
			break;
		case "mysql":
			return "MySQL";
			break;
		case "dos":
			return "DOS";
			break;
		case "csharp":
			return "C#";
			break;
	}
}

function get_timeago($ptime) {
	$estimate_time = time() - $ptime;
	if ($estimate_time < 1) {
		return 'less than 1 second ago';
	}
	
	$condition = array(
		12 * 30 * 24 * 60 * 60 => 'year',
		30 * 24 * 60 * 60 => 'month',
		24 * 60 * 60 => 'day',
		60 * 60 => 'hour',
		60 => 'minute',
		1 => 'second'
	);
	
	foreach ($condition as $secs => $str) {
		$d = $estimate_time / $secs;
		
		if ($d >= 1) {
			$r = round($d);
			return 'about ' . $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
		}
	}
}

function snippet_view_increment($snippet_id, $new_count) {
	$db = DB::getInstance();
	$update_fields = array(
		'view_count' => $new_count
	);
	$where_clause = array(
		'id' => $snippet_id
	);
	$db->update(DBTABLE_SNIPPETS, $update_fields, $where_clause, 1);
}

function get_total_bycategory($category) {
	$db = DB::getInstance();
	$query = "SELECT id FROM ".DBTABLE_SNIPPETS." WHERE category_tag='".$category."'";
	return $db->num_rows($query);
}

// HNAuthLib
function get_username($user_id) {
	$db = DB::getInstance();
	$query = "SELECT username FROM ".DBTABLE_USERS." WHERE id='".$user_id."'";
	if ($db->num_rows($query) > 0) {
		list($username) = $db->get_row($query);
		return $username;
	} else {
		return 'UNKNOWN';
	}
}
function get_userid($username) {
	$db = DB::getInstance();
	$query = "SELECT id FROM ".DBTABLE_USERS." WHERE username='".$username."'";
	if ($db->num_rows($query) > 0) {
		list($userid) = $db->get_row($query);
		return $userid;
	} else {
		return 0;
	}
}

// Get total snippets for each category
$plain_text = get_total_bycategory('plain-text');
$html = get_total_bycategory('html');
$css = get_total_bycategory('css');
$javascript = get_total_bycategory('javascript');
$php = get_total_bycategory('php');
$mysql = get_total_bycategory('mysql');
$dos = get_total_bycategory('dos');
$csharp = get_total_bycategory('csharp');

function get_error_code($var) {
	switch ($var) {
		case "id_not_found":
			return "ID is not found!";
			break;
		case "id_not_numeric":
			return "ID is not numerical!";
			break;
		case "user_not_owner":
			return "User is not the owner of the snippet.";
			break;
		case "invalid_category":
			return "Invalid category.";
			break;
		case "empty_parameter":
			return "Empty parameter.";
		default:
			return $var;
			break;
	}
}

?>