<?php
include 'db.php';
session_start();

// Only admin can access
if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$mes = "";

// Handle Upload
if (isset($_POST['upload'])) {
    $branch   = trim($_POST['branch']);
    $semester = trim($_POST['semester']);
    $year     = trim($_POST['year']);
    $examType = trim($_POST['exam_type']);

    if (empty($branch) || empty($semester) || empty($year) || empty($examType) || !isset($_FILES['paperFiles'])) {
        $mes = "❌ All fields are required.";
    } else {
        $files = $_FILES['paperFiles'];
        $total = count($files['name']);
        $successCount = 0;
        $failCount = 0;

        for ($i = 0; $i < $total; $i++) {
            $fileNameInput = trim($_POST['subjects'][$i] ?? '');
            if (empty($fileNameInput)) {
                $failCount++;
                continue;
            }

            if ($files['type'][$i] != "application/pdf") {
                $failCount++;
                continue;
            }

            // Folder structure
            $uploadDir = "papers/" . $examType . "/" . preg_replace('/\s+/', '_', $branch) .
                         "/Semester_" . $semester . "/Year_" . $year . "/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = strtolower(str_replace(' ', '_', $fileNameInput)) . "_" . $year . ".pdf";
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($files['tmp_name'][$i], $uploadFile)) {
                $table = ($examType === "University") ? "university_papers" : "autonomous_papers";

                $stmt = $conn->prepare("INSERT INTO $table (branch, semester, year, subject, filename, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt) die("SQL Error: " . $conn->error);
                $stmt->bind_param("sissss", $branch, $semester, $year, $fileNameInput, $fileName, $_SESSION['user_email']);
                $stmt->execute();
                $stmt->close();
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $mes = "✅ Uploaded $successCount papers. Failed: $failCount.";
    }

    echo "<script>alert('" . addslashes($mes) . "'); window.location='upload_paper.php';</script>";
}

// Fetch papers
$universityPapers = $conn->query("SELECT * FROM university_papers ORDER BY uploaded_on DESC");
$autonomousPapers = $conn->query("SELECT * FROM autonomous_papers ORDER BY uploaded_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Upload Papers</title>
<style>
/* ===== General Page Styling ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #dfe9f3, #ffffff);
  margin: 0;
  color: #333;
}

/* ===== Header Navigation ===== */
header {
  background: #0077b6;
  color: white;
  padding: 18px 0;
  text-align: center;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

header h1 {
  margin: 0;
  font-size: 26px;
  font-weight: 600;
}

nav {
  margin-top: 10px;
}

nav a {
  color: white;
  text-decoration: none;
  margin: 0 12px;
  font-weight: 500;
  transition: 0.3s;
}

nav a:hover {
  color: #90e0ef;
}

/* ===== Form Container ===== */
.container {
  background: white;
  max-width: 850px;
  margin: 40px auto;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.container h1, .container h2 {
  text-align: center;
  color: #0077b6;
  margin-bottom: 25px;
}

/* ===== Form Inputs ===== */
form input, form select, form button {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  font-size: 15px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-sizing: border-box;
}

form input:focus, form select:focus {
  outline: none;
  border-color: #0077b6;
  box-shadow: 0 0 4px rgba(0, 119, 182, 0.3);
}

/* ===== Buttons ===== */
button {
  background-color: #0077b6;
  color: white;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover { background-color: #023e8a; }

#addMore {
  background-color: #48cae4;
}

#addMore:hover {
  background-color: #0096c7;
}

/* ===== Table ===== */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  font-size: 14px;
}

th, td {
  padding: 12px 10px;
  border: 1px solid #ddd;
  text-align: center;
}

th {
  background: #0077b6;
  color: white;
}

tr:nth-child(even) {
  background: #f5faff;
}

tr:hover {
  background: #dff6ff;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .container {
    width: 90%;
    padding: 20px;
  }

  table {
    font-size: 12px;
  }
}
</style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <nav>
     <a href="main_page.php">Home</a>
    <a href="upload_paper.php">Upload Paper</a>
    <a href="University_paper.php">University Paper</a>
    <a href="Autonomous_paper.php">Autonomous Paper</a>
    <a href="view_student.php">View Students</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h1>📤 Upload Question Papers</h1>
  <form method="post" enctype="multipart/form-data">
    <input type="text" name="branch" placeholder="Branch (e.g. Computer Engineering)" required>
    <input type="number" name="semester" placeholder="Semester (1-8)" min="1" max="8" required>
    <input type="text" name="year" placeholder="Year (e.g. 2024)" required>

    <select name="exam_type" required>
      <option value="">-- Select Exam Type --</option>
      <option value="University">University</option>
      <option value="Autonomous">Autonomous</option>
    </select>

    <div id="fileInputs">
      <div class="file-group">
        <input type="text" name="subjects[]" placeholder="Subject Name" required>
        <input type="file" name="paperFiles[]" accept="application/pdf" required>
      </div>
    </div>

    <button type="button" id="addMore">➕ Add More Subjects</button>
    <button type="submit" name="upload">📎 Upload Papers</button>
  </form>
</div>




<script>
document.getElementById('addMore').addEventListener('click', function() {
  const container = document.getElementById('fileInputs');
  const div = document.createElement('div');
  div.classList.add('file-group');
  div.innerHTML = `
    <input type="text" name="subjects[]" placeholder="Subject Name" required>
    <input type="file" name="paperFiles[]" accept="application/pdf" required>
  `;
  container.appendChild(div);
});
</script>

</body>
</html>
