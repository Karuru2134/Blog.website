<?php
session_start();
include 'connectdb.php'; // Include database connection

// Get the blog ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if the ID is valid
if ($id == 0) {
    die("Invalid blog ID.");
}

// Fetch the specific blog post's content
$sql = "SELECT content, title FROM blogs_page WHERE id = ?";
$stmt = $conn->prepare($sql);

// Check if the preparation of the statement was successful
if ($stmt === false) {
    die("Statement preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
} else {
    die("Blog post not found.");
}

// Fetch the comments for the specific blog post
$comment_sql = "SELECT comment_text, date_posted FROM comments WHERE blog_id = ? ORDER BY date_posted DESC";
$comment_stmt = $conn->prepare($comment_sql);

// Check if the preparation of the statement was successful
if ($comment_stmt === false) {
    die("Comment statement preparation failed: " . $conn->error);
}

$comment_stmt->bind_param("i", $id);
$comment_stmt->execute();
$comment_result = $comment_stmt->get_result();

$comments = [];
if ($comment_result->num_rows > 0) {
    while ($row = $comment_result->fetch_assoc()) {
        $comments[] = $row;
    }
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Sanitize and get the comment text from the form
    $comment_text = htmlspecialchars($_POST['comment_text']);

    if ($comment_text) {
        $insert_comment_sql = "INSERT INTO comments (blog_id, comment_text) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_comment_sql);

        // Check if the preparation of the statement was successful
        if ($insert_stmt === false) {
            die("Insert comment statement preparation failed: " . $conn->error);
        }

        $insert_stmt->bind_param("is", $id, $comment_text);
        if ($insert_stmt->execute()) {
            echo "<script>alert('Comment posted successfully!'); window.location.reload();</script>";
        } else {
            echo "<script>alert('Error posting comment: " . $insert_stmt->error . "');</script>";
        }
        $insert_stmt->close();
    } else {
        echo "<script>alert('Please fill out the comment field.');</script>";
    }
}

$stmt->close();
$comment_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-link:hover {
            background: #0056b3;
        }

        .comments-section {
            margin-top: 40px;
        }

        .comment {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .comment .author {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .comment .date {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .comment .text {
            font-size: 14px;
            color: #444;
        }

        .comment-form {
            margin-top: 30px;
            padding: 20px;
            background: #f3f3f3;
            border-radius: 5px;
        }

        .comment-form input,
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .comment-form button {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .comment-form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <p><?= nl2br(htmlspecialchars($blog['content'])) ?></p>
        <a href="blogs_page.php" class="back-link">Back to Blogs</a>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2>Comments</h2>

            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="date"><?= htmlspecialchars($comment['date_posted']) ?></div>
                        <div class="text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>

        <!-- New Comment Form -->
        <div class="comment-form">
            <h3>Leave a Comment</h3>
            <form method="POST" action="">
                <textarea name="comment_text" rows="4" placeholder="Your Comment" required></textarea>
                <button type="submit" name="comment">Submit Comment</button>
            </form>
        </div>
    </div>
</body>
</html>
