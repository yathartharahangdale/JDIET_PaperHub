<?php
include 'db.php';
$message = "";

if (isset($_POST['register'])) {
    $name       = trim($_POST['name'] ?? '');
    $year       = intval($_POST['year'] ?? 0);
    $department = trim($_POST['department'] ?? '');
    $college    = trim($_POST['college'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $type       = $_POST['student_type'] ?? ''; // university or autonomous

    // Validation
    if (empty($name) || empty($year) || empty($department) || empty($college) || empty($email) || empty($password) || empty($confirm_password) || empty($type)) {
        $message = "❌ Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM studentdetail WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result && $result->num_rows > 0) {
            $message = "⚠️ Email already registered.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data
            $stmt = $conn->prepare("INSERT INTO studentdetail (Name, Year, Department, College, Email, Password, StudentType) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssss", $name, $year, $department, $college, $email, $hashed_password, $type);

            if ($stmt->execute()) {
                $message = "✅ Registration successful! You can now login.";
            } else {
                $message = "❌ Error while registering: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration</title>
<link rel="stylesheet" href="style/login.css">
</head>
<body>
<div class="container form-box">
    <h1>Student Registration</h1>
    <form method="post" autocomplete="off">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="number" name="year" placeholder="Year (1-4)" min="1" max="4" required>
        <input type="text" name="department" placeholder="Department" required>
        <input type="text" name="college" placeholder="College" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <select name="student_type" required>
            <option value="">Select Type</option>
            <option value="university">University</option>
            <option value="autonomous">Autonomous</option>
        </select>
        <button type="submit" name="register">Register</button>
    </form>
    <p style="margin-top:15px;">Already registered? <a href="login.php">Login</a></p>
</div>

<?php if (!empty($message)) : ?>
<script>
alert("<?php echo addslashes($message); ?>");
</script>
<?php endif; ?>
</body>
</html>
