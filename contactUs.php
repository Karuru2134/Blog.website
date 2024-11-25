<?php
include 'header.php';
include 'connectdb.php'; // Include database connection file
?>

<div class="about-container">
    <div class="about-hero">
        <h1>About Our Blog</h1>
        <p>
            Welcome to <strong>Your Niche Blog</strong>, where we bring you the most insightful, well-researched, and engaging content about [Your Niche]. Whether you're a beginner, enthusiast, or expert, our blog is designed to inspire and inform you.
        </p>
    </div>

    <div class="about-sections">
        <div class="about-section">
            <h2>Our Mission</h2>
            <p>
                At <strong>Your Niche Blog</strong>, our mission is to empower our readers with valuable knowledge, actionable tips, and authentic stories. We aim to be your go-to resource for [Your Niche], helping you explore, learn, and grow in your journey.
            </p>
        </div>

        <div class="about-section">
            <h2>What Makes Us Unique</h2>
            <p>
                What sets us apart is our commitment to authenticity and in-depth research. Our content is created by passionate experts and enthusiasts who genuinely care about the [Your Niche] community. We ensure that every post delivers value while keeping you engaged and entertained.
            </p>
        </div>

        <div class="about-section">
            <h2>Meet the Team</h2>
            <p>
                We are a small but passionate team of writers, editors, and creators who share a love for [Your Niche]. Together, we work tirelessly to bring you content that inspires, educates, and connects you to a community of like-minded individuals.
            </p>
        </div>
    </div>

    <!-- Contact Form Section -->
    <div class="about-cta">
        <h2>Join Our Community</h2>
        <p>
            Ready to dive into the world of [Your Niche]? Subscribe to our blog, follow us on social media, or drop us a message. We’d love to hear from you!
        </p>

        <!-- Feedback Messages -->
        <p class="error" style="color: red; display: none;"></p>
        <p class="success" style="color: green; display: none;"></p>

        <!-- Contact Form -->
        <form id="contactForm" method="POST" action="">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connectdb.php'; // Include the database connection

    // Retrieve and sanitize form inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } else {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Thank you for reaching out! We will get back to you soon.');</script>";
        } else {
            echo "<script>alert('Error submitting your message. Please try again later.');</script>";
        }
        $stmt->close();
    }

    $conn->close(); // Close the database connection
    exit; // Stop further script execution
}
?>
