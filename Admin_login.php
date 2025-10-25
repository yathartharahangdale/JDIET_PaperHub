<?php
include 'db.php';
session_start();

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (!empty($email) && !empty($password)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('❌ Invalid email format.');</script>";
        } else {
            // Use prepared statement
            $stmt = $conn->prepare("SELECT Password FROM admin WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();

                if (password_verify($password, $row['Password'])) {
                    $_SESSION['admin_email'] = $email;
                    header("Location: admin_upload.php");
                    exit;
                } else {
                    echo "<script>alert('❌ Invalid password.');</script>";
                }
            } else {
                echo "<script>alert('❌ No admin account found with that email.');</script>";
            }

            $stmt->close();
        }
    } else {
        echo "<script>alert('❌ Please fill all fields.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="style/login.css">
</head>
<body>
  <div class="container">
    <h1>Admin Login</h1>
    <form method="post" autocomplete="off">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
    </form>
    <p style="text-align:center;margin-top:10px;color:white;">
      New admin? <a href="admin_register.php">Register</a>
    </p>
  </div>
</body>
</html>
