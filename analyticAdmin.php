<?php
session_start();
include 'connectdb.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch total number of blog posts
$sqlTotalBlogs = "SELECT COUNT(*) as total_blogs FROM blogs_page";
$resultTotalBlogs = $conn->query($sqlTotalBlogs);
$totalBlogs = ($resultTotalBlogs->num_rows > 0) ? (int) $resultTotalBlogs->fetch_assoc()['total_blogs'] : 0;

// Fetch total number of likes
$sqlTotalLikes = "SELECT SUM(likes) as total_likes FROM blogs_page";
$resultTotalLikes = $conn->query($sqlTotalLikes);
$totalLikes = ($resultTotalLikes->num_rows > 0) ? (int) $resultTotalLikes->fetch_assoc()['total_likes'] : 0;

// Fetch total number of views
$sqlTotalViews = "SELECT SUM(views) as total_views FROM blogs_page";
$resultTotalViews = $conn->query($sqlTotalViews);
$totalViews = ($resultTotalViews->num_rows > 0) ? (int) $resultTotalViews->fetch_assoc()['total_views'] : 0;

// Fetch total number of comments
$sqlTotalComments = "SELECT SUM(comments) as total_comments FROM blogs_page";
$resultTotalComments = $conn->query($sqlTotalComments);
$totalComments = ($resultTotalComments->num_rows > 0) ? (int) $resultTotalComments->fetch_assoc()['total_comments'] : 0;

// Fetch blog activity by date for chart
$sqlBlogActivity = "
    SELECT DATE(date) as blog_date, COUNT(*) as total_blogs
    FROM blogs_page
    GROUP BY DATE(date)
    ORDER BY blog_date ASC";
$resultBlogActivity = $conn->query($sqlBlogActivity);

$blogDates = [];
$blogCounts = [];
if ($resultBlogActivity->num_rows > 0) {
    while ($row = $resultBlogActivity->fetch_assoc()) {
        $blogDates[] = $row['blog_date'];
        $blogCounts[] = (int)$row['total_blogs'];
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .header {
            padding: 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            display: inline-block;
            width: 23%;
            margin: 1%;
            padding: 20px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            font-size: 24px;
            color: #007bff;
        }

        .chart-container {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Blog Analytics Dashboard</h1>
    </div>
    <div class="container">
        <!-- Metrics Cards -->
        <div class="card">
            <h3>Total Blogs</h3>
            <p><?= $totalBlogs ?></p>
        </div>
        <div class="card">
            <h3>Total Likes</h3>
            <p><?= $totalLikes ?></p>
        </div>
        <div class="card">
            <h3>Total Views</h3>
            <p><?= $totalViews ?></p>
        </div>
        <div class="card">
            <h3>Total Comments</h3>
            <p><?= $totalComments ?></p>
        </div>

        <!-- Chart for Blog Activity -->
        <div class="chart-container">
            <canvas id="blogActivityChart" width="800" height="400"></canvas>
        </div>
    </div>

    <script>
        // Data for the Blog Activity chart
        const blogDates = <?= json_encode($blogDates) ?>;
        const blogCounts = <?= json_encode($blogCounts) ?>;

        // Initialize the chart
        const ctx = document.getElementById('blogActivityChart').getContext('2d');
        const blogActivityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: blogDates, // Dates on the x-axis
                datasets: [{
                    label: 'Blogs Published per Day',
                    data: blogCounts, // Blog counts on the y-axis
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Blog Activity Over Time'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Blogs'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
