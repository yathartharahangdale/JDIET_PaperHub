<?php
include 'db.php';

$mes = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['add'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $rawPassword = $_POST['password'] ?? '';

    // Validate fields
    if (empty($name) || empty($email) || empty($address) || empty($contact) || empty($rawPassword)) {
        $mes = "❌ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mes = "❌ Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        $mes = "❌ Contact number must be 10 digits.";
    } else {
        // Check duplicate email
        $check = $conn->prepare("SELECT id FROM admin WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mes = "❌ Email already registered.";
        } else {
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            // Insert using prepared statement
            $stmt = $conn->prepare("INSERT INTO admin (Name, Address, Contact, Email, Password) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $name, $address, $contact, $email, $hashedPassword);
                if ($stmt->execute()) {
                    $mes = "✅ Registration successful! Please login.";
                } else {
                    $mes = "❌ DB Error: " . $conn->error;
                }
            } else {
                $mes = "❌ Prepare error: " . $conn->error;
            }
        }
    }
}
?>

<html>
<head>
  <title>Admin Registration</title>
  <link rel="stylesheet" href="style/rg.css">
</head>
<body>
  <div class="container">
    <h1>Admin Registration</h1>
    <form method="post">
      <input type="text" name="name" placeholder="Full Name" required>
      <textarea name="add" placeholder="Address" required></textarea>
      <input type="text" name="contact" placeholder="Contact no." required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="register">Register</button>
    </form>
  </div>

  <?php if (!empty($mes)) : ?>
    <script>
      alert("<?php echo addslashes($mes); ?>");
      <?php if (strpos($mes, "successful") !== false): ?>
        window.location.href = "admin_login.php";
      <?php endif; ?>
    </script>
  <?php endif; ?>
</body>
</html>
