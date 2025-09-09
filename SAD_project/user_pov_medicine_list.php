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

if ($search !== '') {
  $stmt = $conn->prepare("SELECT * FROM medicines WHERE name LIKE ? OR category LIKE ? OR recommendation LIKE ? ORDER BY created_at DESC");
  $searchTerm = '%' . $search . '%';
  $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT * FROM medicines ORDER BY created_at DESC";
  $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Medicine Recommendations</title>
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
    }

    .main-content h2 {
      color: #023e8a;
      margin-bottom: 20px;
      text-align: center;
    }

    .medicine-card {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
      background: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      flex-wrap: wrap;
    }

    .medicine-card img {
      width: 120px;
      height: 80px;
      object-fit: cover;
      border-radius: 5px;
    }

    .medicine-info {
      flex: 1;
      min-width: 200px;
    }

    .medicine-info h3 {
      margin: 0;
      color: #0f172a;
    }

    .medicine-info p {
      margin: 5px 0;
      color: #64748b;
    }

    .btn {
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 5px;
      color: white;
      margin-right: 10px;
      font-weight: bold;
    }

    .btn-view {
      background-color: #0ea5e9;
    }

    .btn-order {
      background-color: #10b981;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-order:hover {
      background-color: #059669;
      transform: scale(1.05);
    }

    @media (max-width: 600px) {
      .medicine-card {
        flex-direction: column;
        align-items: center;
      }

      .btn-order {
        width: 100%;
        text-align: center;
        margin-top: 10px;
      }
    }
    .btn-order:disabled {
  background-color: #ccc;
  cursor: not-allowed;
  color: #333;
}
  </style>
</head>
<body>
  <header>
    <div class="title">Online Product & Hospital Recommendation</div>
    <div class="search-bar">
      <form method="GET" action="user_pov_medicine_list.php" style="display: flex; width: 100%;">
        <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo htmlspecialchars($search); ?>" />
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
    <h2>Available Medicines</h2>
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="medicine-card">
          <img src="<?php echo $row['image_path']; ?>" alt="Medicine Image">
          <div class="medicine-info">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
            <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($row['recommendation']); ?></p>
            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?> pcs</p>
            <p><strong>Price:</strong> ৳<?php echo htmlspecialchars(number_format($row['price_per_piece'], 2)); ?> per piece</p>
            <p><em>Added on: <?php echo date("d M Y", strtotime($row['created_at'])); ?></em></p>
            <div style="margin-top: 10px;">
              <a href="user_pov_view_m.php?id=<?php echo $row['id']; ?>" class="btn btn-view">See Details</a>
              <?php if ($row['quantity'] > 0): ?>
              <a href="add_to_cart.php?type=medicine&id=<?php echo $row['id']; ?>" class="btn btn-order">🛒 Add to Cart</a>
              <?php else: ?>
              <button class="btn btn-order" style="background-color: #ccc; cursor: not-allowed;" disabled>Out of Stock</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align: center;">No medicines found<?php echo $search ? ' for "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
    <?php endif; ?>
  </div>
</body>
</html>