<?php
if (!defined('ABSPATH')) exit;

function chatbot_display() {
    ob_start();
    ?>
    <div id="chatbot-container">
        <input type="text" id="chatbot-input" placeholder="Ask me anything..." />
        <button id="chatbot-submit">Ask</button>
        <div id="chatbot-response"></div>
        <div id="chatbot-results"></div>
    </div>
    <?php
    wp_enqueue_script('chatbot-script', plugin_dir_url(__FILE__) . '../assets/chatbot.js', ['jquery'], null, true);
    wp_enqueue_style('chatbot-style', plugin_dir_url(__FILE__) . '../assets/chatbot.css');
    return ob_get_clean();
}
add_shortcode('ai_chatbot', 'chatbot_display');
