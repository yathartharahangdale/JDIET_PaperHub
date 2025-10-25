<?php
session_start();
include 'db.php';
$message = "";

// Function to verify login credentials
function verifyUser($conn, $table, $email, $password) {
    $stmt = $conn->prepare("SELECT id, Name, Password FROM $table WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            return $row;
        }
    }
    return false;
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "❌ Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {
        // 🔹 Check Admin Table
        if ($user = verifyUser($conn, 'admin', $email, $password)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name']  = $user['Name'];
            $_SESSION['user_type']  = 'admin';
            $_SESSION['user_id']    = $user['id'];

            header("Location: upload_paper.php"); // Redirect to admin page
            exit();
        }

        // 🔹 Check Student Table
        elseif ($user = verifyUser($conn, 'studentdetail', $email, $password)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name']  = $user['Name'];
            $_SESSION['user_type']  = 'student';
            $_SESSION['user_id']    = $user['id'];

            header("Location: search_papers.php"); // Redirect to student dashboard
            exit();
        }

        // 🔹 No match found
        else {
            $message = "❌ Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="style/login.css">
</head>
<body>
<div class="container form-box">
    <h1>Login</h1>
    <form method="post" autocomplete="off">
        <input type="email" name="email" placeholder="Enter your Email" required>
        <input type="password" name="password" placeholder="Enter your Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p style="margin-top:15px;">New student? <a href="student_register.php">Register</a></p>
</div>

<?php if (!empty($message)) : ?>
<script>
alert("<?php echo addslashes($message); ?>");
</script>
<?php endif; ?>
</body>
</html>
