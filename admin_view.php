<?php 
include 'db.php';
session_start();

// OPTIONAL: Restrict access to only logged-in admins
/*
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}
*/

// Handle delete request
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM admin WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $msg = "✅ Admin record deleted successfully.";
    } else {
        $msg = "❌ Error deleting record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Registered Admins</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      margin-top: 30px;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #007BFF;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    h2 {
      text-align: center;
      margin-top: 40px;
    }

    .notice {
      text-align: center;
      margin: 20px 0;
      font-weight: bold;
      color: #007BFF;
    }

    .msg {
      text-align: center;
      font-weight: bold;
      margin: 10px 0;
      color: green;
    }

    .delete-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 6px 10px;
      cursor: pointer;
      border-radius: 4px;
    }

    .delete-btn:hover {
      background-color: #c82333;
    }

    header h1 {
      text-align: center;
      padding: 20px 0;
    }

    nav {
      text-align: center;
      margin-bottom: 20px;
    }

    nav a {
      margin: 0 10px;
      text-decoration: none;
      color: #007BFF;
      font-weight: bold;
    }

    footer {
      background-color: #f1f1f1;
      padding: 20px;
      text-align: center;
      margin-top: 50px;
    }
  </style>
</head>
<body>

<header>
  <h1>JDIET's Paper Hub - Admin Panel</h1>
  <nav>
    <a href="login.php">Home</a>
    <a href="admin_view_students.php">View Students</a>
    <a href="admin_view_admins.php">View Admins</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="notice">
  <marquee>📢 Admin View: List of Registered Admins</marquee>
</div>

<?php if (isset($msg)): ?>
  <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<section class="admin-list">
  <h2>Registered Admins</h2>

  <?php
  $query = "SELECT id, Name, Address, Contact, Email, created_at FROM admin ORDER BY id DESC";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#ID</th>
          <th>Name</th>
          <th>Address</th>
          <th>Contact</th>
          <th>Email</th>
          <th>Registered On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['Name']) ?></td>
          <td><?= htmlspecialchars($row['Address']) ?></td>
          <td><?= htmlspecialchars($row['Contact']) ?></td>
          <td><?= htmlspecialchars($row['Email']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Are you sure you want to remove this admin?');">
              <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
              <button type="submit" class="delete-btn">Remove</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align:center; color:red; margin-top:20px;">❌ No admins found.</p>
  <?php endif; ?>
</section>

<footer>
  <div>
      <h3>Contact Info</h3>
      <p>📍 Yavatmal, India</p>
      <p>📞 +91 9156240822</p>
      <p>📧 support@jdietpyq.com</p>
  </div> 
  <div>
      <p>&copy; 2025 Previous Year Papers Portal | All Rights Reserved</p>
  </div>
</footer>

<?php $conn->close(); ?>
</body>
</html>