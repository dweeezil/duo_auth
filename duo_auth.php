<?php

# import namespace\class for use Duo Web v4 SDK (Software Development Kit) - Duo Universal Prompt from composer ./vendor/duosecurity/duo_universal_php/src/Client.php & DuoException.php
   use \Duo\DuoUniversal\Client;
   use \Duo\DuoUniversal\DuoException;
##########

# define new class plugin inside RoundCube Plugin App environment (RoundCube API SDK)
   class duo_sdk extends rcube_plugin {
##########

# define hook's set inside calling current RoundCube instance (fully initialize RoundCube proccess instance)
   function init() {
      $rcmail = rcmail::get_instance();

      $this->add_hook('login_after', array($this, '_main_handler_process_'));
      $this->add_hook('send_page', array($this, '_blocking_access_'));

   }
##########

# main Duo Web v4 SDK script user authenticator
   function _main_handler_process_() {
      $rcmail = rcmail::get_instance();

      $username = trim(rcube_utils::get_input_value('_user', rcube_utils::INPUT_POST, true));

      $e = null;
      $config = parse_ini_file("duo_auth.conf");

      if (isset($config["username"]) && in_array($username, $config["username"])) {

         header("Location: {$config["rc_path"]}?_task=mail");
         exit;

      } elseif (isset($config["ipaddr"])) {

         foreach($config["ipaddr"] as $ipaddr) {

            if ($this->ipaddr($_SERVER['REMOTE_ADDR'], $ipaddr)) {

               header("Location: {$config["rc_path"]}?_task=mail");
               exit;

            }

         }

      } else {

         try {
            $duo_client = new Client($config['client_id'], $config['client_secret'], $config['api_hostname'], $config['redirect_uri']);
         } catch (DuoException $e) {
            throw new ErrorException("*** Duo config error. Verify the values in duo_auth.conf are correct ***\n" . $e->getMessage());
         }

         $failmode = strtoupper($config['failmode']);

         try {
            $duo_client->healthCheck();
         } catch (DuoException $e) {

            $e->getMessage();

               if ($duo_failmode == "open") {
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

            if ($e !== null) {

            } else {

            $state = $duo_client->generateState();
            $duo_value = array("state:" => $state, "username:" => $username, "session_id:" => session_id());
               $_SESSION["_duo_auth_"] = $duo_value;

            $prompt_uri = $duo_client->createAuthUrl($username, $state);
               header("Location: $prompt_uri");
               exit;

            }

      }

   }
##########

# function blocking access while 2FA not finished
   function _blocking_access_() {

      if (isset($_SESSION["_duo_auth_"])) {

         header("Location: {$config["rc_path"]}your_page_name_blocking_access_while_2FA_not_approved.htm");
         exit;

      } else {}

   }
##########

# private function ipv4 address handler
   private function ipaddr($ip, $cidr) {

      if (!preg_match('/\//',$cidr)) {$cidr=$cidr . "/32";}

         list ($net, $mask) = explode ('/', $cidr);
         $ip_net = ip2long ($net);
         $ip_mask = ~((1 << (32 - $mask)) - 1);
         $ip_ip = ip2long ($ip);
         return (($ip_ip & $ip_mask) == ($ip_net & $ip_mask));
   }
##########

}

?>