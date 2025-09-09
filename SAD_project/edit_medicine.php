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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $recommendation = $_POST['recommendation'];
  $quantity = intval($_POST['quantity']);
  $price = floatval($_POST['price']);
  $details = $_POST['details'];

  if (!empty($_FILES['image']['name'])) {
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = 'uploads/' . basename($imageName);
    move_uploaded_file($imageTmp, $imagePath);
  } else {
    $imagePath = $medicine['image_path'];
  }

  $update = "UPDATE medicines SET name=?, category=?, recommendation=?, quantity=?, price_per_piece=?, details=?, image_path=? WHERE id=?";
  $stmt = $conn->prepare($update);
  $stmt->bind_param("sssidssi", $name, $category, $recommendation, $quantity, $price, $details, $imagePath, $id);
  $stmt->execute();

  header("Location: manage_medicines.php");
  exit();
}
?>

<!-- Simple edit form -->
<!DOCTYPE html>
<html>
<head><title>Edit Medicine</title></head>
<body>
  <h2>Edit Medicine</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" value="<?php echo $medicine['name']; ?>" required><br>
    <input type="text" name="category" value="<?php echo $medicine['category']; ?>" required><br>
    <textarea name="recommendation" required><?php echo $medicine['recommendation']; ?></textarea><br>
    <input type="number" name="quantity" value="<?php echo $medicine['quantity']; ?>" required><br>
    <input type="number" step="0.01" name="price" value="<?php echo $medicine['price_per_piece']; ?>" required><br>
    <textarea name="details"><?php echo $medicine['details']; ?></textarea><br>
    <input type="file" name="image"><br>
    <input type="submit" value="Update">
  </form>
</body>
</html>