<?php
/**
 * Onehopsmsservic Uninstall Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

$drop_create_rulesets = ( 'DROP TABLE IF EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_rulesets;' );
$drop_create_template = ( 'DROP TABLE IF EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_templates;' );
