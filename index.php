<?php
session_start();
require_once 'database.php';
require_once 'check_user.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Messaging App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
        }

        .received {
            background-color: #e9ecef;
            margin-right: auto;
        }

        .sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }

        .input-area {
            padding: 20px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }

        #messageInput {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #sendButton {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #sendButton:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="messages" id="messagesContainer">
            <!-- Messages will be inserted here -->
        </div>
        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Type your message...">
            <button id="sendButton">Send</button>
        </div>
    </div>

    <script>

        let userInput = prompt("Enter your login:");
        let user = null;
        if (userInput) {
            fetch("check_user.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "username=" + encodeURIComponent(userInput)
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "not_found") {
                    window.location.href = "not_found.html";
                } else {
                    user = data;
                    <?php
                    $result = $conn->query("SELECT * FROM message ORDER BY number ASC");
                    $messages = [];
                    
                    while ($row = $result->fetch_assoc()) {
                        $messages[] = $row;
                    }
                    ?>
                    let messages = <?php echo json_encode($messages); ?>;
                    let last = 0;  // Initialize last outside the loop
                    
                    messages.forEach(msg => {
                        if (parseInt(msg.user1, 10) == user) { 
                            addMessage(msg.message);
                        } else {
                            addMessage(msg.message, false);
                        }
                        last = Math.max(last, msg.number);
                    });

                    // Use setInterval for periodic checks
                    const pollInterval = setInterval(() => {
                        fetch("get_new_messages.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "last=" + last + "&user=" + user
                        })
                        .then(response => response.json())
                        .then(newMessages => {
                            newMessages.forEach(msg => {
                                if (parseInt(msg.user1, 10) == user) { 
                                    addMessage(msg.message);
                                } else {
                                    addMessage(msg.message, false);
                                }
                                last = Math.max(last, msg.number);
                            });
                        })
                        .catch(error => console.error("Polling error:", error));
                    }, 3000);  // Check every 5 seconds
                }
            })
            .catch(error => console.error("Error:", error));
        }


        const messagesContainer = document.getElementById('messagesContainer');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');

        // Add message function
        function addMessage(text, isSent = true) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', isSent ? 'sent' : 'received');
            messageDiv.textContent = text;
            messagesContainer.appendChild(messageDiv);
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Send message handler
        function sendMessage() {
            const message = messageInput.value.trim();
            if (user == 1){
                var recipientID = 2;
            } else {
                var recipientID = 1;
            }
            if (message) {
                fetch("send_message.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `message=${encodeURIComponent(message)}&user1=${user}&user2=${recipientID}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = ''; // Clear input
                    } else {
                        console.error("Send failed:", data.error);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        }

        // Event listeners
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    

    </script>
</body>
</html>

