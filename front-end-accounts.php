<?php
/**
 * Plugin Name: Front End Accounts
 * Plugin URI: https://github.com/chrisguitarguy/Front-End-Accounts
 * Description: Create account pages on the frontend of your website.
 * Version: 0.1
 * Text Domain: front-end-accounts
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: MIT
 *
 * Copyright (c) 2013 Christopher Davis <http://christopherdavis.me>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category    WordPress
 * @package     FrontEndAcounts
 * @since       0.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace Chrisguitarguy\FrontEndAccounts;

!defined('ABSPATH') && exit;

define('FE_ACCOUNTS_VER', '0.1');
define('FE_ACCOUNTS_ROLE', 'fe_unpriv_user');
define('FE_ACCOUNTS_TD', 'front-end-accounts');

require_once __DIR__ . '/inc/Autoloader.php';
require_once __DIR__ . '/inc/functions.php';

Autoloader::register();

register_activation_hook(__FILE__, 'frontend_accounts_activate');
register_deactivation_hook(__FILE__, 'frontend_accounts_deactivate');

add_action('plugins_loaded', 'frontend_accounts_load', 5);
add_action('plugins_loaded', 'frontend_accounts_init', 100);
