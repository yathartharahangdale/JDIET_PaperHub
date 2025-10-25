<?php
include 'db.php';
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
     $mes="";

    $sql = "SELECT * FROM studentdetail WHERE Email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['Password'])) {
            $_SESSION['student_email'] = $email;
            header("Location:main_Page.php");
            exit();
        } else {
             $mes= "❌ Invalid password.";
        }
    } else {
         $mes= "❌ No account found with that email.";
    }
}
?>

<html>
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="style\login.css">
</head>
<body>
<div class="container form-box">
<h1>Student Login</h1>
<form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>
  <p style="text-align:center;margin-top:10px;color:white;">
      New user? <a href="Student_Register.php">Register</a>
    </p>
</div>
<?php if (!empty($mes)) : ?>
    <script>
      alert("<?php echo addslashes($mes); ?>");
    </script>
  <?php endif; ?>
</body>
</html>
