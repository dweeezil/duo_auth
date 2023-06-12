Roundcube lmr/duo_auth
==================

This is a Roundcube webmail plugin that enables [Duo Security](https://duo.com) Two Factor Authentication.

![image](https://duo.com/assets/img/documentation/duoweb/websdk_network_diagram.png)

It creates an additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, hardware token code).

INSTALLATION
============
Install using Composer (https://getcomposer.org) from the plugins directory of your roundcube installation:

Run `$ cd plugins`

Run `$ composer require lmr/duo_auth dev-master` you may see a warning message, please ignore and execute the post install script.

Run `$ php ./duo_auth/bin/install.php`

CONFIGURATION
=============
1. Go into the plugins/duo_auth/ directory and modify duo_auth.conf as necessary.
Enter all keys necessary for integration with Duo in the duo_auth.conf file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested in the config.inc.php there.

2. Add the following line to your roundcube configuration file - config.inc.php located in config/config.inc.php

`$config['session_storage'] = 'php';`

3. Modify your PHP config file (php.ini) as follows:

`session.save_handler = files`

`session.save_path = "/var/lib/php/sessions"`

CREDITS
=======
Author: Alexios Polychronopoulos - Wrote duo_auth for Roundcube.

Author: Leonardo Mariño-Ramírez - Updated the plugin for compatibility with Roundcube 1.3.0.

Author: Johnson Chow - Added support for IPv4 CIDR matching and 2FA overrride for specific users.

Author: Pavlo Lyha - Rewrote the plugin to be compatible with Duo Web v4 SDK.
