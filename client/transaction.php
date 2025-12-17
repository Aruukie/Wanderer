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

$errors  = [];
$success = false;
$bookingId = null;
$transId   = null;

// Handle POST from booking/payment form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // From booking.php
    $hotel      = trim($_POST['hotel'] ?? '');
    $hotel_id   = trim($_POST['hotel_id'] ?? '');
    $user_id    = (int)($_POST['user_id'] ?? 0);
    $guests     = (int)($_POST['guests'] ?? 1);
    $start_date = $_POST['start_date'] ?? '';
    $end_date   = $_POST['end_date'] ?? '';

    // Payment fields (you can change this to your real form)
    $amount     = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.00;
    $card_no    = trim($_POST['card_no'] ?? '');

    // --- Validation ---

    if ($hotel === '') {
        $errors[] = "Hotel is required.";
    }
    if ($hotel_id === '') {
        $errors[] = "Hotel ID is required.";
    }
    if ($guests < 1) {
        $errors[] = "Number of guests must be at least 1.";
    }

    $startObj = DateTime::createFromFormat('Y-m-d', $start_date);
    $endObj   = DateTime::createFromFormat('Y-m-d', $end_date);

    if (!$startObj || $startObj->format('Y-m-d') !== $start_date) {
        $errors[] = "Invalid start date.";
    }
    if (!$endObj || $endObj->format('Y-m-d') !== $end_date) {
        $errors[] = "Invalid end date.";
    }
    if (!$errors && $endObj <= $startObj) {
        $errors[] = "End date must be after start date.";
    }

    if ($amount <= 0) {
        $errors[] = "Amount must be greater than 0.";
    }

    if (!$errors) {
        // --- 1) Insert into BOOKINGS and get BOOKING_ID ---
        $sqlBooking = "
            INSERT INTO BOOKINGS (
              HOTEL,
              HOTEL_ID,
              GUESTS_NO,
              DATE_START,
              DATE_END,
              USER_ID
            ) VALUES (
              ?, ?, ?, ?, ?, ?
            );
            SELECT SCOPE_IDENTITY() AS NEW_BOOKING_ID;
        ";

        $bookingParams = [
            $hotel,
            $hotel_id,
            $guests,
            $startObj->format('Y-m-d'),
            $endObj->format('Y-m-d'),
            $user_id
        ];

        $stmtBooking = sqlsrv_query($conn, $sqlBooking, $bookingParams);
        if ($stmtBooking === false) {
            $errors[] = "Failed to save booking.";
        } else {
            $row = sqlsrv_fetch_array($stmtBooking, SQLSRV_FETCH_ASSOC);
            if (!$row || !isset($row['NEW_BOOKING_ID'])) {
                $errors[] = "Could not retrieve booking ID.";
            } else {
                $bookingId = (int)$row['NEW_BOOKING_ID'];
            }
            sqlsrv_free_stmt($stmtBooking);
        }

        // --- 2) Insert into TRANSACTIONS using BOOKING_ID ---
        if (!$errors && $bookingId) {
            $sqlTrans = "
                INSERT INTO TRANSACTIONS (
                  AMOUNT,
                  CARD_NO,
                  BOOKING_ID,
                  USER_ID
                ) VALUES (
                  ?, ?, ?, ?
                );
                SELECT SCOPE_IDENTITY() AS NEW_TRANS_ID;
            ";

            $transParams = [
                $amount,
                $card_no === '' ? null : $card_no, // allow NULL if no card
                $bookingId,
                $user_id
            ];

            $stmtTrans = sqlsrv_query($conn, $sqlTrans, $transParams);
            if ($stmtTrans === false) {
                $errors[] = "Failed to save transaction.";
            } else {
                $row = sqlsrv_fetch_array($stmtTrans, SQLSRV_FETCH_ASSOC);
                if ($row && isset($row['NEW_TRANS_ID'])) {
                    $transId = (int)$row['NEW_TRANS_ID'];
                }
                sqlsrv_free_stmt($stmtTrans);
                $success = true;
            }
        }
    }
}

// Close connection (optional here, script is ending anyway)
sqlsrv_close($conn);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Wanderer - Transaction</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #181818;
      color: #ffffff;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    .btn-gold {
      background: linear-gradient(135deg, #d4af37, #f3d78c);
      color: #1b1b1b;
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
  </style>
</head>
<body>
<div class="container py-5" style="margin-top:40px;">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
        <div class="alert alert-warning">
          No transaction data received. Please start again from the booking page.
        </div>
        <a href="logdex.php#stay" class="btn btn-outline-secondary btn-sm mt-3">Back to Stay</a>

      <?php else: ?>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <h5 class="mb-2">There was a problem processing your booking:</h5>
            <ul class="mb-0">
              <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm mt-3">Go back</a>

        <?php elseif ($success): ?>
          <div class="alert alert-success">
            <h4 class="mb-2">Booking & Transaction saved!</h4>
            <?php if ($bookingId): ?>
              <p class="mb-1">Booking reference: <strong>#<?php echo htmlspecialchars($bookingId); ?></strong></p>
            <?php endif; ?>
            <?php if ($transId): ?>
              <p class="mb-1">Transaction ID: <strong>#<?php echo htmlspecialchars($transId); ?></strong></p>
            <?php endif; ?>
            <p class="mb-1">Hotel: <strong><?php echo htmlspecialchars($hotel); ?></strong></p>
            <p class="mb-1">Guests: <strong><?php echo (int)$guests; ?></strong></p>
            <p class="mb-1">Stay: <strong><?php echo htmlspecialchars($start_date); ?></strong> to <strong><?php echo htmlspecialchars($end_date); ?></strong></p>
            <p class="mb-1">Amount: <strong><?php echo number_format($amount, 2); ?></strong></p>
          </div>
          <a href="logdex.php#stay" class="btn btn-gold btn-sm mt-3">Back to Stay</a>

        <?php else: ?>
          <div class="alert alert-danger">
            Something went wrong while saving your booking and transaction.
          </div>
          <a href="logdex.php#stay" class="btn btn-outline-secondary btn-sm mt-3">Back to Stay</a>
        <?php endif; ?>

      <?php endif; ?>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
