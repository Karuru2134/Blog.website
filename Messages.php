<?php
include "connectdb.php"; // Include your database connection file
include 'headerAdmins.php'; // Include the admin header

// Start session
session_start();

// Fetch messages from the database
$sql = "SELECT id, name, email, message FROM messages ORDER BY id DESC"; // Get all messages
$result = $conn->query($sql); // Execute the query

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respond'])) {
    $message_id = $_POST['message_id'];
    $response = trim($_POST['response']);

    if (!empty($response)) {
        // Insert the response into the database
        $stmt = $conn->prepare("INSERT INTO responses (message_id, response) VALUES (?, ?)");
        $stmt->bind_param("is", $message_id, $response);
        if ($stmt->execute()) {
            echo "<script>alert('Response sent successfully.');</script>";
        } else {
            echo "<script>alert('Failed to send response.');</script>";
        }
    } else {
        echo "<script>alert('Response cannot be empty.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages</title>
    <style>
        /* Add styles as necessary */
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .messages-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .messages-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .message-box {
            background: #f9f9f9;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .message-box:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .message-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .message-header span {
            display: block;
            margin-bottom: 5px;
        }

        .message-header .name {
            color: #512da8;
            font-size: 18px;
        }

        .message-header .email {
            color: #888;
            font-size: 14px;
        }

        .message-content {
            color: #555;
            line-height: 1.6;
        }

        .no-messages {
            text-align: center;
            color: #888;
            font-size: 18px;
        }

        .response-box {
            background: #f0f0f0;
            padding: 10px;
            margin-top: 10px;
            border-left: 4px solid #512da8;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <h1>Customer Messages</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message-box">
                    <div class="message-header">
                        <span class="name"><?= htmlspecialchars($row['name']); ?></span>
                        <span class="email"><?= htmlspecialchars($row['email']); ?></span>
                    </div>
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($row['message'])); ?>
                    </div>

                    <!-- Response Form -->
                    <form method="POST" action="" class="response-form">
                        <input type="hidden" name="message_id" value="<?= $row['id']; ?>">
                        <textarea name="response" placeholder="Write your response here..." rows="4" required></textarea>
                        <button type="submit" name="respond">Send Response</button>
                    </form>

                    <!-- Display responses if any -->
                    <?php
                    $message_id = $row['id'];

                    $response_sql = "SELECT response, response_date FROM responses WHERE message_id = ? ORDER BY response_date ASC";
                    $response_stmt = $conn->prepare($response_sql);

                    if ($response_stmt) {
                        $response_stmt->bind_param("i", $message_id);
                        $response_stmt->execute();
                        $response_result = $response_stmt->get_result();

                        if ($response_result->num_rows > 0) {
                            while ($response_row = $response_result->fetch_assoc()) {
                                echo '<div class="response-box">';
                                echo 'Response: ' . nl2br(htmlspecialchars($response_row['response'])) . '<br>';
                                echo 'Date: ' . htmlspecialchars($response_row['response_date']) . '<br>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="response-box">No responses found for this message.</div>';
                        }
                    }
                    ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-messages">No messages available.</div>
        <?php endif; ?>

        <?php
        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>
</html>
