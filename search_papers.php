<?php
include 'db.php';
session_start();

// Only logged-in students can access
if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] !== 'student') {
    header("Location: login.php");
    exit();
}

$results = [];
$mes = "";
$branch = $_POST['branch'] ?? '';
$semester = $_POST['semester'] ?? '';
$examType = $_POST['exam_type'] ?? ''; // University or Autonomous

// Handle search
if (isset($_POST['search'])) {
    $branch   = trim($_POST['branch']);
    $semester = trim($_POST['semester']);
    $examType = trim($_POST['exam_type']);

    if (empty($branch) || empty($semester) || empty($examType)) {
        $mes = "❌ All fields are required.";
    } else {
        // Choose the correct table based on exam type
        $table = ($examType === "University") ? "university_papers" : "autonomous_papers";

        $stmt = $conn->prepare("SELECT * FROM `$table` WHERE branch=? AND semester=? ORDER BY uploaded_on DESC");
        $stmt->bind_param("si", $branch, $semester);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            $mes = "❌ No papers found for $branch - Semester $semester ($examType).";
        }
        $stmt->close();
    }
}

// Handle ZIP download
if (isset($_POST['download_zip']) && !empty($_POST['branch']) && !empty($_POST['semester']) && !empty($_POST['exam_type'])) {
    $branch = $_POST['branch'];
    $semester = $_POST['semester'];
    $examType = $_POST['exam_type'];
    $table = ($examType === "University") ? "university_papers" : "autonomous_papers";

    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE branch=? AND semester=?");
    $stmt->bind_param("si", $branch, $semester);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $zip = new ZipArchive();
        $zipName = "papers_" . preg_replace('/\s+/', '_', $branch) . "_sem" . $semester . "_" . strtolower($examType) . ".zip";
        $zipPath = sys_get_temp_dir() . "/" . $zipName;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            while ($row = $res->fetch_assoc()) {
                $branchFolder = preg_replace('/\s+/', '_', $row['branch']);
                $filePath = "papers/$examType/" . $branchFolder . "/Semester_" . $row['semester'] . "/Year_" . $row['year'] . "/" . $row['filename'];
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $row['filename']);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            unlink($zipPath);
            exit();
        } else {
            $mes = "❌ Failed to create ZIP file.";
        }
    } else {
        $mes = "❌ No papers available for ZIP download.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PapersHub | Search Question Papers</title>
  <style>
    /* ===== General ===== */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

    body {
      background: linear-gradient(120deg, #f5f9ff, #dce7ff);
      color: #333;
      min-height: 100vh;
    }

    /* ===== Header ===== */
    header {
      background: rgba(0, 62, 112, 0.95);
      color: #fff;
      padding: 18px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.25);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h1 {
      font-size: 1.6rem;
      letter-spacing: 1px;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
      transition: 0.3s;
    }

    nav a:hover {
      color: #ffdd57;
    }

    /* ===== Main Content ===== */
    main {
      max-width: 1000px;
      margin: 60px auto;
      background: #fff;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #004d7a;
      font-size: 1.8rem;
      margin-bottom: 25px;
    }

    /* ===== Form ===== */
    form {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-bottom: 30px;
    }

    form input, form select {
      padding: 12px;
      border: 2px solid #cdd6f6;
      border-radius: 10px;
      font-size: 1rem;
      width: 250px;
      transition: 0.3s;
    }

    form input:focus, form select:focus {
      border-color: #00b894;
      box-shadow: 0 0 6px rgba(0,184,148,0.4);
      outline: none;
    }

    form button {
      background: #00b894;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    form button:hover {
      background: #ffdd57;
      color: #004d7a;
    }

    /* ===== Table ===== */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 14px 16px;
      text-align: center;
      font-size: 15px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: #004d7a;
      color: white;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    tr:hover {
      background: #eaf4ff;
    }

    a {
      color: #00b894;
      font-weight: 600;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .message {
      text-align: center;
      font-weight: bold;
      color: red;
      margin-top: 15px;
    }

    /* ===== Footer ===== */
    footer {
      text-align: center;
      background: #004d7a;
      color: white;
      padding: 15px;
      font-size: 0.9rem;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.15);
      margin-top: 50px;
    }

    @media (max-width: 768px) {
      form input, form select { width: 100%; }
      main { padding: 25px; margin: 40px 15px; }
      h2 { font-size: 1.4rem; }
    }
  </style>
</head>
<body>

<header>
  <h1>📘 Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
  <nav>
    <a href="main_Page.php">Home</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <h2>🔍 Search Question Papers</h2>

  <form method="post">
    <input type="text" name="branch" placeholder="Branch (e.g. Computer Engineering)" value="<?= htmlspecialchars($branch) ?>" required>
    <input type="number" name="semester" placeholder="Semester (1-8)" min="1" max="8" value="<?= htmlspecialchars($semester) ?>" required>
    <select name="exam_type" required>
      <option value="">-- Select Exam Type --</option>
      <option value="University" <?= ($examType === 'University') ? 'selected' : '' ?>>University</option>
      <option value="Autonomous" <?= ($examType === 'Autonomous') ? 'selected' : '' ?>>Autonomous</option>
    </select>
    <button type="submit" name="search">Search</button>
    <?php if (!empty($results)): ?>
      <button type="submit" name="download_zip">⬇ Download All as ZIP</button>
    <?php endif; ?>
  </form>

  <?php if (!empty($mes)): ?>
    <p class="message"><?= $mes ?></p>
  <?php elseif (!empty($results)): ?>
    <table>
      <tr>
        <th>Subject</th>
        <th>Year</th>
        <th>View</th>
        <th>Download</th>
      </tr>
      <?php foreach ($results as $row): 
        $branchSafe = htmlspecialchars($row['branch']);
        $examTypeFolder = htmlspecialchars($examType);
        $filePath = "papers/$examTypeFolder/" . $branchSafe . "/Semester_" . $row['semester'] . "/Year_" . $row['year'] . "/" . $row['filename'];
      ?>
        <tr>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= htmlspecialchars($row['year']) ?></td>
          <td><a href="<?= $filePath ?>" target="_blank">👁 View</a></td>
          <td><a href="<?= $filePath ?>" download>⬇ Download</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</main>

<footer>
  © <?= date("Y"); ?> PapersHub | Developed by Sharwari & Yatharth Rahangdale
</footer>

</body>
</html>
