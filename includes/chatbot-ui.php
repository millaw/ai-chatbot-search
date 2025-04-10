<?php
class AI_Chatbot_UI {
    private static $instance = null;
    
    private function __construct() {
        add_shortcode('ai_chatbot', array($this, 'chatbot_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function enqueue_scripts() {
        wp_register_style(
            'ai-chatbot-css',
            AI_CHATBOT_PLUGIN_URL . 'assets/chatbot.css',
            array(),
            AI_CHATBOT_VERSION
        );

        wp_register_script(
            'ai-chatbot-js',
            AI_CHATBOT_PLUGIN_URL . 'assets/chatbot.js',
            array('jquery'),
            AI_CHATBOT_VERSION,
            true
        );

        wp_localize_script('ai-chatbot-js', 'ai_chatbot_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_chatbot_nonce')
        ));

        wp_enqueue_style('ai-chatbot-css');
        wp_enqueue_script('ai-chatbot-js');
    }
    
    public function chatbot_shortcode($atts) {
        wp_enqueue_style('ai-chatbot-css');
        wp_enqueue_script('ai-chatbot-js');
        
        ob_start();
        ?>
        <div class="ai-chatbot-container">
            <div class="ai-chatbot-header">
                <h3>AI Assistant</h3>
                <button class="ai-chatbot-close">&times;</button>
            </div>
            <div class="ai-chatbot-messages"></div>
            <div class="ai-chatbot-input">
                <input type="text" placeholder="Ask me anything..." class="ai-chatbot-text-input">
                <button class="ai-chatbot-send">Send</button>
            </div>
            <div class="ai-chatbot-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
