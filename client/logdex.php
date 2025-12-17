<?php
$serverName="localhost\\SQLEXPRESS"; 
$connectionOptions=[ 
"Database"=>"travelwebsite", 
"Uid"=>"", 
"PWD"=>"" 
]; 

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("Database connection failed.");
}

$first_name = 'Guest';

if (isset($_SESSION['user_id'])) {
    $sql = "SELECT FIRST_NAME FROM USERS WHERE USER_ID = ?";
    $params = [ (int)$_SESSION['user_id'] ];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt !== false && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
        if (!empty($row['FIRST_NAME'])) {
            $first_name = $row['FIRST_NAME'];
        }
    }

    if ($stmt !== false) {
        sqlsrv_free_stmt($stmt);
    }
}
?>


?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wanderer - Travel Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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

    /* Navbar */
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

    /* Hero Section */
    .hero {
      background: linear-gradient(135deg, rgba(22, 22, 22, 0.907), rgba(142, 116, 30, 0.359)), url('https://images.pexels.com/photos/1682519/pexels-photo-1682519.jpeg');
      background-size: cover;
      background-position: center;
      min-height: 600px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      margin-top: 70px;
    }

    .hero h1 {
      font-size: clamp(2.5rem, 5vw, 4rem);
      font-weight: 700;
      letter-spacing: .4em;
      text-transform: uppercase;
      margin-bottom: 0.5rem;
      color: var(--gold);
      font-weight: bold;
    }

    .hero p {
      font-size: 1.5rem;
      color: var(--gold-soft);
      letter-spacing: 0.04em;
      margin-bottom: 2rem;
      font-weight: bolder;
    }

    .search-form-wrapper {
      padding: 2.5rem;
      border-radius: 20px;
      max-width: 600px;
      margin: 0 auto;
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

    .input-group-text {
      background: transparent !important;
      border: none !important;
      color: var(--gold);
    }

    /* Sections */
    section {
      padding: 5rem 0;
      border-bottom: 1px solid rgba(212, 175, 55, 0.1);
    }

    section h2 {
      font-size: 2.5rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      margin-bottom: 0.5rem;
      color: var(--gold);
    }

    section > .container > p {
      color: var(--text-muted);
      font-size: 1.1rem;
      margin-bottom: 3rem;
    }

    /* Hotel Cards */
    .hotel-card {
      background: linear-gradient(135deg, rgba(20, 20, 20, 0.8), rgba(30, 30, 30, 0.8));
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 8px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .hotel-card:hover {
      transform: translateY(-10px);
      border-color: var(--gold);
      box-shadow: 0 20px 40px rgba(212, 175, 55, 0.15);
    }

    .hotel-card img {
      height: 200px;
      object-fit: cover;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }

    .hotel-card .card-body {
      padding: 1.5rem;
    }

    .hotel-card .card-title {
      color: var(--gold);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .hotel-card .card-text {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    .price-new {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--gold);
    }

    .rating-badge {
      background: rgba(212, 175, 55, 0.2);
      border: 1px solid var(--gold);
      color: var(--gold);
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.85rem;
    }

    /* Explore Section */
    .explore-card {
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.85), rgba(212, 175, 55, 0.1));
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 8px;
      overflow: hidden;
    }

    .explore-image {
      min-height: 400px;
      background-size: cover;
      background-position: center;
      position: relative;
    }

    .explore-content {
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 400px;
    }

    .explore-content h3 {
      color: var(--gold);
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .explore-content p {
      color: var(--text-muted);
      margin-bottom: 1.5rem;
    }

    /* Footer */
    footer {
      background: linear-gradient(180deg, rgba(0, 0, 0, 0.9), #000);
      border-top: 1px solid rgba(212, 175, 55, 0.1);
    }

    footer h5 {
      color: var(--gold);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 1.5rem;
    }

    footer a {
      color: var(--text-muted);
      transition: color 0.3s ease;
    }

    footer a:hover {
      color: var(--gold);
    }

    /* Alerts */
    .alert {
      background: rgba(212, 175, 55, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.3);
      color: var(--gold-soft);
    }

    .alert-danger {
      background: rgba(220, 53, 69, 0.1);
      border-color: rgba(220, 53, 69, 0.3);
      color: #ff6b6b;
    }

    /* Loading */
    .spinner-border {
      border-color: rgba(212, 175, 55, 0.2);
      border-right-color: var(--gold);
    }
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="index.html">
        <span>Wanderer</span>
      </a>
      
      <div class="d-flex gap-4 align-items-center">
        <p>Welcome, <?php echo htmlspecialchars($first_name); ?>!</p>
        <a href="#stay" class="nav-link">Wander</a>
        <a href="booking.php" class="nav-link">Booking</a>
        <a href="index.html" class="btn btn-gold btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION -->
  <section id="hero" class="hero">
    <div class="search-form-wrapper">
      <h1>Wanderer</h1>
      <p>Adventure Awaits</p>
      
      <form id="searchForm">
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" class="form-control" id="searchCity" placeholder="Search a city (e.g., manila, cebu)" value="manila" required>
          <button type="submit" class="btn btn-gold">Search</button>
        </div>
      </form>
    </div>
  </section>

  <!-- HOTELS SECTION -->
  <section id="stay">
    <div class="container">
      <h2>Stay in <span id="cityName">Manila</span></h2>
      <p>Find the perfect accommodation for your next adventure</p>
      
      <div class="row" id="stayRowMain">
        <div class="col-12 text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 text-muted">Loading hotels...</p>
        </div>
      </div>

      <div class="collapse" id="stayMore">
        <div class="row mt-4" id="stayRowMore"></div>
      </div>

      <div class="text-center mt-4" id="showMoreContainer" style="display: none;">
        <button class="btn btn-outline-gold" type="button" data-bs-toggle="collapse" data-bs-target="#stayMore">
          <i class="bi bi-chevron-down"></i> Show More Hotels
        </button>
      </div>
    </div>
  </section>

  <!-- EXPLORE SECTION -->
  <section id="explore">
    <div class="container">
      <div class="explore-card">
        <div class="row g-0">
          <div class="col-lg-6">
            <div class="explore-image" id="exploreImage"></div>
          </div>
          <div class="col-lg-6">
            <div class="explore-content" id="exploreContent">
              <h3>Featured</h3>
              <p>Search to discover places and landmarks</p>
              <button class="btn btn-gold align-self-start">Learn More</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="container py-5">
      <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
          <h5>Wanderer</h5>
          <p class="text-muted">Discover your next adventure. Travel here, there, and everywhere.</p>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="#stay">Hotels</a></li>
            <li><a href="#explore">Explore</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Sign Up</a></li>
          </ul>
        </div>
        <div class="col-md-4">
          <h5>Follow Us</h5>
          <div class="d-flex gap-3">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
      <hr class="my-4" style="border-color: rgba(212, 175, 55, 0.1);">
      <div class="text-center">
        <p class="text-muted small">&copy; 2025 Wanderer. By Julianne Godfrey Babilonia.</p>
      </div>
    </div>
  </footer>

 <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:#151515; color:#fff; border:1px solid rgba(212,175,55,0.4);">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="bookingModalLabel">Confirm booking</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="create_booking.php">
        <div class="modal-body">
          <!-- Hidden fields posted to PHP -->
          <input type="hidden" name="hotel" id="bookingHotel">
          <input type="hidden" name="hotel_id" id="bookingHotelId">
          <input type="hidden" name="user_id"
                 value="<?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>">

          <div class="mb-3">
            <label class="form-label text-uppercase small">Hotel</label>
            <input type="text" class="form-control" id="bookingHotelDisplay" disabled>
          </div>

          <div class="mb-3">
            <label for="guest_no" class="form-label text-uppercase small">Number of guests</label>
            <input type="number" class="form-control" id="guest_no" name="guest_no" min="1" value="2" required>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-gold btn-sm">Book now</button>
        </div>
      </form>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script src="app.js"></script>
</body>
</html>
