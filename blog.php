<?php
include 'connectdb.php'; // Include the database connection

$error = ""; // Initialize error variable
$success = ""; // Initialize success variable

// Handle form submission to create a new blog post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = "Anonymous"; // Default author for unauthenticated users

    if (!empty($title) && !empty($content)) {
        $query = "INSERT INTO blogs_page (user_id, title, content, created_at) VALUES (NULL, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Statement preparation failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $success = "Blog post created successfully!";
        } else {
            $error = "Error creating blog post. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Title and content cannot be empty.";
    }
}

// Fetch all blog posts
$query = "SELECT blogs_page.id, blogs_page.title, blogs_page.content, users.username AS author
          FROM blogs-page
          LEFT JOIN users ON blogs.user_id = users.id
          ORDER BY blogs.created_at DESC";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching blog posts: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #007bff;
            padding: 10px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
        }

        .navbar a:hover {
            background-color: #0056b3;
            border-radius: 5px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .blog-form label, .blog-posts h2 {
            font-weight: bold;
        }

        .blog-form input, .blog-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .blog-form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .blog-form button:hover {
            background-color: #0056b3;
        }

        .blog-post {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="account.php">User Profile</a>
        <a href="blog.php">Your Blog</a>
    </div>

    <div class="container">
        <h1>Your Blog</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Form to create a new blog post -->
        <div class="blog-form">
            <h2>Create a New Blog Post</h2>
            <form method="POST" action="blog.php">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="5" required></textarea>

                <button type="submit">Post</button>
            </form>
        </div>

        <!-- Display blog posts -->
        <div class="blog-posts">
            <h2>All Blog Posts</h2>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="blog-post">
                    <h3><?= htmlspecialchars($row['title']); ?></h3>
                    <p><small>By <?= htmlspecialchars($row['author'] ?? "Anonymous"); ?> on <?= htmlspecialchars($row['created_at']); ?></small></p>
                    <p><?= nl2br(htmlspecialchars($row['content'])); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
