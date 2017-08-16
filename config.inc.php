<?php

//	Duo Integration Key. 
$rcmail_config['IKEY'] = '';

//	Duo Secret Key
$rcmail_config['SKEY'] = '';

//	Duo API Host
$rcmail_config['HOST'] = '';

//	Duo trusted IPs
$rcmail_config['2FA_OVERRIDE'] = array("127.0.0.1", "X.X.X.X", "Y.Y.Y.Y");

//	Duo Application Key. Generate yourself (at least 40 characters long) and keep it secret from Duo.
//	You can generate a random string in Python with
//	import os, hashlib
//	print hashlib.sha256(os.urandom(32)).hexdigest()
$rcmail_config['AKEY'] = '';

?>
