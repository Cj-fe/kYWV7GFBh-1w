
<script>
    var settingsmenu = document.querySelector(".settings-menu");
    var darkBtn = document.getElementById("dark-btn");

    function settingsMenuToggle() {
        settingsmenu.classList.toggle("settings-menu-height");
    }
    darkBtn.onclick = function () {
        darkBtn.classList.toggle("dark-btn-on");
        document.body.classList.toggle("dark-theme");

        if (localStorage.getItem("theme") == "light") {
            localStorage.setItem("theme", "dark");
        } else {
            localStorage.setItem("theme", "light");
        }


    }

    if (localStorage.getItem("theme") == "light") {
        darkBtn.classList.remove("dark-btn-on");
        document.body.classList.remove("dark-theme");
    } else if (localStorage.getItem("theme") == "dark") {
        darkBtn.classList.add("dark-btn-on");
        document.body.classList.add("dark-theme");
    } else {
        localStorage.setItem("theme", "dark");
    }

</script>


<script>
    // Use PHP to inject alumni info into JavaScript
    const alumniInfo = <?php echo $alumni_info_json; ?>;

    const input = document.getElementById("myInput");
    const autocompleteList = document.getElementById("autocomplete-list");

    input.addEventListener("input", function () {
        const value = this.value.toLowerCase();
        autocompleteList.innerHTML = "";

        if (!value) return;

        const matchingAlumni = alumniInfo.filter(alumni =>
            alumni.name.toLowerCase().includes(value)
        ).slice(0, 5); // Limit to 5 results

        matchingAlumni.forEach(alumni => {
            const div = document.createElement("div");
            div.className = "autocomplete-item";
            div.innerHTML = `
            <img src="${alumni.profile_url}" alt="${alumni.name}" >
            <div class="autocomplete-item-info">
                <span class="autocomplete-item-name">${alumni.name}</span>
                <span class="autocomplete-item-details">Alumni</span>
            </div>
        `;
            div.addEventListener("click", function () {
                input.value = alumni.name;
                autocompleteList.innerHTML = "";
                // Redirect to the alumni's profile
                window.location.href = `view_alumni_details.php?id=${alumni.id}`;
            });
            autocompleteList.appendChild(div);
        });
    });

    document.addEventListener("click", function (e) {
        if (e.target !== input) {
            autocompleteList.innerHTML = "";
        }
    });

    // Add event listener for Enter key press
    input.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const searchQuery = input.value.trim();
            if (searchQuery) {
                window.location.href = `search_results.php?query=${encodeURIComponent(searchQuery)}`;
            }
        }
    });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var notificationIcon = document.querySelector(".notification-icon");
    var notificationMenu = document.querySelector(".notification-menu");
    var notificationCount = document.querySelector(".notification-count");

    function resetNotificationCount() {
        if (notificationCount) {
            notificationCount.textContent = '0';
            notificationCount.style.display = 'none';
            
            // Update last_notification_check on the server
            fetch('update_notification_check.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({user_id: '<?php echo $current_user_id; ?>'}),
            });
        }
    }

    if (notificationIcon && notificationMenu) {
        notificationIcon.addEventListener("click", function (event) {
            event.stopPropagation();
            notificationMenu.classList.toggle("notification-menu-height");
            resetNotificationCount();
        });

        // Close the notification menu when clicking outside
        document.addEventListener("click", function (event) {
            if (!event.target.closest('.notification-icon') && !event.target.closest('.notification-menu')) {
                notificationMenu.classList.remove("notification-menu-height");
            }
        });
    }

    // Initial check for notifications
    checkNewNotifications();
    updateNotificationMenu();  // Fetch notifications on page load

    // Set interval to check for new notifications and refresh the menu every 5 seconds
    setInterval(function() {
        checkNewNotifications();
        updateNotificationMenu();
    }, 5000);
});

function updateNotificationCount(count) {
    const countElement = document.querySelector('.notification-count');
    if (countElement) {
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// Function to check for new notifications and update the count
function checkNewNotifications() {
    fetch('check_new_notifications.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({user_id: '<?php echo $current_user_id; ?>'}),
    })
    .then(response => response.json())
    .then(data => {
        console.log('New notifications count:', data.new_count); // Debug log
        updateNotificationCount(data.new_count);
    })
    .catch(error => console.error('Error:', error));
}

// Function to refresh the notification menu content
function updateNotificationMenu() {
    fetch('get_notifications.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({user_id: '<?php echo $current_user_id; ?>'}),
    })
    .then(response => response.text())
    .then(data => {
        document.querySelector('.notification-menu-inner').innerHTML = data;
    })
    .catch(error => console.error('Error:', error));
}

</script>



<script>
    function messageMenuToggle(event) {
        var menu = document.getElementById('messageMenu');
        menu.classList.toggle('message-menu-height');

        // Send AJAX request to update message_active status
        if (menu.classList.contains('message-menu-height')) {
            updateMessageStatus(1); // Mark as read
        }

        // Stop the event from bubbling up to the document click event
        event.stopPropagation();
    }

    // Close the chatbox if clicking outside of it
    document.addEventListener('click', function (event) {
        var menu = document.getElementById('messageMenu');
        var icon = document.querySelector('.message-icon');

        if (menu.classList.contains('message-menu-height') &&
            !menu.contains(event.target) &&
            !icon.contains(event.target)) {
            menu.classList.remove('message-menu-height');
        }
    });
</script>


<script>
    // Function to fetch and update message count
    function updateMessageCount() {
        fetch('get_message_count.php')
            .then(response => response.json())
            .then(data => {
                const messageCountElement = document.querySelector('.message-count');
                if (data.message_count > 0) {
                    messageCountElement.textContent = data.message_count;
                } else {
                    messageCountElement.textContent = '';
                }
            })
            .catch(error => console.error('Error fetching message count:', error));
    }

    // Refresh the message count every 5 seconds
    setInterval(updateMessageCount, 5000);

    // Initial load
    updateMessageCount();
</script>


<script>
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', () => {
            const url = item.getAttribute('data-href');
            if (url) {
                window.location.href = url;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var globalChatbox = document.getElementById('globalchatbox');
        var chatboxBody = document.querySelector('.chatbox-body');
        var chatboxInput = document.getElementById('chatbox-input');
        var sendMessageIcon = document.getElementById('send-message');
        var messageItemsContainer = document.getElementById('messageItemsContainer');
        var currentOpenChatUserId = null; // Track the currently open chat user ID
        let allMessages = []; // Store all messages for chatbox
        let latestMessages = []; // Store only the latest messages for the message menu

        function fetchMessages() {
            fetch('fetch_messages.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    // Store messages separately
                    allMessages = data.messages; // All conversation messages for the chatbox
                    latestMessages = data.latest_messages; // Only the latest message from each sender for the message menu

                    // Update the message menu
                    updateMessageMenu(latestMessages);

                    // Update the chatbox if a chat is open
                    if (currentOpenChatUserId !== null) {
                        refreshOpenChat(); // Refresh chatbox for the currently open chat
                    }
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        function updateMessageMenu(latestMessages) {
            messageItemsContainer.innerHTML = ''; // Clear existing messages in the menu

            if (latestMessages.length === 0) {
                messageItemsContainer.innerHTML = '<div class="no-messages">No new messages.</div>';
            } else {
                latestMessages.forEach(message => {
                    const messageItem = document.createElement('div');
                    messageItem.className = 'message-item';
                    messageItem.setAttribute('data-message-id', message.messageId); // Store message ID in a data attribute

                    messageItem.innerHTML = `
                <img src="${message.profilePic}" alt="${message.name}">
                <div class="message-info">
                    <p><strong>${message.name}:</strong> ${message.content}</p>
                    <span class="message-time">${message.timeAgo}</span>
                </div>
            `;

                    messageItem.addEventListener('click', function () {
                        openChat(message.userId, message.name);

                        // Send request to mark message as read
                        markMessageAsRead(message.messageId);
                    });

                    messageItemsContainer.appendChild(messageItem);
                });
            }
        }

        function markMessageAsRead(messageId) {
            fetch('mark_message_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `message_id=${messageId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Message marked as read');
                    } else {
                        console.error('Failed to mark message as read:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error marking message as read:', error);
                });
        }


        function openChat(userId, userName) {
            console.log("Opening chat for user:", userId, userName);
            const currentUserId = "<?php echo $_SESSION['user']['id']; ?>";

            // Track the currently open chat user ID
            currentOpenChatUserId = userId;

            // Update chatbox header with user's name
            document.querySelector('.chatbox-name').textContent = userName;

            // Clear previous chat content
            chatboxBody.innerHTML = '';

            // Populate chatbox with messages for this conversation
            refreshOpenChat();

            // Show the chatbox with a sliding effect
            globalChatbox.style.bottom = '0px';

            // Set a custom attribute for the active chat receiver ID
            globalChatbox.setAttribute('data-receiver-id', userId);
        }

        function refreshOpenChat() {
            const currentUserId = "<?php echo $_SESSION['user']['id']; ?>";

            if (currentOpenChatUserId === null) return; // No chat is open

            // Filter messages for the selected conversation
            const filteredMessages = allMessages.filter(message =>
                (message.senderId === currentUserId && message.receiverId === currentOpenChatUserId) ||
                (message.senderId === currentOpenChatUserId && message.receiverId === currentUserId)
            );

            // Clear chatbox body
            chatboxBody.innerHTML = '';

            // Populate chatbox with filtered messages
            filteredMessages.forEach(function (message) {
                var messageDiv = document.createElement('div');
                messageDiv.className = message.senderId === currentUserId ? 'message outgoing' : 'message incoming';
                messageDiv.innerHTML = `
            <p>${message.content}</p>
            <span class="timestamp">${message.timeAgo}</span>
        `;
                chatboxBody.appendChild(messageDiv);
            });

            // Scroll to the bottom of the chatbox to show the latest message
            chatboxBody.scrollTop = chatboxBody.scrollHeight;
        }

        // Fetch messages immediately when the page loads
        fetchMessages();

        // Then fetch messages every 5 seconds
        setInterval(fetchMessages, 5000);

        // Close chatbox functionality
        document.querySelector('.close-chat').addEventListener('click', function () {
            globalChatbox.style.bottom = '-500px'; // Hide the chatbox with a sliding effect
            currentOpenChatUserId = null; // Reset the current open chat user ID
        });

        // Send message functionality
        sendMessageIcon.addEventListener('click', sendMessage);

        chatboxInput.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            var messageContent = chatboxInput.value.trim();
            if (messageContent === '') {
                return; // Do not send empty messages
            }

            var receiverId = globalChatbox.getAttribute('data-receiver-id');
            var currentUserId = "<?php echo $_SESSION['user']['id']; ?>";
            var timestamp = new Date().toISOString();

            // Create message data
            var messageData = {
                senderId: currentUserId,
                receiverId: receiverId,
                content: messageContent,
                timestamp: timestamp
            };

            // Send message using AJAX
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(messageData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Append the message to the chatbox
                        var messageDiv = document.createElement('div');
                        messageDiv.className = 'message outgoing';
                        messageDiv.innerHTML = `
                    <p>${messageContent}</p>
                    <span class="timestamp">${formatTimestamp(new Date(timestamp))}</span>
                `;
                        chatboxBody.appendChild(messageDiv);

                        // Clear the input field
                        chatboxInput.value = '';
                        chatboxBody.scrollTop = chatboxBody.scrollHeight; // Scroll to the bottom

                        // Fetch messages to update the list
                        fetchMessages();
                    } else {
                        alert('Failed to send message: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('An error occurred while sending the message.');
                });
        }

        // Format timestamp to more readable format
        function formatTimestamp(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }
    });
</script>