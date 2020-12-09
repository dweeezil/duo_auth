Roundcube lmr/duo_auth
==================

This is a Roundcube webmail plugin that enables [Duo Security](https://duo.com) Two Factor Authentication.

![image](https://duo.com/assets/img/documentation/duoweb/websdk_network_diagram.png)

It creates an additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, hardware token code).

INSTALLATION
============
Install using Composer (https://getcomposer.org) from the root directory of your roundcube installation:

Run `$ composer require lmr/duo_auth`

Run `$ composer dumpautoload -o`

Run `cd plugins/duo_auth/`

Run `ln -s ../../vendor/duosecurity/duo_api_php/src/* .`

CONFIGURATION
=============
Copy `config.inc.php.dist` to `config.inc.php` and modify as necessary.
Enter all keys necessary for integration with Duo in the config.inc.php file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested in the config.inc.php there.

CREDITS
=======
Author: Alexios Polychronopoulos - Wrote duo_auth for Roundcube.

Author: Leonardo Mariño-Ramírez - Updated the plugin for compatibility with Roundcube 1.3.0.

Author: Johnson Chow - Added support for IPv4 CIDR matching and 2FA overrride for specific users.
