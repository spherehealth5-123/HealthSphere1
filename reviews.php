<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "healthsphere");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. HANDLE FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if (!empty($name) && !empty($email) && !empty($rating) && !empty($review)) {
        $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, review) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $email, $rating, $review);
        $stmt->execute();
        $stmt->close();
        // Refresh the page to show the new review and prevent double-submission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// 2. FETCH ALL REVIEWS
$reviews_result = $conn->query("SELECT name, rating, review FROM reviews ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Reviews | HealthSphere</title>
  <link rel="stylesheet" href="../css/reviews.css">
</head>
<body>
  <div class="container">
    <h1 class="title">What Our Patients Say</h1>
    <p class="subtitle">Real experiences from patients who trusted our hospital.</p>

    <div class="reviews">
      <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
          <?php while ($row = $reviews_result->fetch_assoc()): ?>
              <div class="review-card">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['name']); ?>&background=random" alt="User" class="review-img">
                <div class="review-content">
                  <div class="stars">
                    <?php 
                      // Generates star symbols based on rating number
                      echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); 
                    ?>
                  </div>
                  <p class="review-text">
                    "<?php echo htmlspecialchars($row['review']); ?>"
                  </p>
                  <p class="reviewer">- <?php echo htmlspecialchars($row['name']); ?></p>
                </div>
              </div>
          <?php endwhile; ?>
      <?php else: ?>
          <p style="text-align:center; color: #666;">No reviews yet. Be the first to share your experience!</p>
      <?php endif; ?>
    </div>

    <div class="review-form">
      <h2>Share Your Experience</h2>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        
        <select name="rating" required>
          <option value="">Select Rating</option>
          <option value="5">★★★★★ - Excellent</option>
          <option value="4">★★★★☆ - Good</option>
          <option value="3">★★★☆☆ - Average</option>
          <option value="2">★★☆☆☆ - Poor</option>
          <option value="1">★☆☆☆☆ - Very Bad</option>
        </select>
        
        <textarea name="review" rows="5" placeholder="Write your review here..." required></textarea>
        <button type="submit">Submit Review</button>
      </form>
    </div>
  </div>
</body>
</html>

<?php $conn->close(); ?>