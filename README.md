# AI Chatbot Search Plugin for WordPress

## Overview
This WordPress plugin integrates an AI-powered chatbot that helps users find relevant content on your website. It combines OpenAI's GPT model with a WordPress content search to enhance user experience.

## Features
- AI-driven chatbot for intelligent responses.
- Searches WordPress posts and pages based on user queries.
- Simple shortcode `[ai_chatbot]` for easy integration.
- AJAX-based interface for seamless interaction.
- Lightweight and easy to install.

## Installation
1. Upload the `ai-chatbot-search` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Add `[ai_chatbot]` to any post or page to display the chatbot.

## File Structure
```
/wp-content/plugins/ai-chatbot-search/
│── ai-chatbot.php          # Main plugin file
│── includes/
│   ├── chatbot-api.php     # Handles AI & search logic
│   ├── chatbot-ui.php      # Registers shortcode & UI
│── assets/
│   ├── chatbot.js          # Chatbot frontend logic
│   ├── chatbot.css         # Chatbot styling
```

## Configuration
- Replace `your_openai_api_key` in `chatbot-api.php` with your OpenAI API key.
- Customize chatbot behavior by modifying `chatbot-api.php`.

## How to Get an OpenAI API Key
To use OpenAI for chatbot responses, you need an API key:
1. Go to [OpenAI's website](https://openai.com/)
2. Sign up or log in.
3. Navigate to the **API Keys** section in your account.
4. Generate a new API key.
5. Copy and replace `your_openai_api_key` in `chatbot-api.php`.

## Usage
- Insert `[ai_chatbot]` in any post or page where you want the chatbot to appear.
- Users can type queries, and the chatbot will provide AI-generated responses along with related WordPress content.

## Future Enhancements
- Custom AI training based on site content.
- Integration with external knowledge bases.
- Improved UI with voice interaction.

## License
This project is licensed under the **GPLv3** License.

