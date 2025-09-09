<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT profile_image FROM users WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profileImage = $user['profile_image'] ?? 'default.png';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = ['hospitals' => [], 'fruits' => [], 'medicines' => []];

if ($search !== '') {
  $term = '%' . $search . '%';

  // Hospitals
  $stmt = $conn->prepare("SELECT * FROM hospitals WHERE name LIKE ? OR location LIKE ? OR recommendation LIKE ?");
  $stmt->bind_param("sss", $term, $term, $term);
  $stmt->execute();
  $results['hospitals'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  // Fruits
  $stmt = $conn->prepare("SELECT * FROM fruits WHERE name LIKE ? OR category LIKE ? OR recommendation LIKE ?");
  $stmt->bind_param("sss", $term, $term, $term);
  $stmt->execute();
  $results['fruits'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  // Medicines
  $stmt = $conn->prepare("SELECT * FROM medicines WHERE name LIKE ? OR category LIKE ? OR recommendation LIKE ?");
  $stmt->bind_param("sss", $term, $term, $term);
  $stmt->execute();
  $results['medicines'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function highlight($text, $keyword) {
  if (!$keyword) return htmlspecialchars($text);
  return str_ireplace($keyword, '<span class="highlight">' . htmlspecialchars($keyword) . '</span>', htmlspecialchars($text));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
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
    .main-content {
      padding: 40px;
      text-align: center;
    }
    .main-content h1 {
      color: #023e8a;
      margin-bottom: 20px;
    }
    .result-section {
      text-align: left;
      max-width: 900px;
      margin: 0 auto;
      margin-top: 30px;
    }
    .result-section h3 {
      color: #0077b6;
      margin-bottom: 10px;
    }
    .result-item {
      background: white;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .highlight {
      background-color: #ffe066;
      font-weight: bold;
      padding: 0 2px;
      border-radius: 3px;
    }
    .btn-view {
  background-color: #0ea5e9;
  color: white;
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 5px;
  margin-right: 10px;
  display: inline-block;
}
.btn-book {
  background-color: #10b981;
  color: white;
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 5px;
  display: inline-block;
}
.btn-book:hover {
  background-color: #059669;
}
  </style>
</head>
<body>
  <header>
    <div class="title">Online Product & Hospital Recommendation</div>
    <div class="search-bar">
      <form method="GET" action="" style="display: flex; width: 100%;">
        <input type="text" name="search" placeholder="Search by disease, item, or location..." value="<?php echo htmlspecialchars($search); ?>" />
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

  <div class="main-content">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>

    <?php if ($search !== ''): ?>
  <div class="result-section">
    <h3>Hospitals</h3>
    <?php foreach ($results['hospitals'] as $h): ?>
      <div class="result-item">
        <strong><?php echo highlight($h['name'], $search); ?></strong><br>
        Location: <?php echo highlight($h['location'], $search); ?><br>
        Recommendation: <?php echo highlight($h['recommendation'], $search); ?><br>
        <div style="margin-top: 10px;">
          <a href="user_pov_view_h.php?id=<?php echo $h['id']; ?>" class="btn-view">See Details</a>
          <a href="book_hospital.php?id=<?php echo $h['id']; ?>" class="btn-book">Book</a>
        </div>
      </div>
    <?php endforeach; ?>

    <h3>Fruits</h3>
<?php foreach ($results['fruits'] as $f): ?>
  <div class="result-item">
    <strong><?php echo highlight($f['name'], $search); ?></strong><br>
    Category: <?php echo highlight($f['category'], $search); ?><br>
    Price: ৳<?php echo $f['price_per_kg']; ?>/kg<br>
    Recommendation: <?php echo highlight($f['recommendation'], $search); ?><br>
    Quantity: <?php echo $f['quantity']; ?> kg<br>
    <div style="margin-top: 10px;">
      <a href="user_pov_view_f.php?id=<?php echo $f['id']; ?>" class="btn-view">See Details</a>
      <?php if ($f['quantity'] > 0): ?>
        <a href="order_fruit.php?id=<?php echo $f['id']; ?>" class="btn-book">Order</a>
      <?php else: ?>
        <button class="btn-book" disabled style="background-color: #ccc; cursor: not-allowed;">Out of Stock</button>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

    <h3>Medicines</h3>
<?php foreach ($results['medicines'] as $m): ?>
  <div class="result-item">
    <strong><?php echo highlight($m['name'], $search); ?></strong><br>
    Category: <?php echo highlight($m['category'], $search); ?><br>
    Price: ৳<?php echo $m['price_per_piece']; ?>/piece<br>
    Recommendation: <?php echo highlight($m['recommendation'], $search); ?><br>
    Quantity: <?php echo $m['quantity']; ?> pcs<br>
    <div style="margin-top: 10px;">
      <a href="user_pov_view_m.php?id=<?php echo $m['id']; ?>" class="btn-view">See Details</a>
      <?php if ($m['quantity'] > 0): ?>
        <a href="order_medicine.php?id=<?php echo $m['id']; ?>" class="btn-book">Order</a>
      <?php else: ?>
        <button class="btn-book" disabled style="background-color: #ccc; cursor: not-allowed;">Out of Stock</button>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
  </div>
<?php endif; ?>
  </div>
</body>
</html>