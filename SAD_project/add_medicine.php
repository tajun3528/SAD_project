<?php
include 'db.php'; // Ensure this file defines $conn properly

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $recommendation = $_POST['recommendation'];
    $details = $_POST['details'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $created_at = date('Y-m-d H:i:s'); // Current timestamp

    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = 'uploads/' . basename($imageName);

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $sql = "INSERT INTO medicines (name, category, recommendation, image_path, quantity, price_per_piece, details, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssddss", $name, $category, $recommendation, $imagePath, $quantity, $price, $details, $created_at);

        if ($stmt->execute()) {
            echo "✅ Medicines added successfully!";
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Failed to upload image.";
    }

    $conn->close();
}
?>