<?php
/**
 * WP Blade(One)
 *
 * @package   WP_Blade(One)
 * @copyright Copyright(c) 2020, dievardump
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: WP Blade(One)
 * Plugin URI: https://github.com/dievardump/wp-bladeone-plugin
 * Description: Blade(One) syntaxing in wordpress themes
 * Version: 0.0.1
 * Author: dievardump
 * Author URI: https://github.com/dievardump
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-bladeone-plugin
 * Domain Path: languages
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// composer deps
if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

/**
 * Get the plugin object.
 *
 * @return \WP_BladeOne\Plugin
 */
function wp_bladeone()
{
    static $instance;

    if (null === $instance) {
        $instance = new \WP_BladeOne\Plugin();
    }

    return $instance;
}

/**
 * Alias to wp_bladeone
 */
function wp_blade()
{
    return wp_bladeone();
}

if (!defined('WP_BLADEONE_VIEWS')) {
    define('WP_BLADEONE_VIEWS', get_stylesheet_directory());
}

if (!defined('WP_BLADEONE_CACHE')) {
    define('WP_BLADEONE_CACHE', WP_CONTENT_DIR . '/cache/.wp-bladeone-cache');
}

if (!file_exists(WP_BLADEONE_CACHE)) {
    wp_mkdir_p(WP_BLADEONE_CACHE);
}

if (!defined('WP_BLADEONE_MODE')) {
    define('WP_BLADEONE_MODE', \eftec\bladeone\BladeOne::MODE_AUTO);
}

$blade = new \eftec\bladeone\BladeOne(WP_BLADEONE_VIEWS, WP_BLADEONE_CACHE, WP_BLADEONE_MODE);
wp_bladeone()
    ->setCompiler($blade);

require __DIR__ . '/php/hooks.php';
