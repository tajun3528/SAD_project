<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Fetch user profile image
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profileImage = $user['profile_image'] ?? 'default.png';

// Get hospital ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  echo "Invalid hospital ID.";
  exit();
}

// Fetch hospital details
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$hospital = $result->fetch_assoc();

if (!$hospital) {
  echo "Hospital not found.";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hospital Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f8ff;
    }
    header {
      background: linear-gradient(to right, #0077b6, #00b4d8);
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: white;
    }
    .title {
      font-size: 24px;
      font-weight: bold;
    }
    .search-bar {
      flex: 1;
      margin: 0 30px;
      display: flex;
      align-items: center;
    }
    .search-bar input {
      width: 100%;
      padding: 8px 12px;
      border: none;
      border-radius: 5px 0 0 5px;
      font-size: 14px;
    }
    .search-bar button {
      padding: 8px 12px;
      border: none;
      background-color: #023e8a;
      color: white;
      border-radius: 0 5px 5px 0;
      cursor: pointer;
    }
    .header-buttons {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .register-btn {
      background-color: #03045e;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    .profile-img-header {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
      transition: transform 0.2s;
    }
    .profile-img-header:hover {
      transform: scale(1.05);
      cursor: pointer;
    }
    nav {
      background-color: #03045e;
    }
    nav ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      flex: 1;
    }
    nav ul li a {
      display: block;
      text-align: center;
      padding: 15px;
      color: white;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    nav ul li a:hover {
      background-color: #0077b6;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      color: #0077b6;
      margin-bottom: 20px;
    }
    img {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    p {
      font-size: 16px;
      color: #333;
      margin-bottom: 10px;
    }
    .back-btn, .book-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #0077b6;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-right: 10px;
    }
    .back-btn:hover, .book-btn:hover {
      background-color: #023e8a;
    }
  </style>
</head>
<body>
  <header>
    <div class="title">Online Product & Hospital Recommendation</div>
    <div class="search-bar">
      <form method="GET" action="user_dashboard.php" style="display: flex; width: 100%;">
        <input type="text" name="search" placeholder="Search by disease, item, or location..." />
        <button type="submit">🔍</button>
      </form>
    </div>
    <div class="header-buttons">
      <a href="user_profile.php">
        <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile-img-header">
      </a>
      <button class="register-btn" onclick="location.href='logout.php'">Logout</button>
    </div>
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
    <h2><?php echo htmlspecialchars($hospital['name']); ?></h2>
    <img src="<?php echo $hospital['image_path']; ?>" alt="Hospital Image">
    <p><strong>Location:</strong> <?php echo htmlspecialchars($hospital['location']); ?></p>
    <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($hospital['recommendation']); ?></p>
    <p><strong>Details:</strong> <?php echo nl2br(htmlspecialchars($hospital['details'])); ?></p>
    <p><strong>Added on:</strong> <?php echo date("d M Y", strtotime($hospital['created_at'])); ?></p>
    <a href="user_pov_hospital_list.php" class="back-btn">← Back to Hospitals</a>
    <a href="book_hospital.php?id=<?php echo $hospital['id']; ?>" class="book-btn">Book Now</a>
  </div>
</body>
</html>