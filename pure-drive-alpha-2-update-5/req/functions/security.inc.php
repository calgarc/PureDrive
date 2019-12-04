<?php

/*

Core Security functions
--------------------------------------

    1 salted($length = 5)
    2 subsalt()
    3 escape($data)
    4 sanitize($data)
    5 secure()
    6 LoggedIn($root)
    7 restrict()
    8 restrictlink()
    9 restrictform()
    10 encrypt($source)
    11 auth()
    12 ip()
    13 UserMatch()
    14 TwoFactAuth()
    15 admin()
    16 IsSession()
    17 inputArray()
    18 ipCheck()
    19 token()
    20 tokenMatch()
    21 dataHash()

--------------------------------------

*/

//Required files
if(!defined('inc')) {
   header("Location: ../../login"); //die();
}

//extra salts
function salted($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}


//subtract salt
function subsalt($data) {
    return substr($data, 0, -6);
}


//escape data
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


//sanitize data
function sanitize($data) {
    return preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $data);
}

//sanitize -> escape -> encrypt data
function secure($conn, $data) {
    $r = 1;

    return sanitize(escape(encrypt($conn, 1, $data)));
}


//logout
function loggedin($root) {
    if ($_SESSION['timeout'] + 1 * 60 < time()) {
        // session timed out
        session_unset();
        session_destroy();
        $login = $root.'login';
        session_regenerate_id(true);
        header("Location: $login"); //die();
    }
}


//restricted access
function restrict(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];

    $r = select($conn, "SELECT user_type, usalt FROM core_users WHERE core_username = :username", [':username' => $username]);

    $admin = $r['user_type'];
    $usalt = $r['usalt'];

    if('Administrator' != $admin) {
        if(id() != $usalt) {
            header("Location: ../drive/restrict");
        }
    }
}


//restricted links
function restrictlink(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];

    $r = select($conn, "SELECT user_type, usalt FROM core_users WHERE core_username = :username", [':username' => $username]);

    $admin = $r['user_type'];
    $usersalt = $r['usalt'];

    return $admin;
}


//restricted form
function restrictform(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];

    $r = select($conn, "SELECT user_type, usalt FROM core_users WHERE core_username = :username", [':username' => $username]);

    $admin = $r['user_type'];
    $usalt = $r['usalt'];

    if('Administrator' != $admin) {
        return 'disabled';
    }
}


//openssl encryption
//$source = encryption source
function encrypt(PDO $conn, $r, $source) {
    $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enableEncryption']);

    if($r['setting'] == 'Enabled') {

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enctype']);

        if( $r['setting'] == 'AES-256-CBC'){
            $enctype = 'AES-256-CBC';
        }else if($r['setting'] == 'AES-128-CBC'){
            $enctype = 'AES-128-CBC';
        }

        $ivlen = openssl_cipher_iv_length($cipher= $enctype);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($source, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher= $enctype);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $output = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);

        if (hash_equals($hmac, $calcmac)){
            return $output;
        }

    }else {
        return $source;
    }
}


//authenticate user
function auth(PDO $conn, $r) {

    $uid = UserId($conn, 1);
    $auth = 'false';

    $result = $conn->prepare("SELECT core_username FROM core_users WHERE usalt = :uname");
    $result->execute([':uname' => $uid]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if($r['core_username'] == $_SESSION['user']) {

        $auth = 'true';

    }

    $result = $conn->prepare("SELECT ip FROM core_users WHERE ip = :ip");
    $result->execute([':ip' => ip()]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $ip = $r['ip'];

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = :2fa");
    $result->execute([':2fa' => 'enable2fa']);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $istfa = $r['setting'];

    if ('Enabled'  == $istfa) {

      if($ip == '') {
        $auth = 'false';

      }else if($_SESSION['ip'] == $ip) {
        $auth = 'true';
      }

      if($_SESSION['ip'] == ip()) {
        $auth = 'true';
      }

    }else if($_SESSION['ip'] == ip()){
      $auth = 'true';
    }

    if('true' != $auth) {

        session_unset();
        session_destroy();
        $login = 'restrict';
        session_regenerate_id(true);
        header("Location: $login"); //die();

    }else {
      return 'true';
    }
}


//get users ip
function ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];

    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


// match user
function UserMatch() {

  if(isset($_GET['nid'])) {
    include('uid.php');

    if($_GET['nid']  != $userid) {

      session_unset();
      session_destroy();
      $login = 'restrict';
      session_regenerate_id(true);
      header("Location: $login"); //die();

    }else {
      return 'true';
    }
  }
}

// two factor Authentication
function TwoFactAuth(PDO $conn, $r, $authcode) {

    $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enable2fa']);


    $istfa = $r['setting'];

    $r = select($conn, "SELECT ip, core_email, core_username FROM core_users WHERE usalt = :uid", [':uid' => UserId()]);

    $user = $r['core_username'];
    $email = $r['core_email'];

    if ('Enable'  == $istfa) {
        if($_SESSION['user'] == $user){

            if (isset($authcode)){
                if ($authcode == $_SESSION['tfa']) {

                    $result = $conn->prepare("UPDATE core_users SET ip = '".ip()."' WHERE core_username= :uname");
                    $result->execute([':uname' => $user]);
                    $result->setFetchMode(PDO::FETCH_ASSOC);

                    session_regenerate_id(true);
                    $t = '1';
                }
            }

            if ($r['ip'] != ip()) {

                $tfa = salted();
                $_SESSION['tfa'] = $tfa;
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                $tfb = '<html><body>
                <h2 style="font-family:arial; text-align:center; font-size:30px;">Authentication code</h2>
                <h3 style="font-family:arial; color:#999999; font-size:48px; padding:5px; text-align:center;">'.$tfa.'</h3>
                <br /> <p style="text-align:center;">

                </p>
                </body></html>';

                if ('1' != $t) {
                    mail($email, "Authentication code", $tfb, $headers);
                }

                $e = 'false';

            }else if ($r['ip'] == ip()) {
                $_SESSION['ip'] = ip();
                $e = 'true';
            }
        }

    }else {
      $_SESSION['ip'] = ip();
      $e = 'true';
    }

    return $e;
}

//is admin
function admin(PDO $conn, $r) {
    $username = $_SESSION['user'];

    $r = select($conn, "SELECT user_type FROM core_users WHERE core_username = :username", [':username' => $username]);

    if('Administrator' == $r['user_type']) {
      return 'true';
    }
}

//check session
function IsSession($root) {
  $login = $root.'login';

  if(!isset($_SESSION['user'])) {
      header("Location: $login"); //die();
  }

  if ($_SESSION['user'] == '0'){
      header("Location: $login"); //die();
  }
}

//array input fields
function inputArray($data) {
  return str_replace(" ","", preg_replace("/[^a-zA-Z0-9\/_|+ .,-]/", '', $data));
}

//ip checkbox
function ipCheck(PDO $conn) {

  $r = 1;

  $r = select($conn, "SELECT setting FROM core_options WHERE option = :option", [':option' => 'enable2fa']);

  $istfa = $r['setting'];

  if ('Enabled'  == $istfa) {

    if($ip == '') {
      $auth = 'false';
    }

    else if($_SESSION['ip'] == $ip) {
      $auth = 'true';
    }

    if($_SESSION['ip'] == ip()) {
      $auth = 'true';
    }
  }

  else if($_SESSION['ip'] == ip()){
    $auth = 'true';
  }

  if('true' != $auth) {
    return false;
  }

  else {
    return true;
  }

}

//token generator
function token() {
  $token0 = str_split(salted(), 3);
  $tokens0 = array();
  
  $token1 = str_split(salted(), 3);
  $tokens1 = array();

  foreach($token0 as $token){
    $tokens0[] = hash('sha512', $token);
  }

  foreach($token1 as $token){
    $tokens1[] = hash('sha512', $token);
  }

  $tokens0 = implode('', $tokens0);
  $tokens1 = implode('', $tokens1);
  $token = $tokens0.$tokens1;

  return hash('sha512', $token);
}

//token matching
function tokenMatch($conn, $time) {
  $r = 1;
  $now = time();

  if('true' == auth($conn, 1)) {
      if(!isset($_SESSION['timer'])){
        $_SESSION['timer'] = time() + $time;
      }

      if($now > $_SESSION['timer']) {
        $_SESSION['timer'] = time() + $time;
        $token = token();
        $_SESSION['token'] = $token;

        if($_SESSION['token'] != $token) {
          $auth = 'false';
        }
      }
    }

  if('false' == $auth) {
    session_unset();
    session_destroy();
    $login = 'restrict';
    session_regenerate_id(true);
    header("Location: $login"); //die();
  }
}

//hashed data
function dataHash($data) {
  $data = str_split($data, 3);
  $tokens = array();

  foreach($data as $token){
    $tokens[] = hash('sha512', $token);
  }

  $tokens = implode('', $tokens);
  return hash('sha512', $tokens);
}


?>
