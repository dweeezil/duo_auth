<?php
/**
 * Two Factor Authentication using Duo Security for RoundCube
 *
 * @version 1.0.5
 *
 * Author(s): Alexios Polychronopoulos <dev@pushret.co.uk>
 * Author(s): Leonardo Marino-Ramirez <marino@marino-johnson.org>
 * Author(s): Johnson Chow <wschow@Comp.HKBU.Edu.HK>
 * Date: 06/7/2018
 */


require_once 'duo_web.php';
require_once 'Client.php';
require_once 'Auth.php';
require_once 'Requester.php';
require_once 'CurlRequester.php';

class duo_auth extends rcube_plugin 
{
	
	function init() 
	{
		$rcmail = rcmail::get_instance();
		
		$this->add_hook('login_after', array($this, 'login_after'));
		$this->add_hook('send_page', array($this, 'check_2FA'));
   	    	 
		$this->load_config();
	}

	//hook called after successful user/pass authentication.
	function login_after($args)
	{
		$rcmail = rcmail::get_instance();
		
		$this->register_handler('plugin.body', array($this, 'generate_html'));
		
		$ikey = $this->get('IKEY');
		$skey = $this->get('SKEY');
	        $host = $this->get('HOST');

        	$user = trim(rcube_utils::get_input_value('_user', rcube_utils::INPUT_POST, true));
		$D = new DuoAPI\Auth($ikey, $skey, $host);
		$result=$D->preauth($user);

		// bypass Roundcube users without DUO account
		if($result["response"]["response"]["result"] == "auth") {
		}
		else {
			$_SESSION['_Duo_2FAuth'] = True;
			header('Location: ?_task=mail');
		}

		// bypass local users
		if(in_array($user, $this->get('2FA_OVERRIDE_USERS'))) {
                        $_SESSION['_Duo_2FAuth'] = True;
                        header('Location: ?_task=mail');
                }


		// 2FA override with specific IPs 
		foreach($this->get('2FA_OVERRIDE') as $ip) {
			if($this->ipCIDRCheck($_SERVER['REMOTE_ADDR'],$ip)) {
				$_SESSION['_Duo_2FAuth'] = True;
				header('Location: ?_task=mail');
			}
		}

		//indicates that user/pass authentication has succeeded.
		$_SESSION['_Duo_Auth'] = True;
    	
		$rcmail->output->send('plugin');
	}
    
	//intermediate page for Duo 2FA. Fetches the Duo javascript, initializes Duo and renders the Duo iframe.
	function generate_html() 
	{
		$rcmail = rcmail::get_instance();
		$rcmail->output->set_pagetitle('Duo Authentication');
		
		$this->include_script('js/Duo-Web-v2.min.js');
		
		$ikey = $this->get('IKEY');
		$skey = $this->get('SKEY');
        	$host = $this->get('HOST');
        	$akey = $this->get('AKEY');

        	$user = trim(rcube_utils::get_input_value('_user', rcube_utils::INPUT_POST, true));
        	
		$sig_request = Duo::signRequest($ikey, $skey, $akey, $user);

		$content =	"<script>
						Duo.init({
							'host': '" . $host . "',
							'post_action': '.',
							'sig_request': '" . $sig_request . "'
						});
				</script>
				<center>	
					<iframe id=\"duo_iframe\" frameborder=\"0\" allowtransparency=\"true\" style=\"background: transparent;\">
					</iframe>
					<style>
					  #duo_iframe {
					    width: 100%;
					    min-width: 304px;
					    max-width: 620px;
					    height: 330px;
					    border: none;
					  }
				</style>
				</center>";	
		
		return($content);
	}
	
	
	//hook called on every roundcube page request. Makes sure that user is authenticated using 2 factors.
	function check_2FA($p)
	{
		$rcmail = rcmail::get_instance();
		
		//user has gone through 2FA
		if($_SESSION['_Duo_Auth'] && $_SESSION['_Duo_2FAuth']) 
		{
			return $p;
		}
		
		//login page has to allow requests that are not 2 factor authenticated.
		else if($rcmail->task == 'login')
		{
			return $p;
		}
		
		//checking 2nd factor of authentication.
		else if(isset($_POST['sig_response']))
		{
			$ikey = $this->get('IKEY');
			$skey = $this->get('SKEY');
			$akey = $this->get('AKEY');
			
			$resp = Duo::verifyResponse($ikey, $skey, $akey, $_POST['sig_response']);
			
			//successful 2FA login.
			if($resp != NULL)
			{
				//indicates successful Duo 2FA.
				$_SESSION['_Duo_2FAuth'] = True;
				
				//redirect to inbox.
				header('Location: ?_task=mail');
				return $p;
			}
			else {
				$this->fail();
			}
		}
		
		//in any other case, log the user out.
		$this->fail();
	}

	private function get($v)
	{
		return rcmail::get_instance()->config->get($v);
	}
	
	//unsets all the session variables used in the plugin, 
	//invalidates the user's session and redirects to the login page.
	private function fail() 
	{
		$rcmail = rcmail::get_instance();
		
		unset($_SESSION['_Duo_Auth']);
		unset($_SESSION['_Duo_2FAuth']);
		
		$rcmail->kill_session();
		header('Location: ?_task=login');
		
		exit;
	}	
	
	private function ipCIDRCheck ($IP, $CIDR) {
		if (!preg_match('/\//',$CIDR)) { $CIDR=$CIDR . "/32"; }

		list ($net, $mask) = explode ('/', $CIDR);
		$ip_net = ip2long ($net);
		$ip_mask = ~((1 << (32 - $mask)) - 1);
    		$ip_ip = ip2long ($IP);
    		return (($ip_ip & $ip_mask) == ($ip_net & $ip_mask));
	}



}
