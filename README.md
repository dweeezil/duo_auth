Roundcube lmr/duo_auth
==================

This is a Roundcube webmail plugin that enables [Duo Security](https://duo.com) Two Factor Authentication.

![image](https://duo.com/assets/img/documentation/duoweb/websdk_network_diagram.png)

It redirect to additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, hardware token code).

INSTALLATION
============
Install using Composer (https://getcomposer.org) from the root directory of your roundcube installation:

Run `$ composer update`

Run `$ composer dumpautoload`

Run `$ composer require "lmr/duo_auth:^1.0.9"`

CONFIGURATION
=============
1. Go into the plugins/duo_auth/ directory and modify duo_auth.conf as necessary.
Enter all keys necessary for integration with Duo in the duo_auth.conf file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested.
Specify the location of the redirect URI. After running the post-intall script above the `your_page_name_redirect.php` file should be located in the root roundcube directory. Modify the `header()` section of the `your_page_name_redirect.php` file to your particular needs. If you have a subdirectory where you access your WebMail application, adjust accordingly, for example: `header("Location: /WebMail/");` and `header("Location: /WebmMail/?_task=mail");`

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
