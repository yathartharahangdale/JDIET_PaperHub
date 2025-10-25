<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PapersHub | Question Paper Management System</title>

<style>
  /* ===== GENERAL STYLING ===== */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }

  body {
    background: linear-gradient(120deg, #f5f9ff, #dce7ff);
    color: #333;
    overflow-x: hidden;
  }

  /* ===== HEADER ===== */
  header {
    background: rgba(0, 62, 112, 0.95);
    color: white;
    padding: 18px 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(8px);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  header h1 {
    font-size: 1.8rem;
    letter-spacing: 1px;
  }

  nav a {
    color: white;
    text-decoration: none;
    margin-left: 25px;
    font-weight: 500;
    transition: 0.3s;
  }

  nav a:hover {
    color: #ffdd57;
  }

  /* ===== HERO SECTION ===== */
  .hero {
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    background: url('https://jdiet.ac.in/image/5d283d25332705d283d2533279.jpg') no-repeat center/cover;
    position: relative;
    color: white;
  }

  .hero::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,77,122,0.6));
  }

  .hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    padding: 20px;
    animation: fadeIn 1.5s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .hero h2 {
    font-size: 3rem;
    margin-bottom: 15px;
    text-shadow: 0 4px 12px rgba(0,0,0,0.4);
  }

  .hero p {
    font-size: 1.2rem;
    margin-bottom: 35px;
    line-height: 1.6;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }

  .hero .btn-group a {
    background: #00b894;
    color: white;
    text-decoration: none;
    padding: 12px 25px;
    margin: 10px;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }

  .hero .btn-group a:hover {
    background: #ffdd57;
    color: #004d7a;
  }

  /* ===== FEATURES ===== */
  .features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 80px 60px;
    background: linear-gradient(135deg, #f0f7ff, #ffffff);
  }

  .feature-box {
    background: white;
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    color: #333;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .feature-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
  }

  .feature-box h3 {
    font-size: 1.3rem;
    margin-bottom: 12px;
    color: #004d7a;
    font-weight: 700;
  }

  .feature-box p {
    font-size: 1rem;
    color: #555;
  }

  /* ===== ABOUT SECTION ===== */
  .about {
    background: #ffffff;
    padding: 80px 60px;
    text-align: center;
  }

  .about h2 {
    font-size: 2rem;
    color: #004d7a;
    margin-bottom: 25px;
  }

  .about p {
    font-size: 1.1rem;
    color: #555;
    max-width: 800px;
    margin: auto;
    line-height: 1.7;
  }

  .about ul {
    list-style: none;
    margin-top: 25px;
    padding: 0;
  }

  .about ul li {
    font-size: 1rem;
    padding: 8px 0;
    color: #333;
  }

  .about ul li::before {
    content: "✔️ ";
    color: #00b894;
  }

  /* ===== FOOTER ===== */
  footer {
    text-align: center;
    background: #004d7a;
    color: white;
    padding: 18px;
    font-size: 0.95rem;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.15);
  }

  /* ===== CHATBOT BUTTON ===== */
  .chatbot-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #00a86b;
    color: white;
    border: none;
    border-radius: 50%;
    width: 55px;
    height: 55px;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: 0.3s;
  }

  .chatbot-btn:hover {
    background: #ffdd57;
    color: #004d7a;
  }

  @media (max-width: 768px) {
    header { flex-direction: column; text-align: center; }
    nav { margin-top: 10px; }
    .hero h2 { font-size: 2.2rem; }
    .hero p { font-size: 1rem; }
  }
</style>
</head>

<body>
  <header>
    <h1>📘 PapersHub </h1>
    <nav>
      <a href="login.php">Login</a>
      <a href="student_register.php">Register</a>
      <a href="#about">About</a>
      <a href="contact.php">Contact</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>Access & Upload Question Papers Easily</h2>
      <p>Streamlining question paper management for students and admins — fast, secure, and organized.</p>

      <div class="btn-group">
        <a href="login.php">👨‍🎓 Student Login</a>
        <a href="student_register.php">📝 Register</a>
      </div>
    </div>
  </section>

  <section class="features">
    <div class="feature-box">
      <h3>🎓 Student Access</h3>
      <p>University and Autonomous students can search and download question papers effortlessly.</p>
    </div>

    <div class="feature-box">
      <h3>🔐 Secure Login System</h3>
      <p>Role-based authentication ensures privacy and controlled access for all users.</p>
    </div>

    <div class="feature-box">
      <h3>⚡ Instant Downloads</h3>
      <p>Download papers instantly or view them online — no delays, no barriers.</p>
    </div>
  </section>

  <!-- ===== ABOUT SECTION ===== -->
  <section id="about" class="about">
    <h2>About PapersHub</h2>
    <p>
      <strong>PapersHub</strong> is a one-stop digital solution for accessing and managing previous year question papers.  
      Built for students, by students — this platform simplifies exam preparation, helping you find exactly what you need with just a few clicks.
    </p>

    <ul>
      <li>📘 Search papers by branch, semester, and year</li>
      <li>📥 Instant PDF downloads and previews</li>
      <li>👩‍💻 Secure login for both students and admins</li>
      <li>🌍 Access anytime, anywhere — completely free</li>
    </ul>
  </section>

  <footer>
    © <?php echo date("Y"); ?> PapersHub | Developed by Sharwari Rahangdale & Yatharth Rahangdale
  </footer>
</body>
</html>
