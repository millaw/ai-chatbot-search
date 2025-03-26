jQuery(document).ready(function($) {
    $('.ai-chatbot-container').each(function() {
        const $container = $(this);
        const $messages = $container.find('.ai-chatbot-messages');
        const $input = $container.find('.ai-chatbot-text-input');
        const $sendBtn = $container.find('.ai-chatbot-send');
        const $results = $container.find('.ai-chatbot-results');
        const $closeBtn = $container.find('.ai-chatbot-close');
        
        function addMessage(role, content) {
            const messageClass = role === 'user' ? 'user-message' : 'bot-message';
            $messages.append(`<div class="ai-chatbot-message ${messageClass}">${content}</div>`);
            $messages.scrollTop($messages[0].scrollHeight);
        }
        
        function showResults(results) {
            if (results.length === 0) {
                $results.html('<p>No results found.</p>');
                return;
            }
            
            let html = '<div class="ai-chatbot-results-header"><h4>Related Content</h4></div><ul>';
            results.forEach(result => {
                html += `
                    <li>
                        <a href="${result.url}" target="_blank">${result.title}</a>
                        <p>${result.excerpt}</p>
                    </li>
                `;
            });
            html += '</ul>';
            $results.html(html);
        }
        
        function sendMessage() {
            const message = $input.val().trim();
            if (!message) return;
            
            addMessage('user', message);
            $input.val('');
            $sendBtn.prop('disabled', true);
            
            $.ajax({
                url: ai_chatbot_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'ai_chatbot_query',
                    message: message,
                    security: ai_chatbot_vars.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        addMessage('bot', response.data.ai_response);
                        showResults(response.data.search_results);
                    } else {
                        addMessage('bot', 'Sorry, there was an error processing your request.');
                    }
                },
                error: function() {
                    addMessage('bot', 'Sorry, there was an error connecting to the server.');
                },
                complete: function() {
                    $sendBtn.prop('disabled', false);
                }
            });
        }
        
        $sendBtn.on('click', sendMessage);
        $input.on('keypress', function(e) {
            if (e.which === 13) {
                sendMessage();
            }
        });
        
        $closeBtn.on('click', function() {
            $container.hide();
        });
    });
});
