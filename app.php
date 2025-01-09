<?php
    session_start();
    
    // Check if the user is logged in
    if (!isset($_SESSION['name'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sendit</title>
    <script src="js/script.js"></script>
    <link href='styles/style.css' rel='stylesheet'>
    <link href='styles/app.css' rel='stylesheet'>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <style>
        header {
            flex-direction: row;
            padding-left: 8%;
            padding-right: 8%;
            /*justify-content: space-around;*/
        }
        .logout{
            background-color: #aa0000;
        }
        .logout > a {
            text-decoration: none;
            color: white;
        }
        section.hero {
            width: 100%;
            padding: 0;
            overflow: hidden;
        }
        div.contacts{
            overflow-x: hidden;
        }
        div.search_container {
            display: flex;
            overflow-x: scroll;
            flex-wrap: wrap;
            padding: 0.25rem;
        }
        input#searchContacts {
            flex: 4;
        }
        button#searchContactButton{
            flex: 1;
            margin: 10px;
            color: white;
            background-color: #3d2d7d;
        }
        .message.sent {
            justify-self: right; 
            box-shadow: 3px 3px 6px #ccc;
        }
        .message.received {
            justify-self: left;
            box-shadow: 3px 3px 6px #ccc;
        }
        .message-form {
            margin: 0;
        }
    </style>
</head>
<body>
    <header style="text-align: center;">
        <div>
            <h3>Send it</h3>
            <h6>From any where</h6>
        </div>
        <div>
            <?php
                echo "<p>". $_SESSION['name']."</p>";
                echo "<button class='logout'><a href='logout.php'>Logout</a></button>";
                ?>
        </div>
    </header>
    
    <section class="hero">
        
        <div class="container">
            <!-- Contacts Section -->
            <div class="contacts">
                <!--<div class="search_container">-->
                <form action="" class="message-form" id="searchContactForm">
                    <input type="text" placeholder="Search Contacts" id="searchContacts" name="searchQuery" >
                    <button type="submit" id="searchContactButton" name="searchContactButton" >Search</button>    
                </form>
                <div id="resultMessage"></div>
                <div class="contact-list" id="contactList">
                    <!-- Contacts will be dynamically populated here -->
                </div>
            </div>
        
            <!-- Messages Section -->
            <div class="messages">
                <div class="message-list" id="messageList">
                    <!-- Messages will be dynamically populated here -->
                </div>
                <form class="message-form" id="messageForm">
                    <input type="text" placeholder="Type a message" id="messageInput">
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
        
    </section>
    
    <footer>
        
        <p>&copy; 2024 sendit. All Rights Reserved.</p>
        
    </footer>
    
    
    <script>
	
		// ################################## AJAX To Search for Friends ##################################################
		
        document.getElementById('searchContactForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent form submission
        
            const searchQuery = document.getElementById('searchContacts').value;
        
            // Make AJAX request
            fetch('php/search_contact_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ searchQuery: searchQuery })
            })
                .then(response => response.json())
                .then(data => {
                    const resultMessage = document.getElementById('resultMessage');
                    if (data.success) {
                        resultMessage.textContent = data.message;
                        loadFriends();
                    } else {
                        resultMessage.textContent = data.message || 'An error occurred';
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        
        let firstload = 1;  // used for laoding messages for the first time for a contact
        let lastMessageTimestamp = "";  // stores the timestamp of last message
        
        
        // ################## Function to fetch friends and display them in the contact list ###############################
		
        function loadFriends() {
            fetch('php/fetch_friends_api.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const contacts = data.friends; // Array of friends
                        contactList.innerHTML = ''; // Clear existing contacts
                        contacts.reverse().forEach(contact => {
                            const div = document.createElement('div');
                            div.className = 'contact-item';
                            div.textContent = contact.name;
                            div.onclick = () => {
                                
                                // Remove the background color from all contacts
                                const allContactDivs = document.querySelectorAll('.contact-item');
                                allContactDivs.forEach(item => {
                                    item.style.backgroundColor = ''; // Reset background color
                                });
                    
                                // Set background color for the selected contact
                                div.style.backgroundColor = '#cce8b6';
                                
                                firstload = 1;
                                lastMessageTimestamp = "";  // stores the timestamp of last message
                                
                                // Load messages for the selected contact
                                loadMessages(contact.id); // Pass friend ID
                            };
                            contactList.appendChild(div);
                        });
                    } else {
                        console.error('Failed to load friends:', data.message);
                    }
                })
                .catch(error => console.error('Error fetching friends:', error));
        }
		
		// onload function
        window.onload = function() {
            // Call the function to load friends on page load
            loadFriends();
        }
    
	
	// ############################### Logic for Messages #######################################
    
        let currentContactId = null; // Track the selected contact

        // Function to load messages for a contact
        function loadMessages(contactId) {
            
            currentContactId = contactId;
            fetch(`php/fetch_messages_api.php?contact_id=${contactId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        // if loading a contact for the first time, load all messages
                        if (firstload){
                            messageList.innerHTML = '';
                            data.messages.forEach(msg => {
                                const p = document.createElement('p');
                                p.className = `message ${msg.type}`;
                                p.textContent = msg.text;
                                messageList.appendChild(p);
                                
                                lastMessageTimestamp = msg.timestamp;
                                
                                // scroll to bottom of message list
                                scrollToBottom();
                                
                            });
                            
                            // console.log("Last Message Time: ");
                            // console.log(lastMessageTimestamp);
                            
                            firstload = 0;
                        }
                        else {  // only add newer messaegs if there are any
                            data.messages.forEach(msg => {
                                // console.log("Last Message: ");
                                // console.log(lastMessageTimestamp);
                                
                                if (msg.timestamp > lastMessageTimestamp){
                                    
                                    // console.log("New Message: ")
                                    // console.log(msg.timestamp);
                                    
                                    const p = document.createElement('p');
                                    p.className = `message ${msg.type}`;
                                    p.textContent = msg.text;
                                    messageList.appendChild(p);
                                    
                                    // stores the timestamp of latest message
                                    lastMessageTimestamp = msg.timestamp;
                                    
                                    // scroll to bottom of message list
                                    scrollToBottom();
                                }
                            });
                        }
                         
                        
                    } else {
                        console.error('Failed to load messages:', data.message);
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }
        
        function scrollToBottom() {
          const messageList = document.getElementById('messageList');
          messageList.scrollTop = messageList.scrollHeight;
        }
        
        // ####################################### Handle sending messages ###################################
		
        messageForm.addEventListener('submit', event => {
            event.preventDefault();
            const text = messageInput.value.trim();
            if (text && currentContactId) {
                fetch('php/send_message_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ receiver_id: currentContactId, message: text }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            
                            loadMessages(currentContactId); // Reload messages
                            // refresh(currentContactId);
                            
                            messageInput.value = ''; // Clear input
                        } else {
                            console.error('Failed to send message:', data.message);
                        }
                    })
                    .catch(error => console.error('Error sending message:', error));
            }
        });
        
        // Periodically fetch new messages
        setInterval(() => {
            if (currentContactId) {
                loadMessages(currentContactId);
            }
        }, 1000); // Every 1 seconds
        
    </script>

</body>
</html>
