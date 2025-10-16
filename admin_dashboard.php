<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
  }
  // print_r($_SESSION);

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: weather.php");
    exit();
}

include "db.php";


// Fetch all users
$sql = "SELECT id, name, email, is_admin FROM user_master ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Weather App</title>
<link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <span class="welcome">👤 Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (Admin)</span>
      <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>

    <h1>🌦️ Admin Dashboard - Registered Users</h1>

    <div class="stats">
      <span>📊 Total Users: <strong><?php echo mysqli_num_rows($result); ?></strong></span>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
              <?php if ($row['is_admin'] == 1): ?>
                <span class="admin-badge">Admin</span>
              <?php else: ?>
                User
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['id'] != $_SESSION['user_id']): ?>
                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="text-align:center; padding:40px; color:#ffcc66;">📭 No users registered yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>
<?php mysqli_close($conn); ?>
