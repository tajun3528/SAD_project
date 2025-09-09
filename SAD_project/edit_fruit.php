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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $recommendation = $_POST['recommendation'];
  $quantity = intval($_POST['quantity']);
  $price_per_kg = floatval($_POST['price_per_kg']);
  $details = $_POST['details'];

  if (!empty($_FILES['image']['name'])) {
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = 'uploads/' . basename($imageName);
    move_uploaded_file($imageTmp, $imagePath);
  } else {
    $imagePath = $fruit['image_path'];
  }

  $update = "UPDATE fruits SET name=?, category=?, recommendation=?, image_path=?, quantity=?, price_per_kg=?, details=? WHERE id=?";
  $stmt = $conn->prepare($update);
  $stmt->bind_param("ssssddsi", $name, $category, $recommendation, $imagePath, $quantity, $price_per_kg, $details, $id);
  $stmt->execute();

  header("Location: manage_fruits.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Fruit</title>
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
</head>
<body>
  <div class="main">
    <div class="header">
      <h1>Edit Fruit</h1>
      <button onclick="location.href='logout.php'">Logout</button>
    </div>
    <div class="content-box">
      <h2>Edit Fruit Info</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($fruit['name']); ?>" required>
        <input type="text" name="category" value="<?php echo htmlspecialchars($fruit['category']); ?>" required>
        <textarea name="recommendation" required><?php echo htmlspecialchars($fruit['recommendation']); ?></textarea>
        <input type="number" name="quantity" value="<?php echo htmlspecialchars($fruit['quantity']); ?>" required>
        <input type="number" step="0.01" name="price_per_kg" value="<?php echo htmlspecialchars($fruit['price_per_kg']); ?>" required>
        <textarea name="details"><?php echo htmlspecialchars($fruit['details']); ?></textarea>
        <input type="file" name="image">
        <input type="submit" value="Update">
      </form>
    </div>
  </div>
</body>
</html>