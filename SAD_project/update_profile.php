<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Collect form data
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];

// Handle image upload
$profile_image = '';
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
  $target_dir = "uploads/";
  $filename = basename($_FILES["profile_image"]["name"]);
  $target_file = $target_dir . time() . "_" . $filename;
  move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
  $profile_image = $target_file;
}

// Build query
if ($profile_image) {
  $stmt = $conn->prepare("UPDATE users SET email=?, phone=?, address=?, gender=?, dob=?, profile_image=? WHERE name=?");
  $stmt->bind_param("sssssss", $email, $phone, $address, $gender, $dob, $profile_image, $username);
} else {
  $stmt = $conn->prepare("UPDATE users SET email=?, phone=?, address=?, gender=?, dob=? WHERE name=?");
  $stmt->bind_param("ssssss", $email, $phone, $address, $gender, $dob, $username);
}

if ($stmt->execute()) {
  header("Location: user_profile.php"); // ✅ Redirect back to profile page
  exit();
} else {
  echo "Error updating profile.";
}
?><?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Collect form data
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];

// Handle image upload
$profile_image = '';
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
  $target_dir = "uploads/";
  $filename = basename($_FILES["profile_image"]["name"]);
  $target_file = $target_dir . time() . "_" . $filename;
  move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
  $profile_image = $target_file;
}

// Build query
if ($profile_image) {
  $stmt = $conn->prepare("UPDATE users SET email=?, phone=?, address=?, gender=?, dob=?, profile_image=? WHERE username=?");
  $stmt->bind_param("sssssss", $email, $phone, $address, $gender, $dob, $profile_image, $username);
} else {
  $stmt = $conn->prepare("UPDATE users SET email=?, phone=?, address=?, gender=?, dob=? WHERE username=?");
  $stmt->bind_param("ssssss", $email, $phone, $address, $gender, $dob, $username);
}

if ($stmt->execute()) {
  header("Location: user_profile.php"); // ✅ Redirect back to profile page
  exit();
} else {
  echo "Error updating profile.";
}
?>