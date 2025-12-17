<?php
session_start();

// Data from the hotel listing page (via GET)
$hotel     = isset($_GET['hotel']) ? urldecode($_GET['hotel']) : '';
$hotel_id  = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : '';
$hotel_img = isset($_GET['image']) ? urldecode($_GET['image']) : '';
$user_id   = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking - Wanderer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --bg-dark: #181818;
      --bg-darker: #151515;
      --gold: #d4af37;
      --gold-soft: #f3d78c;
      --text-muted: #bbbbbb;
    }
    * {
      scroll-behavior: smooth;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    body {
      background-color: var(--bg-dark);
      color: #ffffff;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    .navbar {
      background: rgba(29, 29, 29, 0.85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(212, 175, 55, 0.1);
    }
    .navbar-brand span {
      color: var(--gold);
      font-weight: 700;
      letter-spacing: 0.08em;
      font-size: 1.3rem;
      text-transform: uppercase;
    }
    .nav-link {
      color: #ffffff !important;
      font-size: 0.85rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      transition: color 0.3s ease;
    }
    .nav-link:hover,
    .nav-link.active {
      color: var(--gold) !important;
    }
    .btn-gold {
      background: linear-gradient(135deg, var(--gold), var(--gold-soft));
      color: #1B1B1B;
      border: none;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      font-size: 0.75rem;
      transition: filter 0.3s ease;
    }
    .btn-gold:hover {
      filter: brightness(1.15);
      color: #000;
    }
    section {
      padding: 5rem 0;
      border-bottom: 1px solid rgba(212, 175, 55, 0.1);
    }
    .booking-card {
      background: linear-gradient(135deg, rgba(20, 20, 20, 0.9), rgba(30, 30, 30, 0.9));
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 12px;
    }
    .form-control {
      background: rgba(205, 205, 205, 0.3);
      border: 1px solid rgba(255, 240, 29, 0.596);
      color: #fff;
    }
    .form-control::placeholder {
      color: var(--text-muted);
    }
    .form-control:focus {
      background: rgba(255, 255, 255, 0.08);
      border-color: var(--gold);
      color: #fff;
      box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
  </style>
</head>
<body class="bg-dark text-light">

<!-- Optional navbar (simplified to match the rest of the site) -->
<nav class="navbar fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="logdex.php">
      <span>Wanderer</span>
    </a>
    <div class="d-flex gap-4 align-items-center">
      <a href="logdex.php#stay" class="nav-link">Stay</a>
      <a href="logdex.php#explore" class="nav-link">Explore</a>
      <a href="login.php" class="btn btn-gold btn-sm">Login</a>
    </div>
  </div>
</nav>

<section style="margin-top: 80px;">
  <div class="container">
    <div class="row g-4 align-items-start">
      <!-- Left: hotel summary -->
      <div class="col-md-5">
        <?php if ($hotel_img): ?>
          <img src="<?php echo htmlspecialchars($hotel_img); ?>"
               class="img-fluid rounded mb-3"
               alt="<?php echo htmlspecialchars($hotel); ?>">
        <?php endif; ?>
        <h2 class="mb-1" style="color: var(--gold);">
          <?php echo htmlspecialchars($hotel ?: 'Hotel'); ?>
        </h2>
        <?php if ($hotel_id !== ''): ?>
          <p class="text-muted mb-0">
            Hotel ID: <?php echo htmlspecialchars($hotel_id); ?>
          </p>
        <?php endif; ?>
      </div>

      <!-- Right: booking form -->
      <div class="col-md-7">
        <div class="booking-card p-4">
          <h3 class="mb-4" style="color: var(--gold);">Booking details</h3>

          <!-- POST to create_booking.php, which inserts into BOOKINGS then redirects -->
          <form method="POST" action="create_booking.php">
            <!-- Hidden fields -->
            <input type="hidden" name="hotel" value="<?php echo htmlspecialchars($hotel); ?>">
            <input type="hidden" name="hotel_id" value="<?php echo htmlspecialchars($hotel_id); ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="mb-3">
              <label for="guests" class="form-label text-uppercase small">Number of guests</label>
              <input type="number" id="guests" name="guests" class="form-control" min="1" value="2" required>
            </div>

            <div class="mb-3">
              <label for="start_date" class="form-label text-uppercase small">Check-in date</label>
              <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="end_date" class="form-label text-uppercase small">Check-out date</label>
              <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="logdex.php#stay" class="btn btn-outline-secondary btn-sm">Back</a>
              <button type="submit" class="btn btn-gold btn-sm">Continue</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
