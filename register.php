<?php
include 'db.php';
session_start();

$mes = "";

if (isset($_POST['register'])) {
    $user_type  = $_POST['user_type'] ?? '';
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $con_pass   = $_POST['con_pass'];

    // Additional fields for students
    $yr         = trim($_POST['yr'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $college    = trim($_POST['college'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($con_pass)) {
        $mes = "❌ All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mes = "❌ Invalid email format!";
    } elseif ($password !== $con_pass) {
        $mes = "❌ Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $mes = "❌ Password must be at least 6 characters.";
    } elseif ($user_type !== 'admin' && $user_type !== 'student') {
        $mes = "❌ Please select a valid user type.";
    } else {
        $table = $user_type === 'admin' ? 'admin' : 'studentdetail';
        $stmt = $conn->prepare("SELECT id FROM $table WHERE Email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mes = "❌ Email already registered.";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

            if ($user_type === 'admin') {
                $insert = $conn->prepare("INSERT INTO admin (Name, Email, Password) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $name, $email, $hashed_pass);
            } else {
                $insert = $conn->prepare("INSERT INTO studentdetail (Name, Year, Department, College, Email, Password) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->bind_param("sissss", $name, $yr, $department, $college, $email, $hashed_pass);
            }

            if ($insert->execute()) {
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name']  = $name;
                $_SESSION['user_type']  = $user_type;
                $_SESSION['user_id']    = $insert->insert_id;

                echo "<script>alert('✅ Registration successful!'); window.location='dashboard.php';</script>";
                exit();
            } else {
                $mes = "❌ Database error: " . $insert->error;
            }
            $insert->close();
        }
        $stmt->close();
    }

    if (!empty($mes)) {
        echo "<script>alert('" . addslashes($mes) . "');</script>";
    }
}
?>

<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style/rg.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="post">
            <select name="user_type" required>
                <option value="">Select User Type</option>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>

            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" minlength="6" required>
            <input type="password" name="con_pass" placeholder="Confirm Password" minlength="6" required>

            <!-- Student fields -->
            <div id="student_fields">
                <input type="number" name="yr" placeholder="Year (1-4)">
                <input type="text" name="department" placeholder="Department">
                <input type="text" name="college" placeholder="College Name">
            </div>

            <button type="submit" name="register">Register</button>
        </form>
    </div>

    <script>
        const userTypeSelect = document.querySelector('select[name="user_type"]');
        const studentFields = document.getElementById('student_fields');

        function toggleStudentFields() {
            if (userTypeSelect.value === 'student') {
                studentFields.style.display = 'block';
                studentFields.querySelectorAll('input').forEach(i => i.required = true);
            } else {
                studentFields.style.display = 'none';
                studentFields.querySelectorAll('input').forEach(i => i.required = false);
            }
        }

        userTypeSelect.addEventListener('change', toggleStudentFields);
        toggleStudentFields();
    </script>
</body>
</html>
