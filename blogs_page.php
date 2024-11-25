<?php
session_start();
include 'connectdb.php'; // Include database connection
include 'header.php';

// Handle the form submission for new blog posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_blog'])) {
    // Sanitize input
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $author = $_SESSION['username'] ?? 'Anonymous'; // Fallback if not logged in

    if ($title && $content) {
        // Prepare SQL query
        $sql = "INSERT INTO blogs_page (title, content, author) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $title, $content, $author);
            if ($stmt->execute()) {
                echo "<script>alert('Blog post submitted successfully!'); window.location='blogs_page.php';</script>";
            } else {
                echo "<script>alert('Error submitting blog: " . htmlspecialchars($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Database error. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}

// Fetch blog posts
$sql = "SELECT * FROM blogs_page ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bona Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 20px 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .header nav {
            display: flex;
            gap: 20px;
        }

        .header nav a {
            text-decoration: none;
            font-size: 16px;
            color: #333;
        }

        .hero {
            background: url('featured-image.jpg') no-repeat center center/cover;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .hero h1 {
            font-size: 36px;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            text-align: center;
            font-size: 32px;
            color: #333;
            margin-bottom: 30px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 300px;
        }

        button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .blog-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 50px;
        }

        .blog-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }

        .blog-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .blog-card-content {
            padding: 20px;
        }

        .blog-card-content h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .blog-card-content p {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .blog-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
        }

        .blog-card-footer .author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .blog-card-footer .author img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .blog-card-footer .stats {
            display: flex;
            gap: 10px;
        }

        .blog-card-footer .stats span {
            font-size: 14px;
            color: #555;
        }

        .no-blogs {
            text-align: center;
            color: #555;
            font-size: 18px;
            padding: 50px 0;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to Bona Blog</h1>
    </div>
    <div class="blog-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="blogs_text.php?id=<?= htmlspecialchars($row['id']) ?>" class="blog-card">
                    <img src="<?= htmlspecialchars($row['image'] ?? 'default-image.jpg') ?>" alt="Blog Image">
                    <div class="blog-card-content">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p>By <?= htmlspecialchars($row['author'] ?? 'Anonymous') ?> on <?= htmlspecialchars($row['date']) ?></p>
                    </div>
                    <div class="blog-card-footer">
                        <div class="author"><?= htmlspecialchars($row['author']) ?></div>
                        <div class="stats">
                            <span>❤️ <?= htmlspecialchars($row['likes']) ?></span>
                            <span>💬 <?= htmlspecialchars($row['comments']) ?></span>
                            <span>👁️ <?= htmlspecialchars($row['views']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-blogs">No blog posts found.</div>
        <?php endif; ?>
    </div>

    <h1 class="form-title">Share Your Story</h1>

    <div class="container">
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Blog Title" required>
            <textarea name="content" placeholder="Blog Content" required></textarea>
            <button type="submit" name="submit_blog">Submit Blog</button>
        </form>
    </div>

</body>
</html>

<?php $conn->close(); ?>
