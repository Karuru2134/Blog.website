<?php
session_start();
include 'connectdb.php'; // Include database connection
include 'header.php';

// Example: Fetch the logged-in user's profile using session user_id
$user_id = $_SESSION['user_id'] ?? 1; // Replace '1' with a test user ID if no session is set for testing

// Initialize variables
$success = '';
$error = '';

// Handle form submission for updating the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_bio = trim($_POST['bio']);
    $profile_image = null;

    // Directory to store images
    $upload_dir = __DIR__ . '/uploads/profile_images/';

    // Ensure the directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory with appropriate permissions
    }

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Validate file type
        if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
            $error = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($_FILES['profile_image']['size'] > $max_size) {
            $error = "Image size exceeds the maximum limit of 2MB.";
        } elseif ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error code: " . $_FILES['profile_image']['error'];
        } else {
            // Generate unique file name
            $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $profile_image = $upload_dir . uniqid('profile_') . '.' . $file_ext;

            // Move file to upload directory
            if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image)) {
                $error = "Failed to upload the image. Please check directory permissions.";
            } else {
                // Convert to relative path for database storage
                $profile_image = 'uploads/profile_images/' . basename($profile_image);
            }
        }
    }

    if (empty($error) && !empty($new_username) && !empty($new_email)) {
        $update_query = "UPDATE users SET username = ?, email = ?, bio = ?, image = COALESCE(?, image) WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            $error = "Statement preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssssi", $new_username, $new_email, $new_bio, $profile_image, $user_id);
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
            } else {
                $error = "Error updating profile. Please try again.";
            }
            $stmt->close();
        }
    } else if (empty($new_username) || empty($new_email)) {
        $error = "Username and email cannot be empty.";
    }
}

// Fetch updated user details
$query = "SELECT username, email, bio, image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Statement preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_image);
if (!$stmt->fetch()) {
    die("No user found for the given ID.");
}
$stmt->close();

// Fetch blogs authored by the specific user
$sql = "
    SELECT b.*
    FROM blogs_page b
    JOIN users u ON b.author = u.username
    WHERE u.id = ?
    ORDER BY b.date DESC
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Statement preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        /* General Reset */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        /* Navigation bar styling */
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

        /* Profile Card */
        .profile-card {
            max-width: 1200px;
            margin: 40px auto;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-section {
            text-align: center;
            padding: 20px;
            position: relative;
        }

        .profile-picture img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-details h1 {
            margin: 40px 0 10px;
            font-size: 24px;
            color: #007bff;
        }

        .profile-details p {
            margin: 5px 0;
            color: #666;
        }

        /* Profile update form */
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .profile-container input, .profile-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .profile-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .profile-container button:hover {
            background-color: #0056b3;
        }

        .blog-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 50px;
        }

        .blog-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
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
    </style>
</head>
<body>
    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-section">
            <div class="profile-picture">
                <img src="<?= !empty($profile_image) ? htmlspecialchars($profile_image) : 'default-profile.png'; ?>" alt="Profile Picture">
            </div>
            <div class="profile-details">
                <h1><?= htmlspecialchars($username); ?></h1>
                <p>Email: <?= htmlspecialchars($email); ?></p>
                <p>Bio: <?= htmlspecialchars($bio); ?></p>
            </div>
        </div>
    </div>

    <!-- Blog Cards Section -->
    <div class="blog-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="blog-card">
                    <img src="' . htmlspecialchars($row['image']) . '" alt="Blog Image">
                    <h3>' . htmlspecialchars($row['title']) . '</h3>
                    <p>By ' . htmlspecialchars($row['author']) . ' on ' . htmlspecialchars($row['date']) . '</p>
                </div>';
            }
        } else {
            echo '<p>No blog posts found.</p>';
        }
        $conn->close();
        ?>
    </div>

    <!-- Profile Update Form -->
    <div class="profile-container">
        <h1>Update Profile</h1>
        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>

            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($bio); ?></textarea>

            <label for="profile_image">Profile Image:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
