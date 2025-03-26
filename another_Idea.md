# AI-Powered Chatbot Search Project

## **Step 1: Set Up the External AI Server**
### **1.1 Install Required Packages**
Ensure your server has Python and necessary dependencies:
```bash
sudo apt update && sudo apt install python3 python3-pip
pip install fastapi uvicorn elasticsearch
```

### **1.2 Install and Configure Elasticsearch**
Download and install Elasticsearch (self-hosted version for free usage):
```bash
wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.10.2-linux-x86_64.tar.gz
 tar -xzf elasticsearch-7.10.2-linux-x86_64.tar.gz
cd elasticsearch-7.10.2
./bin/elasticsearch
```

## **Step 2: Develop the AI Chatbot Search API**
### **2.1 Create API Server (`server/app.py`)**
```python
from fastapi import FastAPI
from elasticsearch import Elasticsearch

app = FastAPI()
es = Elasticsearch("http://localhost:9200")

@app.get("/search/")
def search(q: str):
    response = es.search(index="website", body={"query": {"match": {"content": q}}})
    results = [{"url": hit["_source"]["url"], "title": hit["_source"]["title"], "snippet": hit["_source"]["content"][:200]} for hit in response["hits"]["hits"]]
    return {"results": results}
```

### **2.2 Run the API Server**
```bash
uvicorn app:app --host 0.0.0.0 --port 8000 --reload
```

## **Step 3: Develop WordPress Plugin for Integration**
### **3.1 Plugin Folder Structure**
```
ai-search-bot/
│── ai-search-bot.php
│── assets/
│   ├── chatbot-style.css
│   ├── chatbot.js
│── includes/
│   ├── api-handler.php
```

### **3.2 Create the Main Plugin File (`wordpress-plugin/ai-search-bot.php`)**
```php
<?php
/**
 * Plugin Name: AI Chatbot Search
 * Description: Integrates an AI-powered chatbot search bot with WordPress using a shortcode.
 * Version: 1.0
 * Author: Your Name
 */

define('AI_SEARCH_BOT_URL', 'http://your-external-server-ip:8000/search/');

// Register shortcode [ai_chatbot]
function ai_chatbot_shortcode() {
    ob_start();
    ?>
    <div id="chat-container">
        <div id="chat-box"></div>
        <input type="text" id="chat-input" placeholder="Ask me anything...">
        <button onclick="sendMessage()">Send</button>
    </div>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'assets/chatbot-style.css'; ?>">
    <script src="<?php echo plugin_dir_url(__FILE__) . 'assets/chatbot.js'; ?>"></script>
    <?php
    return ob_get_clean();
}
add_shortcode('ai_chatbot', 'ai_chatbot_shortcode');
```

### **3.3 JavaScript for Chatbot (`wordpress-plugin/assets/chatbot.js`)**
```js
document.addEventListener("DOMContentLoaded", function () {
    window.sendMessage = function () {
        let input = document.getElementById("chat-input");
        let message = input.value.trim();
        if (message === "") return;
        
        let chatBox = document.getElementById("chat-box");
        chatBox.innerHTML += `<div class='user-message'>${message}</div>`;
        input.value = "";
        
        fetch(`http://your-external-server-ip:8000/search/?q=${message}`)
            .then(response => response.json())
            .then(data => {
                let botResponse = "<div class='bot-message'>";
                data.results.forEach(item => {
                    botResponse += `<p><a href="${item.url}">${item.title}</a>: ${item.snippet}</p>`;
                });
                botResponse += "</div>";
                chatBox.innerHTML += botResponse;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    };
});
```

### **3.4 Chatbot Styling (`wordpress-plugin/assets/chatbot-style.css`)**
```css
#chat-container {
    width: 300px;
    height: 400px;
    border: 1px solid #ccc;
    display: flex;
    flex-direction: column;
}
#chat-box {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px;
}
.user-message {
    text-align: right;
    color: blue;
    margin: 5px 0;
}
.bot-message {
    text-align: left;
    color: green;
    margin: 5px 0;
}
#chat-input {
    width: 80%;
    padding: 5px;
}
button {
    width: 18%;
}
```

## **Step 4: Deploy and Use the Chatbot**
### **4.1 Install the Plugin**
1. Upload `wordpress-plugin/ai-search-bot/` to `wp-content/plugins/`.
2. Activate the plugin in WordPress Admin > Plugins.
3. Use `[ai_chatbot]` in posts/pages to display the chatbot.

---
✅ **Now, your WordPress site has an AI-powered chatbot search bot powered by an external FastAPI server!** 🚀
