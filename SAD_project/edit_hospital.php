<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$hospital = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['hospital_name'];
  $location = $_POST['location'];
  $recommendation = $_POST['recommendation'];
  $details = $_POST['details'];

  if (!empty($_FILES['hospital_image']['name'])) {
    $imageName = $_FILES['hospital_image']['name'];
    $imageTmp = $_FILES['hospital_image']['tmp_name'];
    $imagePath = 'uploads/' . basename($imageName);
    move_uploaded_file($imageTmp, $imagePath);
  } else {
    $imagePath = $hospital['image_path'];
  }

  $update = "UPDATE hospitals SET name=?, location=?, recommendation=?, image_path=?, details=? WHERE id=?";
  $stmt = $conn->prepare($update);
  $stmt->bind_param("sssssi", $name, $location, $recommendation, $imagePath, $details, $id);
  $stmt->execute();

  header("Location: manage_hospitals.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Hospital</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      background-color: #f1f5f9;
      padding: 40px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h1 {
      color: #2563eb;
      margin-bottom: 25px;
      text-align: center;
    }
    form input, form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    form input[type="submit"] {
      background-color: #2563eb;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    form input[type="submit"]:hover {
      background-color: #1e40af;
    }
    .current-image {
      text-align: center;
      margin-bottom: 20px;
    }
    .current-image img {
      max-width: 300px;
      border-radius: 10px;
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
  <div class="container">
    <h1>Edit Hospital Info</h1>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="hospital_name" value="<?php echo htmlspecialchars($hospital['name']); ?>" placeholder="Hospital Name" required>
      <input type="text" name="location" value="<?php echo htmlspecialchars($hospital['location']); ?>" placeholder="Location" required>
      <textarea name="recommendation" rows="4" placeholder="Recommendation for Disease" required><?php echo htmlspecialchars($hospital['recommendation']); ?></textarea>
      <textarea name="details" rows="4" placeholder="Additional Details"><?php echo htmlspecialchars($hospital['details']); ?></textarea>

      <div class="current-image">
        <p>Current Image:</p>
        <img src="<?php echo $hospital['image_path']; ?>" alt="Hospital Image">
      </div>

      <input type="file" name="hospital_image" accept="image/*">
      <input type="submit" value="Update Hospital">
    </form>
    <a href="manage_hospitals.php" class="back-link">🔙 Back to Hospital List</a>
  </div>
</body>
</html>