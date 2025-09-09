<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; }
    .container { max-width: 900px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .header { display: flex; align-items: center; gap: 20px; }
    .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #0077b6; }
    .info { margin-top: 30px; }
    .info p { margin: 10px 0; font-size: 16px; }
    .label { font-weight: bold; color: #555; }
    .section { margin-top: 40px; }
    .section h3 { color: #0077b6; margin-bottom: 10px; }
    .actions a { text-decoration: none; padding: 10px 20px; background: #0077b6; color: white; border-radius: 5px; margin-right: 10px; }
    .actions a:hover { background: #023e8a; }
    .navbar {
  background-color: #0077b6;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.nav-brand {
  font-size: 22px;
  font-weight: bold;
}
.nav-links {
  list-style: none;
  display: flex;
  gap: 20px;
  margin: 0;
  padding: 0;
}
.nav-links li a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}
.nav-links li a:hover,
.nav-links li a.active {
  color: #90e0ef;
}
  </style>
</head>
<body>
    <nav class="navbar">
  <div class="nav-brand">Online Product & Hospital Recommendation</div>
  <ul class="nav-links">
    <li><a href="user_dashboard.php">Dashboard</a></li>
    <li><a href="user_profile.php" class="active">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>
  <div class="container">
    <div class="header">
      <img src="<?php echo $user['profile_image'] ?: 'default.png'; ?>" class="profile-img" alt="Profile Image">
      <div>
        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
      </div>
    </div>

    <div class="info">
      <p><span class="label">Username:</span> <?php echo htmlspecialchars($user['name']); ?></p>
      <p><span class="label">Phone:</span> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
      <p><span class="label">Address:</span> <?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></p>
      <p><span class="label">Gender:</span> <?php echo htmlspecialchars($user['gender'] ?? 'N/A'); ?></p>
      <p><span class="label">Date of Birth:</span> <?php echo htmlspecialchars($user['dob'] ?? 'N/A'); ?></p>
      <p><span class="label">Role:</span> <?php echo htmlspecialchars($user['role'] ?? 'User'); ?></p>
      <p><span class="label">Status:</span> <?php echo htmlspecialchars($user['status'] ?? 'Active'); ?></p>
      <p><span class="label">Member Since:</span> <?php echo date("d M Y", strtotime($user['created_at'])); ?></p>
      <p><span class="label">Last Login:</span> <?php echo !empty($user['last_login']) ? date("d M Y, h:i A", strtotime($user['last_login'])) : 'N/A'; ?></p>
    </div>

    <div class="section">
      <h3>Wishlist</h3>
      <p><?php echo htmlspecialchars($user['wishlist'] ?? 'No items'); ?></p>
    </div>

    <div class="section">
      <h3>Cart Items</h3>
      <p><?php echo htmlspecialchars($user['cart_items'] ?? 'Empty'); ?></p>
    </div>

    <div class="section">
      <h3>Preferences</h3>
      <p><?php echo htmlspecialchars($user['preferences'] ?? 'Not set'); ?></p>
    </div>

    <div class="actions">
      <a href="view_cart.php">My Cart</a>
      <a href="order_status.php">My order</a>
      <a href="edit_profile.php">Edit Profile</a>
      <a href="change_password.php">Change Password</a>
      <a href="recommendations.php">View Recommendations</a>
    </div>
  </div>
</body>
</html>