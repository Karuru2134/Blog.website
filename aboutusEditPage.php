<?php
// Include reusable components
include 'headerAdmins.php';

// Variables for easy editing
$blogName = "Your Niche Blog";
$niche = "[Your Niche]";

// Editable content
$aboutHero = "Welcome to <strong>$blogName</strong>, where we bring you the most insightful, well-researched, and engaging content about $niche. Whether you're a beginner, enthusiast, or expert, our blog is designed to inspire and inform you.";

$mission = "At <strong>$blogName</strong>, our mission is to empower our readers with valuable knowledge, actionable tips, and authentic stories. We aim to be your go-to resource for $niche, helping you explore, learn, and grow in your journey.";

$unique = "What sets us apart is our commitment to authenticity and in-depth research. Our content is created by passionate experts and enthusiasts who genuinely care about the $niche community. We ensure that every post delivers value while keeping you engaged and entertained.";

$team = "We are a small but passionate team of writers, editors, and creators who share a love for $niche. Together, we work tirelessly to bring you content that inspires, educates, and connects you to a community of like-minded individuals.";
?>
<div class="about-container">
    <div class="about-hero">
        <h1>About Our Blog</h1>
        <p><?php echo $aboutHero; ?></p>
    </div>

    <div class="about-sections">
        <div class="about-section">
            <h2>Our Mission</h2>
            <p><?php echo $mission; ?></p>
        </div>

        <div class="about-section">
            <h2>What Makes Us Unique</h2>
            <p><?php echo $unique; ?></p>
        </div>

        <div class="about-section">
            <h2>Meet the Team</h2>
            <p><?php echo $team; ?></p>
        </div>
    </div>

<?php include 'footer.php'; ?>
