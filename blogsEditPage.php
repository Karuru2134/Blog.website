<?php
// Include necessary files
include 'headerAdmins.php';
include 'connectdb.php';

// Start the session to check admin role
session_start();

// Check if the admin is logged in
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Handle delete action
if (isset($_GET['delete_id']) && $is_admin) {
    $delete_id = intval($_GET['delete_id']); // Sanitize the delete ID

    // Fetch the blog data to move it to the `deleted` table
    $fetch_sql = "SELECT * FROM blogs_page WHERE id = ?";
    $stmt_fetch = $conn->prepare($fetch_sql);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $delete_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();

        if ($result->num_rows > 0) {
            $blog = $result->fetch_assoc();

            // Insert the blog data into the `deleted` table
            $insert_sql = "INSERT INTO deleted (title, image, author, date, likes, comments, views)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            if ($stmt_insert) {
                $stmt_insert->bind_param(
                    "ssssiii",
                    $blog['title'],
                    $blog['image'],
                    $blog['author'],
                    $blog['date'],
                    $blog['likes'],
                    $blog['comments'],
                    $blog['views']
                );

                if ($stmt_insert->execute()) {
                    // Delete the blog from the `blogs_page` table
                    $delete_sql = "DELETE FROM blogs_page WHERE id = ?";
                    $stmt_delete = $conn->prepare($delete_sql);
                    if ($stmt_delete) {
                        $stmt_delete->bind_param("i", $delete_id);
                        if ($stmt_delete->execute()) {
                            echo "<script>alert('Blog post moved to deleted table successfully!'); window.location='blogsEditPage.php';</script>";
                        } else {
                            echo "<script>alert('Error deleting blog post: " . $stmt_delete->error . "');</script>";
                        }
                        $stmt_delete->close();
                    }
                } else {
                    echo "<script>alert('Error moving blog to deleted table: " . $stmt_insert->error . "');</script>";
                }
                $stmt_insert->close();
            }
        } else {
            echo "<script>alert('Blog post not found.');</script>";
        }
        $stmt_fetch->close();
    } else {
        echo "<script>alert('Error fetching blog data: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bona Blog - Admin Panel</title>
    <style>
        /* General Styles */
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
            position: relative; /* For delete button */
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

        .blog-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
        }

        /* Delete button */
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .delete-btn:hover {
            background-color: #ff1a1a;
        }
    </style>
</head>
<body>
    <!-- Blog Cards Section -->
    <div class="blog-container">
        <?php
        // Fetch all blog posts from the database
        $sql = "SELECT * FROM blogs_page ORDER BY date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="blog-card">
                    <!-- Delete Button (only visible for admins) -->
                    ' . ($is_admin ? '
                    <a href="blogsEditPage.php?delete_id=' . htmlspecialchars($row['id']) . '"
                        class="delete-btn"
                        onclick="return confirm(\'Are you sure you want to delete this blog?\')">X</a>' : '') . '

                    <img src="' . htmlspecialchars($row['image']) . '" alt="Blog Image">
                    <div class="blog-card-content">
                        <h3>' . htmlspecialchars($row['title']) . '</h3>
                        <p>By ' . htmlspecialchars($row['author']) . ' on ' . htmlspecialchars($row['date']) . '</p>
                    </div>
                    <div class="blog-card-footer">
                        <div class="author">
                            <span>' . htmlspecialchars($row['author']) . '</span>
                        </div>
                        <div class="stats">
                            <span>❤️ ' . htmlspecialchars($row['likes']) . '</span>
                            <span>💬 ' . htmlspecialchars($row['comments']) . '</span>
                            <span>👁️ ' . htmlspecialchars($row['views']) . '</span>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No blog posts found.</p>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
