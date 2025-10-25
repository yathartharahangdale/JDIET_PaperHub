<?php
include 'db.php';
session_start();
if (empty($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $sub = $_POST['sub'];
  $year = $_POST['year'];
  $mes="";
  // Main papers directory
  $uploadDir = "papers/";

  // Create year-wise folder if not exists
  $yearDir = $uploadDir . $year . "/";
  if (!is_dir($yearDir)) {
    mkdir($yearDir, 0777, true);
  }

  // File name format: subject_year.pdf
  $fileName = strtolower(str_replace(' ', '_', $sub)) . "_" . $year . ".pdf";
  $uploadFile = $yearDir . $fileName;

  // Check file type
  if ($_FILES['paperFile']['type'] != "application/pdf") {
    $mes= "❌ Only PDF files allowed.";
    exit;
  }

  // Move file
  if (move_uploaded_file($_FILES['paperFile']['tmp_name'], $uploadFile)) {
  $stmt = $conn->prepare("INSERT INTO papers (subject, year, filename, uploaded_on) VALUES (?, ?, ?, NOW())");
if (!$stmt) {
   $mes= "❌ Prepare Error: " . $conn->error;
  exit;
}
$stmt->bind_param("sss", $sub, $year, $fileName);


    if ($stmt->execute()) {
       $mes= "✅ Uploaded successfully. Stored in: $yearDir";
    } else {
       $mes= "❌ DB Error: " . $conn->error;
    }
  } else {
    $mes= "❌ Upload failed.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Papers</title>
  <link rel="stylesheet" href="style/upload.css">
</head>
<body>
  <div class="container">
    <h1>Upload Question Paper</h1>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="sub" placeholder="Subject" required>
      <input type="text" name="year" placeholder="Year (e.g. 2023)" required>
      <input type="file" name="paperFile" accept="application/pdf" required>
      <button type="submit" value="Upload">Upload</button>
    </form>
  </div>
   <?php if (!empty($mes)) : ?>
    <script>
      alert("<?php echo addslashes($mes); ?>");
    </script>
  <?php endif; ?>
</body>
</html>
