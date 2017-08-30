<?php

//	Duo Integration Key
$rcmail_config['IKEY'] = '';

//	Duo Secret Key
$rcmail_config['SKEY'] = '';

//	Duo API Host
$rcmail_config['HOST'] = '';

//	Duo trusted IPs
//      Remove comment below to configure the array 
//$rcmail_config['2FA_OVERRIDE'] = array("127.0.0.1", "X.X.X.X", "Y.Y.Y.Y");

//	Duo Application Key. Generate yourself (at least 40 characters long) and keep it secret from Duo.
//	You can generate a random string in Python with
//	import os, hashlib
//	print hashlib.sha256(os.urandom(32)).hexdigest()
//      *** Change the AKEY below ***
$rcmail_config['AKEY'] = '86c57ce7cd8d7cd4ebe776f8cffe8cf7f5b1b2177cd772d02afc747ab9f457a2';

?>
