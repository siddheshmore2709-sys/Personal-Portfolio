<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$recipient = "siddheshmore2709@gmail.com";
$name = trim(filter_var($_POST["Name"] ?? "", FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));
$email = filter_var(trim($_POST["Email"] ?? ""), FILTER_SANITIZE_EMAIL);
$message = trim(filter_var($_POST["Message"] ?? "", FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));
$errors = [];
$sent = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($name === "") $errors[] = "Please enter your name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
    if ($message === "") $errors[] = "Please enter a message.";

    if (!$errors) {
        $subject = "Portfolio contact message from " . $name;
        $body = "Name: " . $name . "\nEmail: " . $email . "\n\nMessage:\n" . $message;
        $headers = "From: " . $email . "\r\nReply-To: " . $email . "\r\n";

      if (!function_exists('mail')) {
        $errors[] = "Mail sending is not available on this server. Please contact me directly at siddheshmore2709@gmail.com.";
      } else {
        $sent = @mail($recipient, $subject, $body, $headers);

        if (!$sent) {
          $errors[] = "Mail is not configured on this local server. Please contact me directly at siddheshmore2709@gmail.com or configure SMTP on the hosting server.";
        } else {
          $name = $email = $message = "";
        }
      }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Portfolio of Siddhesh More - Diploma IT Student and aspiring Full-Stack Web Developer." />
    <title>Siddhesh | Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <script src="main.js" defer></script>
    <link rel="stylesheet" href="my.css" />
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-glass shadow-lg">
      <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.html">Siddhesh.</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
            <li class="nav-item"><a class="nav-link" href="skills.html">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="resume.html">Resume</a></li>
            <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
          </ul>
          <button class="theme-toggle ms-lg-3 mt-3 mt-lg-0" type="button" data-theme-toggle aria-label="Switch to light theme" title="Switch to light theme" aria-pressed="false">☀</button>
        </div>
      </div>
    </nav>

    <main>
      <section class="animate-in hero hero-sm d-flex align-items-center">
        <div class="container text-center text-md-start">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <p class="eyebrow">Get in touch</p>
              <h1 class="hero-title">Let's connect</h1>
              <p class="hero-text mb-4">Contact me about my work, learning journey, or web projects.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="animate-in container py-5">
        <div class="row g-4">
          <div class="col-lg-5">
            <div class="contact-card p-4 rounded-4 shadow-sm h-100">
              <h2>Contact details</h2>
              <p>You can reach me by email, phone, or through my professional profiles.</p>
              <div class="mb-3"><h3>Email</h3><p><a href="mailto:siddheshmore2709@gmail.com">siddheshmore2709@gmail.com</a></p></div>
              <div><h3>Phone</h3><p><a href="tel:+919309497077">9309497077</a></p></div>
              <div class="mb-3"><h3>Location</h3><p>Kolhapur, Maharashtra, India</p></div>
              <div><h3>Profiles</h3><p><a href="https://github.com/siddheshmore2709-sys" target="_blank" rel="noreferrer">GitHub</a><br /><a href="https://www.linkedin.com/in/siddhesh-more-91549839a" target="_blank" rel="noreferrer">LinkedIn</a></p></div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="feature p-4 rounded-4 shadow-sm h-100">
              <h2>Send a message</h2>
              <?php if ($sent): ?><div class="alert alert-success" role="status">Thank you for reaching out. Your message was sent.</div><?php endif; ?>
              <?php if ($errors): ?><div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li><?php endforeach; ?></ul></div><?php endif; ?>
              <form action="contact.php" method="post">
                <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" type="text" id="name" name="Name" value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>" placeholder="Your name" required /></div>
                <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="Email" value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>" placeholder="you@example.com" required /></div>
                <div class="mb-3"><label class="form-label" for="message">Message</label><textarea class="form-control" id="message" name="Message" rows="5" placeholder="Tell me about your project" required><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></textarea></div>
                <button class="btn btn-primary btn-lg" type="submit">Send message</button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer py-4 bg-dark text-light"><div class="container d-flex flex-column flex-md-row justify-content-between align-items-center"><p class="mb-2 mb-md-0">&copy; Siddhesh Shivaji More.</p></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
