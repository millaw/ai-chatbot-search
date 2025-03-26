document.addEventListener("DOMContentLoaded", function () {
    let input = document.getElementById("chatbot-input");
    let button = document.getElementById("chatbot-submit");
    let responseDiv = document.getElementById("chatbot-response");
    let resultsDiv = document.getElementById("chatbot-results");

    button.addEventListener("click", function () {
        let query = input.value.trim();
        if (!query) return;

        responseDiv.innerHTML = "<p>Thinking...</p>";
        resultsDiv.innerHTML = "";

        let data = new FormData();
        data.append("action", "chatbot_query");
        data.append("query", query);

        fetch("/wp-admin/admin-ajax.php", {
            method: "POST",
            body: data
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                responseDiv.innerHTML = `<p><strong>AI:</strong> ${data.data.ai_response}</p>`;
                
                if (data.data.search_results.length) {
                    resultsDiv.innerHTML = "<h3>Related Content:</h3><ul>";
                    data.data.search_results.forEach(post => {
                        resultsDiv.innerHTML += `<li><a href="${post.url}">${post.title}</a></li>`;
                    });
                    resultsDiv.innerHTML += "</ul>";
                }
            }
        });
    });
});
