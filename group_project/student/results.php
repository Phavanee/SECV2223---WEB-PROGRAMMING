<?php
$required_role = 'student';
include_once '../session.php';

$conn = new mysqli("localhost", "root", "", "jobhunt");

$userId = $_GET['userId'] ?? null;
$title = $conn->real_escape_string($_GET['title'] ?? '');
$location = $conn->real_escape_string($_GET['location'] ?? '');

// Get applied job IDs for this user
$appliedJobIds = [];
if ($userId) {
    $appliedSql = "SELECT job_id FROM applications WHERE user_id = $userId";
    $appliedResult = $conn->query($appliedSql);
    while ($row = $appliedResult->fetch_assoc()) {
        $appliedJobIds[] = (int)$row['job_id'];
    }
}

// Search jobs
$sql = "SELECT * FROM jobs WHERE title LIKE '%$title%' AND location LIKE '%$location%'";
$result = $conn->query($sql);
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <nav class="nav-menu">
            <div class="logo-container">
                <a href="home.html"><img src="../images/logo.png" alt="StudeeWork Logo" class="logo"></a>
            </div>
            <ul>
                <li><a href="home.html">Find Jobs</a></li>
                <li><a href="applications.html">Applications</a></li>
            </ul>
        </nav>
        <div class="profile">
            <a href="profile.html"><img src="../images/student.png" alt="Profile" class="profile-icon"></a>
        </div>
    </header>

    <section class="search-section">
        <h1>Earn While You Learn<span class="highlight"></span></h1>
        <p>Find your next gig in your area</p>
        <div class="search-bar">
            <form method="get" action="results.php">
                <input type="text" placeholder="Job title or keyword" name="title" id="jobTitle">
                <input type="text" placeholder="Location (e.g., Florence, Italy)" name="location" id="location">
                <button type="submit">Search</button>
                <!-- <button onclick="searchJobs()">Search</button> -->
            </form>
        </div>
    </section>
    <div class="container">
        <section class="jobs-section">
            <h2>Job Results</h2>
            <div class="jobs-container" id="jobsContainer" style="display: flex; justify-content: space-between;">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <div class="job-info">
                        <h3><?= htmlspecialchars($job['title']) ?></h3>
                        <p><?= htmlspecialchars($job['location']) ?></p>
                        <div class="tags-container">
                            <?php foreach (explode(',', $job['tags']) as $tag): ?>
                                <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="apply-btn-container">
                        <?php if (in_array((int)$job['id'], $appliedJobIds)): ?>
                            <button class="applied" disabled>Applied</button>
                        <?php else: ?>
                            <button onclick="applyForJob(<?= $userId ?>, <?= $job['id'] ?>, this)">Apply Now</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/10/14/20250610145325-M5UJZ1FE.js"></script>

    <footer class="footer">
        <p>© 2025 Byte Builders — Built as a project for SECV2223 Web Programming</p>
    </footer>

</body>
</html>