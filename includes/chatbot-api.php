<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_chatbot_query', 'chatbot_query_handler');
add_action('wp_ajax_nopriv_chatbot_query', 'chatbot_query_handler'); // Allow non-logged users

function chatbot_query_handler() {
    $query = sanitize_text_field($_POST['query']);
    if (empty($query)) {
        wp_send_json_error("Empty query");
    }

    // OpenAI API (Replace with your OpenAI Key)
    $apiKey = "your_openai_api_key";
    $response = wp_remote_post("https://api.openai.com/v1/chat/completions", [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
        ],
        'body' => json_encode([
            'model' => 'gpt-4',
            'messages' => [['role' => 'user', 'content' => $query]],
            'max_tokens' => 150
        ]),
    ]);

    $ai_response = json_decode(wp_remote_retrieve_body($response), true);
    $ai_text = $ai_response['choices'][0]['message']['content'] ?? 'Sorry, I could not understand.';

    // Search WordPress content
    $search_results = new WP_Query([
        's' => $query,
        'posts_per_page' => 3
    ]);

    $results = [];
    if ($search_results->have_posts()) {
        while ($search_results->have_posts()) {
            $search_results->the_post();
            $results[] = ['title' => get_the_title(), 'url' => get_permalink()];
        }
    }
    wp_reset_postdata();

    wp_send_json_success([
        "ai_response" => $ai_text,
        "search_results" => $results
    ]);
}
