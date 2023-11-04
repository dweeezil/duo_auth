<?php

# require lib file's for execution code page
   require "./vendor/duosecurity/duo_universal_php/src/Client.php";
   require "./vendor/duosecurity/duo_universal_php/src/DuoException.php";
   require "./vendor/firebase/php-jwt/src/JWT.php";
   require "./vendor/firebase/php-jwt/src/Key.php";
   require "./vendor/firebase/php-jwt/src/BeforeValidException.php";
   require "./vendor/firebase/php-jwt/src/ExpiredException.php";
   require "./vendor/firebase/php-jwt/src/SignatureInvalidException.php";
   require "./vendor/firebase/php-jwt/src/JWTExceptionWithPayloadInterface.php";

# register using namespace/class from lib file's
   use \Firebase\JWT\JWT;
   use \Firebase\JWT\Key;
   use \Firebase\JWT\BeforeValidException;
   use \Firebase\JWT\ExpiredException;
   use \Firebase\JWT\SignatureInvalidException;
   use \Duo\DuoUniversal\Client;
   use \Duo\DuoUniversal\DuoException;

# define script variable environment
   $config = parse_ini_file("./plugins/duo_auth/duo_auth.conf");
   $duo_client = new Client($config['client_id'], $config['client_secret'], $config['api_hostname'], $config['redirect_uri']);

# define attribute user Duo Web v4 SDK session state. Change session name if needed
   $state = $_GET["state"];
   $code = $_GET["duo_code"];
   session_name("roundcube_sessid");
   session_start();
   $saved_state = $_SESSION["_duo_auth_"]["state:"];
   $username = $_SESSION["_duo_auth_"]["username:"];
   $session_id = $_SESSION["_duo_auth_"]["session_id:"];

   if ($saved_state !== $state) {
      $_SESSION = array();

      if (ini_get("session.use_cookies")) {
         $params = session_get_cookie_params();
         setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
      }
      session_destroy();

      header("Location: {$config["rc_path"]}");
      exit;

   } else {

      unset($_SESSION["_duo_auth_"]);

# calculate result token from Duo Web v4 SDK & execute corresponding action (access allow or deny)
      $decoded_token = $duo_client->exchangeAuthorizationCodeFor2FAResult($code, $username);
         $result = json_encode($decoded_token);
         $result = json_decode($result, true);

      if ($result["auth_context"]["result"] == 'success') {
         header("Location: {$config["rc_path"]}?_task=mail");
         exit;
      } else {
         $_SESSION = array();

         if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
         }
         session_destroy();

         header("Location: {$config["rc_path"]}");
         exit;
      }

   }

?>
