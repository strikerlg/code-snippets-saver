<?php

require_once HNAUTH_DIR . 'lang.php';
require_once HNAUTH_DIR . 'class.session.php';
//require_once HNAUTH_DIR . 'class.mysqli.php'; // already been declared by the app
require_once HNAUTH_DIR . 'class.phpmailer.php';
require_once HNAUTH_DIR . 'functions.php';

/*
* Check if user is logged in
* Return true if yes, false if no
*/
function is_user_logged_in() {
  global $lang, $loc, $responseMsg;
  $session = new Session();
  if ($session->getItem('username') !== '' && $session->getItem('userlevel') !== '') {
    if (_checkUsername($session->getItem('username'))) return true;
    else return false;
  } else {
    return false;
  }
}

/*
* Login
* Return true if success, false if fail
* @params $username, $password
*/
function login($username, $password) {
  global $hndb, $lang, $loc, $responseMsg;
  $session = new Session();
  $db = DB::getInstance();
  $username = $db->filter($username);
  $password = $db->filter($password);
  if ($username == '' || $password == '') {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['all_fields_required'];
    return false;
  } else {
    $query = "SELECT password FROM ".$hndb['table']." WHERE username='".$username."'";
    if ($db->num_rows($query) > 0) {
      list($mysql_password) = $db->get_row($query);
      if (_hashPassword($password) !== $mysql_password) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['login_failed'];
        return false;
      } else {
        $userlevel = $row['userlevel'];
        $session->setItem('username', $username);
        $session->setItem('userlevel', $userlevel);
        $session->save();
        return true;
      }
    } else {
      $responseMsg['hnauth'] = $lang['hnauth'][$loc]['login_failed'];
      return false;
    }
  }
}

/*
* Register
* Return true if success, false if fail
* @params $username, $email, $password
*/
function register($username, $email, $password, $antispam = '') {
  global $hndb, $lang, $loc, $responseMsg, $hnauth;
  $session = new Session();
  $db = DB::getInstance();
  $username = $db->filter($username);
  $email = $db->filter($email);
  $password = $db->filter($password);
  if ($antispam !== '') {
    return false;
  } else if (_checkExist($username, 'username')) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_already_exist'];
    return false;
  } else if (strlen($username) == 0 || strlen($password) == 0 || strlen($email) == 0) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['all_fields_required'];
    return false;
  } else if (strlen($username) > 30) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_too_long'];
    return false;
  } else if (strlen($username) < 3) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_too_short'];
    return false;
  } else if (strlen($password) > 100) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['password_too_long'];
    return false;
  } else if (strlen($password) < 8) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['password_too_short'];
    return false;
  } else if (!_validateUsername($username)) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_invalid'];
    return false;
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['email_invalid'];
    return false;
  } else {
    $hash_password = _hashPassword($password);
    $data = array(
		'username' => $username,
		'password' => $hash_password,
		'email' => $email
	);
	$add_query = $db->insert($hndb['table'], $data);
	if ($add_query) {
		$session->setItem('username', $username);
		$session->setItem('userlevel', $hnauth['default_userlevel']);
		$session->save();
		return true;
	}
  }
}

/*
* Request reset password
* Return true if successfully sent reset password, false if not
* @params $email
*/
function request_reset_password($email) {
  global $hndb, $lang, $loc, $hnmail, $responseMsg;
  $db = DB::getInstance();
  $email = $db->filter($email);
  if ($email == '') {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['field_empty'];
    return false;
  } else {
    $query = "SELECT username FROM ".$hndb['table']." WHERE email='".$email."'";
    if ($db->num_rows($query) > 0) {
      $random_password = _createPassword();
      $hash_r_password = _hashPassword($random_password);
      list($username) = $db->get_row($query);
    
      $mail = new PHPMailer;
      $mail->From = $hnmail['from'];
      $mail->FromName = $hnmail['from_name'];
      $mail->AddAddress($email, $username); // Add a recipient
      $mail->IsHTML(true); // Set email format to HTML
      $mail->Subject = 'Your reset password for '.$hnmail['from_name'];
      $mail->Body    = '<span style="color:#888;text-style:italic">Hi, I\'m a Robot, please don\'t reply!</span>';
      $mail->Body    .= '<hr style="border:0;border-bottom:1px dashed #555;margin:20px auto">';
      $mail->Body    .= 'Username: <b>'.$username.'</b>';
      $mail->Body    .= '<br/>Reset password: <b style="color:red;">'.$random_password.'</b>';
      $mail->Body    .= '<hr style="border:0;border-bottom:1px dashed #555;margin:20px auto">';
      $mail->Body    .= 'Regards,<br/>';
      $mail->Body    .= '<i>'.$hnmail['from_name'].'</i>';
      $mail->Body    .= '<br/><i><a href="'.$hnmail['site_url'].'">'.$hnmail['site_url'].'</a></i>';

      if(!$mail->Send()) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['sending_mail_failed'] . $mail->ErrorInfo;
        return false;
      } else {
		$data_update = array(
			'password' => $hash_r_password
		);
		$where_clause = array(
			'email' => $email
		);
        $updated = $db->update($hndb['table'], $data_update, $where_clause, 1);
        if ($updated) {
			return true;
		}
      }
    } else {
      $responseMsg['hnauth'] = $lang['hnauth'][$loc]['email_not_exist_in_database'];
      return false;
    }
  }
}

/*
* Change password
* Return true if success, false if fail
* @params $username, $old_password, $new_password
*/
function change_password($username, $old_password, $new_password) {
  global $hndb, $lang, $loc, $responseMsg;
  $db = DB::getInstance();
  $username = $db->filter($username);
  $old_password = $db->filter($old_password);
  $new_password = $db->filter($new_password);
  if ($old_password == '' || $new_password == '') {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['all_fields_required'];
    return false;
  } else {
    $query = "SELECT password FROM ".$hndb['table']." WHERE username='".$username."'";
    if ($db->num_rows($query) > 0) {
      list($mysql_password) = $db->get_row($query);
      if ($mysql_password !== _hashPassword($old_password)) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['incorrect_password'];
        return false;
      } else {
        $hash_new_password = _hashPassword($new_password);
        $data_update = array(
			'password' => $hash_new_password
		);
		$where_clause = array(
			'username' => $username
		);
		$updated = $db->update($hndb['table'], $data_update, $where_clause, 1);
		if ($updated) {
			$responseMsg['hnauth'] = $lang['hnauth'][$loc]['change_password_success'];
			return true;
		}
      }
    } else {
      $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_not_exist_in_database'];
      return false;
    }
  }
}

/*
* Change email address
* Return true if success, false if fail
* @params $username, $current_password, $new_email
*/
function change_email($username, $current_password, $new_email) {
  global $hndb, $lang, $loc, $responseMsg;
  $db = DB::getInstance();
  $username = $db->filter($username);
  $current_password = $db->filter($current_password);
  $new_email = $db->filter($new_email);
  if ($current_password == '' || $new_email == '') {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['all_fields_required'];
    return false;
  } else {
    $query = "SELECT password FROM ".$hndb['table']." WHERE username='".$username."'";
    if ($db->num_rows($query) > 0) {
      list($mysql_password) = $db->get_row($query);
      if (_hashPassword($current_password) !== $mysql_password) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['incorrect_password'];
        return false;
      } else if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['email_invalid'];
        return false;
      } else {
		$data_update = array(
			'email' => $new_email
		);
		$where_clause = array(
			'username' => $username
		);
		$updated = $db->update($hndb['table'], $data_update, $where_clause, 1);
		if ($updated) {
			$responseMsg['hnauth'] = $lang['hnauth'][$loc]['change_email_success'];
			return true;
		}
      }
    } else {
      $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_not_exist_in_database'];
      return false;
    }
  }
}

/*
* Delete account
* Return true if success, false if fail
* @params $username, $current_password
*/
function delete_account($username, $current_password) {
  global $hndb, $lang, $loc, $responseMsg;
  $session = new Session();
  $db = DB::getInstance();
  $username = $db->filter($username);
  $current_password = $db->filter($current_password);
  if ($current_password == '') {
    $responseMsg['hnauth'] = $lang['hnauth'][$loc]['field_empty'];
    return false;
  } else {
    $query = "SELECT password FROM ".$hndb['table']." WHERE username='".$username."'";
    if ($db->num_rows($query) > 0) {
      list($mysql_password) = $db->get_row($query);
      if (_hashPassword($current_password) !== $mysql_password) {
        $responseMsg['hnauth'] = $lang['hnauth'][$loc]['incorrect_password'];
        return false;
      } else {
		$data_delete = array(
			'username' => $username
		);
		$deleted = $db->delete($hndb['table'], $data_delete, 1);
		if ($deleted) {
			if ($session->getItem('username') !== '' && $session->getItem('userlevel')) {
				$session->destroy();
			}
			return true;
		}
      }
    } else {
      $responseMsg['hnauth'] = $lang['hnauth'][$loc]['username_not_exist_in_database'];
      return false;
    }
  }
}

/*
* Log out user when receiving parameter "?logout"
* eg: index.php?logout
*/
if (isset($_GET['logout'])) {
  $session = new Session();
  if ($session->getItem('username') !== '' && $session->getItem('userlevel') !== '') {
    $session->destroy();
    header('Location: index.php');
  } else {
    header('Location: index.php');
  }
}

?>