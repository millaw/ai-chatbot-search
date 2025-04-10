<?php
class AI_Chatbot_API {
    private static $instance = null;
    private $openai_key;
    
    private function __construct() {
        $this->openai_key = $this->decrypt_key(get_option('ai_chatbot_openai_key'));
        
        add_action('wp_ajax_ai_chatbot_query', array($this, 'handle_chatbot_query'));
        add_action('wp_ajax_nopriv_ai_chatbot_query', array($this, 'handle_chatbot_query'));
        
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function encrypt_key($key) {
        return base64_encode($key);
    }

    private function decrypt_key($key) {
        return base64_decode($key);
    }
    
    public function register_settings() {
        register_setting('ai_chatbot_options', 'ai_chatbot_openai_key', array(
            'sanitize_callback' => array($this, 'encrypt_key')
        ));

        add_settings_section(
            'ai_chatbot_settings_section',
            'API Settings',
            array($this, 'settings_section_callback'),
            'ai_chatbot_options'
        );

        add_settings_field(
            'ai_chatbot_openai_key',
            'OpenAI API Key',
            array($this, 'openai_key_callback'),
            'ai_chatbot_options',
            'ai_chatbot_settings_section'
        );
    }
    
    public function settings_section_callback() {
        echo '<p>Enter your OpenAI API key below. Get your key from <a href="https://platform.openai.com/account/api-keys" target="_blank">OpenAI</a>.</p>';
    }
    
    public function openai_key_callback() {
        $key = $this->decrypt_key(get_option('ai_chatbot_openai_key'));
        echo '<input type="password" id="ai_chatbot_openai_key" name="ai_chatbot_openai_key" value="' . esc_attr($key) . '" class="regular-text">';
    }
    
    public function add_admin_menu() {
        add_options_page(
            'AI Chatbot Settings',
            'AI Chatbot',
            'manage_options',
            'ai_chatbot_options',
            array($this, 'options_page')
        );
    }
    
    public function options_page() {
        ?>
        <div class="wrap">
            <h1>AI Chatbot Search Settings</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('ai_chatbot_options');
                do_settings_sections('ai_chatbot_options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function handle_chatbot_query() {
        check_ajax_referer('ai_chatbot_nonce', 'security');
        
        if (!isset($_POST['message']) || empty($_POST['message'])) {
            wp_send_json_error('Empty message');
            return;
        }
        
        $message = sanitize_text_field($_POST['message']);
        
        // First search WordPress content
        $search_results = $this->search_wordpress_content($message);
        
        // Then get AI response
        $ai_response = $this->get_ai_response($message, $search_results);
        
        wp_send_json_success(array(
            'ai_response' => $ai_response,
            'search_results' => $search_results
        ));
    }
    
    private function search_wordpress_content($query) {
        $args = array(
            's' => $query,
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'posts_per_page' => 5
        );
        
        $search = new WP_Query($args);
        $results = array();
        
        if ($search->have_posts()) {
            while ($search->have_posts()) {
                $search->the_post();
                $results[] = array(
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'excerpt' => wp_trim_words(get_the_excerpt(), 20)
                );
            }
            wp_reset_postdata();
        }
        
        return $results;
    }
    
    private function get_ai_response($message, $search_results = array()) {
        if (empty($this->openai_key)) {
            return 'The chatbot is not properly configured. Please contact the site administrator.';
        }
        
        $context = "You are a helpful assistant for a WordPress website. ";
        $context .= "Here are some search results from the website that might be relevant to the user's question:\n";
        
        foreach ($search_results as $result) {
            $context .= "- " . $result['title'] . ": " . $result['excerpt'] . "\n";
        }
        
        $context .= "\nPlease provide a helpful response to the user's question, using the above information when relevant. ";
        $context .= "If the search results don't answer the question, provide a general helpful response.";
        
        $messages = array(
            array("role" => "system", "content" => $context),
            array("role" => "user", "content" => $message)
        );
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openai_key
            ),
            'body' => json_encode(array(
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500
            )),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return 'Sorry, I encountered an error processing your request.';
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['choices'][0]['message']['content'])) {
            return $body['choices'][0]['message']['content'];
        }
        
        return 'Sorry, I couldn\'t generate a response to your question.';
    }
}
