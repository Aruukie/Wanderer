<?php
session_start();

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

// Only accept POST from booking.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: logdex.php#stay');
    exit;
}

// Get POST data from booking.php
$hotel      = trim($_POST['hotel'] ?? '');
$hotel_id   = trim($_POST['hotel_id'] ?? '');
$post_user  = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// Prefer session user id, but fall back to POST if needed
$session_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$user_id = $session_user > 0 ? $session_user : $post_user;

$guests     = (int)($_POST['guests'] ?? 1);
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date'] ?? '';

$errors = [];

// Basic validation
if ($hotel === '')      $errors[] = "Hotel is required.";
if ($hotel_id === '')   $errors[] = "Hotel ID is required.";
if ($guests < 1)        $errors[] = "Guests must be at least 1.";
if ($user_id <= 0)      $errors[] = "User is not logged in (no USER_ID).";

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

if ($errors) {
    echo "<h3>Booking error</h3><ul>";
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul><a href=\"javascript:history.back()\">Go back</a>";
    exit;
}

// Insert into BOOKINGS (no need to get BOOKING_ID yet)
$sql = "
    INSERT INTO BOOKINGS (
      HOTEL,
      HOTEL_ID,
      GUESTS_NO,
      DATE_START,
      DATE_END,
      USER_ID
    ) VALUES (
      ?, ?, ?, ?, ?, ?
    )
";

$params = [
    $hotel,
    $hotel_id,
    $guests,
    $startObj->format('Y-m-d'),
    $endObj->format('Y-m-d'),
    $user_id   // goes into USER_ID column
];

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die("Failed to save booking.<br>" . print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// For now, just go to transaction page without booking_id
header("Location: transaction.php");
exit;