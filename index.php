<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Society Management Software</title>
      <link rel="icon" type="image/png" href="home.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f8f9fa, #e3f2fd);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .hero {
      padding: 100px 20px;
      text-align: center;
      background: url('https://images.unsplash.com/photo-1568605114967-8130f3a36994') center/cover no-repeat;
      color: white;
      position: relative;
    }
    .hero::after {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .feature-icon {
      font-size: 40px;
      color: #0d6efd;
      transition: transform 0.3s ease;
    }
    .feature-icon:hover {
      transform: scale(1.2);
      color: #6610f2;
    }
    .btn-primary {
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #6610f2;
      border-color: #6610f2;
    }
    .section-title {
      font-size: 2rem;
      margin-bottom: 30px;
      font-weight: bold;
    }
    .feature-card {
      transition: transform 0.3s;
    }
    .feature-card:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    footer a {
      transition: color 0.3s;
    }
    footer a:hover {
      color: #0d6efd !important;
    }
  </style>
</head>
<body>

  <!-- Hero -->
  <header class="hero">
    <div class="container hero-content" data-aos="zoom-in">
      <h1 class="display-4 fw-bold">Welcome to Your Society Management Software</h1>
      <p class="lead">Manage everything from maintenance to member services in one platform.</p>
      <a href="Login.php" class="btn btn-primary btn-lg mt-3"><i class="bi bi-box-arrow-in-right me-2"></i>Get Started</a>
    </div>
  </header>

  <!-- Features -->
  <section class="py-5">
    <div class="container text-center">
      <h2 class="section-title" data-aos="fade-up">Features</h2>
      <div class="row g-4">
        <!-- Feature Items -->
        <!-- Each Column -->
        <!-- Use this format for all -->
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="100">
          <i class="bi bi-house-door-fill feature-icon"></i>
          <h5 class="mt-3">House Details</h5>
          <p>Manage house registrations, ownership and member information.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="200">
          <i class="bi bi-currency-rupee feature-icon"></i>
          <h5 class="mt-3">Maintenance Management</h5>
          <p>Track and collect maintenance, generate receipts, and view reports.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="300">
          <i class="bi bi-bell-fill feature-icon"></i>
          <h5 class="mt-3">Notifications & Complaints</h5>
          <p>Send updates and allow members to raise concerns easily.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="400">
          <i class="bi bi-calendar3 feature-icon"></i>
          <h5 class="mt-3">Yearly Reports</h5>
          <p>Generate and view annual financial reports.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="500">
          <i class="bi bi-people-fill feature-icon"></i>
          <h5 class="mt-3">Member Management</h5>
          <p>Manage residents, payments, and communication.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="600">
          <i class="bi bi-shield-lock-fill feature-icon"></i>
          <h5 class="mt-3">Secure & Reliable</h5>
          <p>Robust architecture with PHP & MySQL.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="700">
          <i class="bi bi-chat-dots-fill feature-icon"></i>
          <h5 class="mt-3">Community Engagement</h5>
          <p>Support for events, polls, and discussions.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="800">
          <i class="bi bi-gear-fill feature-icon"></i>
          <h5 class="mt-3">Customizable</h5>
          <p>Adapt the system to your society's needs.</p>
        </div>
        <div class="col-md-4 feature-card" data-aos="fade-up" data-aos-delay="900">
          <i class="bi bi-graph-up feature-icon"></i>
          <h5 class="mt-3">Analytics & Insights</h5>
          <p>Track finances and member activity.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="bg-light py-5">
    <div class="container">
      <h2 class="text-center section-title mb-4" data-aos="fade-down">Contact Us</h2>
      <form class="row g-3 needs-validation" novalidate data-aos="fade-up">
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Your Name" required>
          <div class="invalid-feedback">Name is required</div>
        </div>
        <div class="col-md-6">
          <input type="email" class="form-control" placeholder="Email" required>
          <div class="invalid-feedback">Email is required</div>
        </div>
        <div class="col-12">
          <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
          <div class="invalid-feedback">Message is required</div>
        </div>
        <div class="col-12 text-center">
          <button class="btn btn-primary px-4" windows.onclick ="alert('Thank you for your message! We will get back to you soon.');" type="submit">
            <i class="bi bi-send-fill me-2"></i>Send Message
        </div>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center py-4 bg-dark text-white" data-aos="fade-up">
    <p class="mb-2">&copy; 2025 Society Management System. Designed by Milan & Aliesh Web Solutions.</p>
    <div>
      <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
      <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
      <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
      <a href="mailto:support@societyapp.com" class="text-white"><i class="bi bi-envelope"></i></a>
    </div>
  </footer>

  <!-- Scroll to top and contact -->
  <a href="#" id="scrollTopBtn" class="btn btn-primary rounded-circle position-fixed" style="bottom: 80px; right: 20px; display: none;"><i class="bi bi-arrow-up"></i></a>
  <a href="mailto:sdsgroup28@gmail.com" class="btn btn-success rounded-circle position-fixed" style="bottom: 20px; right: 20px; z-index: 1000;">
    <i class="bi bi-chat-dots-fill"></i>
  </a>

  <script>
    // Bootstrap validation
    (() => {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', e => {
          if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Scroll to top
    const scrollBtn = document.getElementById("scrollTopBtn");
    window.onscroll = () => {
      scrollBtn.style.display = (document.documentElement.scrollTop > 200) ? "block" : "none";
    };
    scrollBtn.onclick = () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };
  </script>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true });
  </script>
</body>
</html>