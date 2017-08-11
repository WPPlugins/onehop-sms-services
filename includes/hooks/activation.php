<?php
/**
 * Onehopsmsservic Activation Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

$create_rulesets = ( 'CREATE TABLE IF NOT EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_rulesets (
        `ruleid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `rule_name` varchar(200) NOT NULL,
        `template` varchar(100) NOT NULL,
        `label` varchar(100) NOT NULL,
        `senderid` varchar(100) NOT NULL,
        `active` enum("1","0") NOT NULL DEFAULT "1"
        ) CHARSET=utf8;' );

$create_template = ( 'CREATE TABLE IF NOT EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_templates (
              `temp_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `temp_name` varchar(200) NOT NULL,
              `temp_body` text NOT NULL,
              `submitdate` datetime NOT NULL
            ) CHARSET=utf8;' );
