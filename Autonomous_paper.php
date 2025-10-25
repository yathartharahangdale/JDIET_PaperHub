<?php
include 'db.php';
session_start();

// ✅ Only admin can access
if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Fetch autonomous papers
$autonomousPapers = $conn->query("SELECT * FROM autonomous_papers ORDER BY uploaded_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Autonomous Papers - Admin Panel</title>

<style>
/* ===== Base Page Styling ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #e0f7fa, #ffffff);
  margin: 0;
  color: #333;
}

/* ===== Header ===== */
header {
  background: #0077b6;
  color: #fff;
  text-align: center;
  padding: 20px 0;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

header h1 {
  margin: 0;
  font-size: 26px;
  font-weight: 600;
}

nav {
  margin-top: 8px;
}

nav a {
  color: #fff;
  text-decoration: none;
  margin: 0 15px;
  font-weight: 500;
  transition: 0.3s;
}

nav a:hover {
  color: #90e0ef;
}

/* ===== Container ===== */
.container {
  max-width: 1100px;
  margin: 40px auto;
  background: #ffffff;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.container h2 {
  text-align: center;
  color: #0077b6;
  margin-bottom: 20px;
}

/* ===== Table Styling ===== */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
  margin-top: 20px;
}

th, td {
  padding: 12px 10px;
  border: 1px solid #ddd;
  text-align: center;
}

th {
  background: #0077b6;
  color: #fff;
  font-weight: 600;
}

tr:nth-child(even) {
  background: #f1fbff;
}

tr:hover {
  background: #d9f2ff;
}

/* ===== Links ===== */
a {
  color: #0077b6;
  text-decoration: none;
  font-weight: 500;
}

a:hover {
  text-decoration: underline;
}

/* ===== Empty Message ===== */
.empty {
  text-align: center;
  color: #777;
  padding: 20px;
}

/* ===== Responsive Table ===== */
@media (max-width: 768px) {
  .container {
    width: 90%;
    padding: 20px;
  }

  table, th, td {
    font-size: 13px;
  }

  nav a {
    margin: 0 8px;
  }
}
</style>
</head>

<body>

<header>
  <h1>Admin Dashboard</h1>
  <nav>
       <a href="upload_paper.php">Upload Paper</a>
    <a href="view_student.php">View Students</a>
    <a href="university_paper.php">University Papers</a>
    <a href="autonomous_paper.php">Autonomous Papers</a>
    <a href="main_page.php">Home</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>📗 Uploaded Autonomous Papers</h2>

  <table>
    <tr>
      <th>ID</th>
      <th>Branch</th>
      <th>Semester</th>
      <th>Year</th>
      <th>Subject</th>
      <th>File</th>
      <th>Uploaded By</th>
      <th>Date</th>
    </tr>

    <?php if ($autonomousPapers && $autonomousPapers->num_rows > 0): ?>
      <?php while($row = $autonomousPapers->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['branch']) ?></td>
          <td><?= $row['semester'] ?></td>
          <td><?= htmlspecialchars($row['year']) ?></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td>
            <a href="papers/Autonomous/<?= htmlspecialchars($row['branch']) ?>/Semester_<?= $row['semester'] ?>/Year_<?= htmlspecialchars($row['year']) ?>/<?= $row['filename'] ?>" target="_blank">View</a>
            |
            <a href="papers/Autonomous/<?= htmlspecialchars($row['branch']) ?>/Semester_<?= $row['semester'] ?>/Year_<?= htmlspecialchars($row['year']) ?>/<?= $row['filename'] ?>" download>Download</a>
          </td>
          <td><?= htmlspecialchars($row['uploaded_by']) ?></td>
          <td><?= $row['uploaded_on'] ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="8" class="empty">No autonomous papers uploaded yet.</td>
      </tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
