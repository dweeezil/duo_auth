Roundcube lmr/duo_auth
==================

This is a Roundcube webmail plugin that enables Duo Security Two Factor Authentication.

It creates an additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, code).

INSTALLATION
============

Install using Composer (https://getcomposer.org) from the root directory of your roundcube installation:

$ composer require lmr/duo_auth

CONFIGURATION
=============
Enter all keys necessary for integration with Duo in the config.inc.php file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested in the config.inc.php there.

CREDITS
=======
Author: Alexios Polychronopoulos - Wrote duo_auth for Roundcube.

Author: Leonardo Mariño-Ramírez - Updated the plugin for compatibility with Roundcube 1.3.0.
