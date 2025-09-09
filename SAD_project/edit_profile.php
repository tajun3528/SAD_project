<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$name = $_SESSION['username']; // Assuming this stores the user's name

$stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $name); // ✅ Bind before executing
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; }
    .container { max-width: 900px; margin: 40px auto; display: flex; gap: 30px; }
    .profile { flex: 2; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .sidebar { flex: 1; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { color: #0077b6; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input, select { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    input[type="submit"] { background: #0077b6; color: white; border: none; margin-top: 20px; cursor: pointer; }
    input[type="submit"]:hover { background: #023e8a; }
    .profile-image { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="profile">
      <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h2>
      <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

        <label>Address</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">

        <label>Gender</label>
        <select name="gender">
          <option value="">Select</option>
          <option value="Male" <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
          <option value="Other" <?php if($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>

        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">

        <label>Profile Image</label>
        <?php if (!empty($user['profile_image'])): ?>
          <img src="<?php echo $user['profile_image']; ?>" class="profile-image" alt="Profile Image">
        <?php endif; ?>
        <input type="file" name="profile_image" accept="image/*">

        <input type="submit" value="Update Profile">
      </form>
    </div>

    <div class="sidebar">
      <h3>Account Summary</h3>
      <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role'] ?? 'User'); ?></p>
      <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status'] ?? 'Active'); ?></p>
      <p><strong>Member Since:</strong> <?php echo date("d M Y", strtotime($user['created_at'])); ?></p>
      <p><strong>Last Login:</strong> <?php echo !empty($user['last_login']) ? date("d M Y, h:i A", strtotime($user['last_login'])) : 'N/A'; ?></p>
    </div>
  </div>
</body>
</html>