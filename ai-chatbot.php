<?php
/**
 * Plugin Name: AI Chatbot Search
 * Plugin URI: https://github.com/millaw/ai-chatbot-search
 * Description: AI-powered chatbot that helps users find relevant content on your WordPress site.
 * Version: 1.0.0
 * Author: Milla Wynn
 * Author URI: https://github.com/millaw
 * License: GPLv3
 */

defined('ABSPATH') or die('Direct access not allowed');

// Define plugin constants
define('AI_CHATBOT_VERSION', '1.0.0');
define('AI_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/chatbot-api.php';
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/chatbot-ui.php';

// Register activation hook
register_activation_hook(__FILE__, 'ai_chatbot_activate');

function ai_chatbot_activate() {
    // Add any activation setup here
    if (!get_option('ai_chatbot_openai_key')) {
        update_option('ai_chatbot_openai_key', '');
    }
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'ai_chatbot_deactivate');

function ai_chatbot_deactivate() {
    // Clean up if needed
}

// Initialize the plugin
function ai_chatbot_init() {
    AI_Chatbot_API::get_instance();
    AI_Chatbot_UI::get_instance();
}
add_action('plugins_loaded', 'ai_chatbot_init');
