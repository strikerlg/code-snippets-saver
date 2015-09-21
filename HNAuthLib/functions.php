<?php

function _checkUsername($username) {
  global $hndb;
  $db = DB::getInstance();
  $query = "SELECT id FROM ".$hndb['table']." WHERE username='".$username."'";
  $count = $db->num_rows($query);
  if ($count > 0) { return true; }
  else { return false; }
}

function _checkExist($item, $column) {
  global $hndb;
  $db = DB::getInstance();
  $query = "SELECT id FROM ".$hndb['table']." WHERE ".$column."='".$item."'";
  $count = $db->num_rows($query);
  if ($count > 0) { return true; }
  else { return false; }
}

function _hashPassword($password) {
  global $hnauth;
  $hash_password = hash("SHA512", base64_encode(str_rot13(hash("SHA512", str_rot13($hnauth['salt'].$password)))));
	return $hash_password;
}

function _validateUsername($username) {
  return preg_match('/^[a-zA-Z0-9_]+$/', $username);
}

function _createPassword() {
  $chars = "abcdefghijkmnopqrstuvwxyz023456789";
  srand((double)microtime()*1000000);
  $i = 0;
  $pass = '' ;
  while ($i <= 7) {
    $num = rand() % 33;
    $tmp = substr($chars, $num, 1);
    $pass = $pass . $tmp;
    $i++;
  }
  return $pass;
}

?>