<?php
/*
Plugin Name: Custom Authentication
Description: Custom registration and authentication system
Version: 1.0
Author: ghadghoud
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-custom-auth.php';
require_once plugin_dir_path(__FILE__) . 'includes/auth-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';

new CustomAuth();
