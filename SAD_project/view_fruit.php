<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM fruits WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$fruit = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Fruit Details</title></head>
<style>
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
  .content-box {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  .content-box h2 {
    margin-bottom: 20px;
    color: #2563eb;
  }
  form input, form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  form input[type="submit"] {
    background-color: #2563eb;
    color: white;
    border: none;
    cursor: pointer;
  }
  .fruit-details img {
    width: 200px;
    border-radius: 10px;
    margin-bottom: 20px;
  }
  .fruit-details p {
    margin: 8px 0;
    color: #334155;
  }
  .back-link {
    display: inline-block;
    margin-top: 20px;
    padding: 8px 16px;
    background-color: #0ea5e9;
    color: white;
    text-decoration: none;
    border-radius: 5px;
  }
</style>
</style>
<body>
  <div class="main">
  <div class="header">
    <h1>Fruit Details</h1>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
    <div class="content-box fruit-details">
     <img src="<?php echo $fruit['image_path']; ?>" alt="Fruit Image">
     <p><strong>Name:</strong> <?php echo htmlspecialchars($fruit['name']); ?></p>
     <p><strong>Category:</strong> <?php echo htmlspecialchars($fruit['category']); ?></p>
     <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($fruit['recommendation']); ?></p>
     <p><strong>Quantity:</strong> <?php echo htmlspecialchars($fruit['quantity']); ?></p>
     <p><strong>Price per Kg:</strong> ৳<?php echo htmlspecialchars(number_format($fruit['price_per_kg'], 2)); ?></p>
     <p><strong>Details:</strong> <?php echo nl2br(htmlspecialchars($fruit['details'])); ?></p>
     <p><em>Added on: <?php echo date("d M Y", strtotime($fruit['created_at'])); ?></em></p>
    <a href="manage_fruits.php" class="back-link">🔙 Back to list</a>
  </div>
  </div>
</body>
</html>