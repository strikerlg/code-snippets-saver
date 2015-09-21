<?php

require_once('config.php');
require_once('class.mysqli.php');
require_once('class.util.php');

// function is_ajax() {
// 	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
// }

// if (is_ajax()) {
// 	if (isset($_POST["action"]) && !empty($_POST["action"])) {
// 		$action = $_POST["action"];
// 		switch($action) {
// 			case "add": add_snippet(); break;
// 			case "edit": edit_snippet(); break;
// 			case "delete": delete_snippet(); break;
// 		}
// 	}
// }

if (isset($_POST["action"]) && !empty($_POST["action"])) {
	$action = $_POST["action"];
	switch($action) {
		case "add": add_snippet(); break;
		case "edit": edit_snippet(); break;
		case "delete": delete_snippet(); break;
	}
}

function add_snippet() {
	// Validation occurs beforehand in JS on click
	$db = DB::getInstance();
	if (isset($_POST)) {
		foreach ($_POST as $key => $value) {
			$_POST[$key] = $db->filter($value);
		}
	}
	$created_time = time();
	$fields = array(
		'user_id' => $_POST['userID'],
		'category_tag' => $_POST['snippetCategory'],
		'name' => $_POST['snippetName'],
		'visibility' => $_POST['snippetVisibility'],
		'description' => $_POST['snippetDesc'],
		'snippet' => $_POST['snippetCode'],
		'created_time' => $created_time,
		'updated_time' => $created_time,
		'view_count' => 0
	);
	$add_query = $db->insert(DBTABLE_SNIPPETS, $fields);

	if ($add_query) {
		echo 'OK|'.$db->lastid();
	} else {
		echo 'KO|Failed to add the snippet into the database.';
	}
}

function edit_snippet() {
	// Validation occurs beforehand in JS on click
	$db = DB::getInstance();
	if (isset($_POST)) {
		foreach ($_POST as $key => $value) {
			$_POST[$key] = $db->filter($value);
		}
	}
	$fields = array(
		'category_tag' => $_POST['snippetCategory'],
		'name' => $_POST['snippetName'],
		'visibility' => $_POST['snippetVisibility'],
		'description' => $_POST['snippetDesc'],
		'snippet' => $_POST['snippetCode'],
		'updated_time' => time()
	);
	$where_clause = array(
		'id' => $_POST['snippetID'],
		'user_id' => $_POST['userID']
	);
	$update_query = $db->update(DBTABLE_SNIPPETS, $fields, $where_clause, 1);

	if ($update_query) {
		echo 'OK|'.$_POST['snippetID'];
	} else {
		echo 'KO|Failed to update Snippet ID: ['.$_POST['snippetID'].'] into the database.';
	}
}

function delete_snippet() {
	// Validation occurs beforehand in JS on click
	$db = DB::getInstance();
	if (isset($_POST)) {
		foreach ($_POST as $key => $value) {
			$_POST[$key] = $db->filter($value);
		}
	}
	$fields = array(
		'id' => $_POST['snippetID'],
		'user_id' => $_POST['userID']
	);
	$delete_query = $db->delete(DBTABLE_SNIPPETS, $fields, 1);

	if ($delete_query) {
		echo 'OK|'.$_POST['snippetID'];
	} else {
		echo 'KO|Failed to delete Snippet ID" ['.$_POST['snippetID'].'] from the database.';
	}
}

function get_total_snippet() {
	$db = DB::getInstance();
	$query = "SELECT id FROM ".DBTABLE_SNIPPETS;
	$return = array();
	$return['totalSnippets'] = $db->num_rows($query);
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	echo json_encode($return);
}

if (isset($_GET['get']) && !empty($_GET['get']) && $_GET['get'] == 'totalSnippets') {
	get_total_snippet();
}


?>