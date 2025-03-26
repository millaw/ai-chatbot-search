<?php
/*
Plugin Name: AI Chatbot Search
Description: AI-powered chatbot to help users find relevant content.
Version: 1.0
Author: Milla Wynn
*/

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Include chatbot logic & UI
include_once plugin_dir_path(__FILE__) . 'includes/chatbot-api.php';
include_once plugin_dir_path(__FILE__) . 'includes/chatbot-ui.php';
