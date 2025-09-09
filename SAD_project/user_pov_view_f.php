<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Fetch profile image
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$profileImage = $user['profile_image'] ?? 'default.png';

// Get fruit details
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM fruits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$fruit = $stmt->get_result()->fetch_assoc();
if (!$fruit) {
  echo "Fruit not found.";
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Fruit Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body { margin: 0; font-family: 'Segoe UI'; background-color: #f0f8ff; }
    header, nav, .container { max-width: 1000px; margin: auto; }
    header {
      background: linear-gradient(to right, #0077b6, #00b4d8);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }
    .title { font-size: 24px; font-weight: bold; }
    .search-bar { flex: 1; margin: 0 30px; display: flex; }
    .search-bar input { flex: 1; padding: 8px; border-radius: 5px 0 0 5px; border: none; }
    .search-bar button { padding: 8px 12px; border: none; background: #023e8a; color: white; border-radius: 0 5px 5px 0; cursor: pointer; }
    .profile-img-header { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid white; }
    nav { background-color: #03045e; }
    nav ul { display: flex; list-style: none; padding: 0; margin: 0; }
    nav ul li a { flex: 1; text-align: center; padding: 15px; display: block; color: white; text-decoration: none; font-weight: bold; }
    nav ul li a:hover { background-color: #0077b6; }
    .container { background: white; padding: 30px; margin-top: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    img { width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; }
    .btn { padding: 10px 20px; background: #0077b6; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .btn:hover { background: #023e8a; }

    /* Modal styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .modal-content {
      background: white; margin: 10% auto; padding: 20px; border-radius: 8px; width: 400px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .close { float: right; font-size: 24px; cursor: pointer; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <header>
    <div class="title">Online Product & Hospital Recommendation</div>
    <div class="search-bar">
      <form method="GET" action="user_dashboard.php">
        <input type="text" name="search" placeholder="Search by disease, item, or location..." />
        <button type="submit">🔍</button>
      </form>
    </div>
    <a href="user_profile.php">
      <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile-img-header">
    </a>
    <button class="btn" onclick="location.href='logout.php'">Logout</button>
  </header>

  <nav>
    <ul>
      <li><a href="user_dashboard.php">Home</a></li>
      <li><a href="user_pov_hospital_list.php">Hospital</a></li>
      <li><a href="User_pov_fruits_list.php">Food</a></li>
      <li><a href="user_pov_medicine_list.php">Madicine</a></li>
      <li><a href="view_cart.php" class="active">View Cart</a></li>
      <li><a href="order_status.php">Order Status</a></li>
    </ul>
  </nav>

  <div class="container">
    <h2><?php echo htmlspecialchars($fruit['name']); ?></h2>
    <img src="<?php echo $fruit['image_path']; ?>" alt="fruits Image">
    <p><strong>Category:</strong> <?php echo htmlspecialchars($fruit['category']); ?></p>
    <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($fruit['recommendation']); ?></p>
    <p><strong>Details:</strong> <?php echo nl2br(htmlspecialchars($fruit['details'])); ?></p>
    <p><strong>Quantity Available:</strong> <?php echo htmlspecialchars($fruit['quantity']); ?> pcs</p>
    <p><strong>Price per kg:</strong> ৳<?php echo htmlspecialchars(number_format($fruit['price_per_kg'], 2)); ?></p>
    <p><strong>Added on:</strong> <?php echo date("d M Y", strtotime($fruit['created_at'])); ?></p>
    <a href="user_pov_fruits_list.php" class="back-btn">← Back to fruits</a>
    <a href="add_to_cart.php?type=fruit&id=<?php echo $fruit['id']; ?>" class="order-btn">Add to Cart</a>
  </div>
</body>
</html>