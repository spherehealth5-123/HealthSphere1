<?php
// Database connection
$servername = "localhost";
$username   = "root";    // change if needed
$password   = "";        // change if needed
$dbname     = "healthsphere";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch reviews
$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Reviews</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
    .container { max-width: 900px; margin: auto; }
    .title { text-align: center; color: #2c3e50; }
    .reviews { margin-top: 20px; }
    .review-card { display: flex; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; }
    .review-img { width: 60px; height: 60px; border-radius: 50%; margin-right: 15px; }
    .stars { color: gold; font-size: 18px; }
    .reviewer { font-weight: bold; color: #333; }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="title">What Our Patients Say</h1>

    <div class="reviews">
      <?php if ($result && $result->num_rows > 0) { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <div class="review-card">
            <img src="https://randomuser.me/api/portraits/<?php echo (rand(0,1) ? 'men' : 'women'); ?>/<?php echo rand(1,80); ?>.jpg" 
                 alt="<?php echo htmlspecialchars($row['name']); ?>" class="review-img">
            <div>
              <div class="stars">
                <?php echo str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']); ?>
              </div>
              <p>"<?php echo htmlspecialchars($row['review']); ?>"</p>
              <p class="reviewer">- <?php echo htmlspecialchars($row['name']); ?></p>
            </div>
          </div>
        <?php } ?>
      <?php } else { ?>
        <p>No reviews yet.</p>
      <?php } ?>
    </div>
  </div>
</body>
</html>
