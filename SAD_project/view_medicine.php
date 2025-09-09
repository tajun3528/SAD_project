<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM medicines WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medicine = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Medicine Details</title></head>
<body>
  <h2>Medicine Details</h2>
  <img src="<?php echo $medicine['image_path']; ?>" width="200"><br>
  <p><strong>Name:</strong> <?php echo $medicine['name']; ?></p>
  <p><strong>Category:</strong> <?php echo $medicine['category']; ?></p>
  <p><strong>Recommendation:</strong> <?php echo $medicine['recommendation']; ?></p>
  <p><strong>Quantity:</strong> <?php echo $medicine['quantity']; ?> pcs</p>
  <p><strong>Price:</strong> ৳<?php echo number_format($medicine['price_per_piece'], 2); ?> per piece</p>
  <p><strong>Details:</strong> <?php echo $medicine['details']; ?></p>
  <p><em>Added on: <?php echo date("d M Y", strtotime($medicine['created_at'])); ?></em></p>
  <a href="manage_medicines.php">🔙 Back to list</a>
</body>
</html>