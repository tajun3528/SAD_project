<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}
$admin = $_SESSION['admin_name'];
$hospitalCount = $conn->query("SELECT COUNT(*) AS total FROM hospitals")->fetch_assoc()['total'];
$medicineCount = $conn->query("SELECT COUNT(*) AS total FROM medicines")->fetch_assoc()['total'];
$fruitCount = $conn->query("SELECT COUNT(*) AS total FROM fruits")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      display: flex;
      min-height: 100vh;
      background-color: #f1f5f9;
    }
    .sidebar {
      width: 220px;
      background-color: #0f172a;
      color: white;
      padding: 20px;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 22px;
      color: #38bdf8;
    }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin: 15px 0;
      padding: 10px;
      border-radius: 5px;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background-color: #1e293b;
    }
    .main {
      flex: 1;
      padding: 30px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .header h1 {
      color: #0f172a;
    }
    .header button {
      padding: 10px 20px;
      background-color: #ef4444;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .card {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card h3 {
      margin-bottom: 10px;
      color: #2563eb;
    }
    .card p {
      color: #64748b;
    }
    .cards {
  display: flex;
  gap: 20px;
  margin-top: 30px;
}

.card-link {
  text-decoration: none;
  flex: 1;
}

.card {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s, box-shadow 0.2s;
  text-align: center;
  color: #0f172a;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  cursor: pointer;
}

.card h3 {
  margin-bottom: 10px;
  color: #2563eb;
}

.card p {
  font-size: 16px;
  color: #64748b;
}
  </style>
</head>
<body>
  <div class="sidebar">
     <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">🏠 Home</a>
    <a href="manage_hospitals.php">🏥 Manage Hospitals</a>
    <a href="manage_medicines.php">💊 Manage Medicines</a>
    <a href="manage_fruits.php">🍎 Manage Fruits</a>
    <a href="admin_order.php">📦 Order List</a>
    <a href="#">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="header">
      <h1>Welcome, <?php echo htmlspecialchars($admin); ?> 👋</h1>
      <button onclick="location.href='logout.php'">Logout</button>
    </div>

    <div class="cards">
  <a href="hospital_list.php" class="card-link">
    <div class="card">
      <h3>Total Hospitals</h3>
      <p><?php echo $hospitalCount; ?> Registered</p>
    </div>
  </a>
  <a href="medicine_list.php" class="card-link">
    <div class="card">
      <h3>Total Medicines</h3>
      <p><?php echo $medicineCount; ?> Available</p>
    </div>
  </a>
  <a href="fruits_list.php" class="card-link">
    <div class="card">
      <h3>Fruits</h3>
      <p><?php echo $fruitCount; ?> listed</p>
    </div>
  </a>
</div>
    </div>
  </div>
</body>
</html>