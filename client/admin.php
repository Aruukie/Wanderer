<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wanderer Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
    }

    .navbar {
      background: rgba(29, 29, 29, 0.95);
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }

    .navbar-brand span {
      color: var(--gold);
      font-weight: 700;
      letter-spacing: 0.12em;
      font-size: 1.2rem;
      text-transform: uppercase;
    }

    .nav-link {
      color: #ffffff !important;
      font-size: 0.8rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
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
    }

    .btn-gold:hover {
      filter: brightness(1.15);
      color: #000;
    }

    .page-header {
      padding-top: 80px;
      padding-bottom: 24px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.15);
      background: linear-gradient(135deg, rgba(22,22,22,0.96), rgba(142,116,30,0.3));
    }

    .page-header h1 {
      font-size: 1.8rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--gold);
    }

    .page-header p {
      color: var(--text-muted);
      margin-bottom: 0;
    }

    .admin-wrapper {
      padding: 2.5rem 0 3rem;
    }

    .card-dark {
      background: linear-gradient(135deg, rgba(20,20,20,0.95), rgba(30,30,30,0.95));
      border-radius: 12px;
      border: 1px solid rgba(212, 175, 55, 0.25);
    }

    .card-dark .card-header {
      border-bottom-color: rgba(212, 175, 55, 0.15);
    }

    .card-dark .card-title {
      font-size: 0.9rem;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--gold-soft);
      margin-bottom: 0;
    }

    .table-dark {
      --bs-table-bg: transparent;
      --bs-table-striped-bg: rgba(255,255,255,0.03);
      --bs-table-hover-bg: rgba(212,175,55,0.08);
      color: #fff;
    }

    .table thead th {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      border-bottom-color: rgba(212, 175, 55, 0.25);
    }

    .badge-pill {
      border-radius: 999px;
      padding-inline: 0.7rem;
    }

    footer {
      border-top: 1px solid rgba(212, 175, 55, 0.15);
      background: #000;
    }

    footer p {
      color: var(--text-muted);
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="index.php">
        <span>Wanderer</span>
      </a>
      <div class="d-flex gap-3 align-items-center">
        <a href="index.php" class="nav-link">Site</a>
        <a href="admin.php" class="nav-link active">Admin</a>
        <a href="login.php" class="btn btn-gold btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- HEADER -->
  <header class="page-header">
    <div class="container">
      <h1>Admin Dashboard</h1>
      <p>Overview of users and bookings.</p>
    </div>
  </header>

  <!-- MAIN -->
  <main class="admin-wrapper">
    <div class="container">
      <div class="row g-4">
        <!-- USERS -->
        <div class="col-12">
          <div class="card card-dark">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h2 class="card-title">Users</h2>
              <span class="badge bg-secondary badge-pill">
                <?php echo count($users); ?> total
              </span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                  <thead>
                    <tr>
                      <th scope="col">ID</th>
                      <th scope="col">Name</th>
                      <th scope="col">Email</th>
                      <th scope="col">Created</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($users)): ?>
                    <tr>
                      <td colspan="4" class="text-center py-3 text-muted">No users found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($users as $u): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($u['USER_ID']); ?></td>
                        <td><?php echo htmlspecialchars($u['FIRST_NAME'] . ' ' . $u['LAST_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($u['EMAIL']); ?></td>
                        <td>
                          <?php
                            if (!empty($u['CREATED_AT'])) {
                              echo htmlspecialchars($u['CREATED_AT']->format('Y-m-d H:i'));
                            } else {
                              echo '-';
                            }
                          ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- BOOKINGS -->
        <div class="col-12">
          <div class="card card-dark">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h2 class="card-title">Bookings</h2>
              <span class="badge bg-secondary badge-pill">
                <?php echo count($bookings); ?> total
              </span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                  <thead>
                    <tr>
                      <th scope="col">Booking ID</th>
                      <th scope="col">Hotel</th>
                      <th scope="col">Hotel ID</th>
                      <th scope="col">Guest no.</th>
                      <th scope="col">User ID</th>
                      <th scope="col">Created</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($bookings)): ?>
                    <tr>
                      <td colspan="6" class="text-center py-3 text-muted">No bookings yet.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($b['BOOKING_ID']); ?></td>
                        <td><?php echo htmlspecialchars($b['HOTEL']); ?></td>
                        <td><?php echo htmlspecialchars($b['HOTEL_ID']); ?></td>
                        <td><?php echo htmlspecialchars($b['GUEST_NO']); ?></td>
                        <td><?php echo htmlspecialchars($b['USER_ID']); ?></td>
                        <td>
                          <?php
                            if (!empty($b['CREATED_AT'])) {
                              echo htmlspecialchars($b['CREATED_AT']->format('Y-m-d H:i'));
                            } else {
                              echo '-';
                            }
                          ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="py-3">
    <div class="container text-center">
      <p class="small mb-0">&copy; 2025 Wanderer. Admin.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>