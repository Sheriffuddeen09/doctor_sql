<?php
session_start();
include 'db.php';

// 🔁 STEP 1: Auto-generate slots when a date is selected
function generateRandomSlots($date, $doctor_id = 1) {
  global $pdo;

  // Check if 5 or more slots already exist for this doctor & date
  $check = $pdo->prepare("SELECT COUNT(*) FROM slots WHERE doctor_id = ? AND date = ?");
  $check->execute([$doctor_id, $date]);
  if ($check->fetchColumn() >= 5) {
    return; // Already generated
  }

  // Generate all 15-minute slots between 6:00 AM – 9:00 AM
  $start = strtotime("06:00");
  $end = strtotime("09:00");
  $allSlots = [];

  while ($start <= $end) {
    $allSlots[] = date("H:i:s", $start);
    $start += 15 * 60;
  }

  // Pick 5 random slots
  shuffle($allSlots);
  $randomSlots = array_slice($allSlots, 0, 5);

  // Insert into DB
  $stmt = $pdo->prepare("INSERT INTO slots (doctor_id, date, time, is_booked) VALUES (?, ?, ?, 0)");
  foreach ($randomSlots as $time) {
    $stmt->execute([$doctor_id, $date, $time]);
  }
}

// 🔁 STEP 2: Handle form submissions

// Fetch doctors
$result_doctors = $pdo->query("SELECT * FROM doctors");

// Fetch fees
$result_fees = $pdo->query("SELECT * FROM fees");

// Fetch clinics
$result_clinics = $pdo->query("SELECT * FROM clinics");

// Clinic description
$selectedClinicId = $_POST['clinic_name'] ?? null;

// Get default if not set
if (!$selectedClinicId) {
  $row = $pdo->query("SELECT id FROM clinics LIMIT 1")->fetch();
  $selectedClinicId = $row ? $row['id'] : null;
}

$selectedClinicRow = null;
if ($selectedClinicId) {
  $stmt = $pdo->prepare("SELECT * FROM clinics WHERE id = ?");
  $stmt->execute([$selectedClinicId]);
  $selectedClinicRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Booking a slot
// Booking a slot
$selected_slot_id = $_POST['slot_id'] ?? null;

// Don't mark booked here; just remember selection
$selected_slot_id = $_POST['slot_id'] ?? null;
$selected_date = $_POST['date'] ?? null;


// Show slots for selected date
$result_slots = null;
if (isset($_POST['date'])) {
  $selected_date = $_POST['date'];
  generateRandomSlots($selected_date); // ✅ Auto-create if not exists
  $stmt = $pdo->prepare("SELECT * FROM slots WHERE date = ? ORDER BY time ASC");
  $stmt->execute([$selected_date]);
  $result_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch distinct dates for dropdown
$result_dates = $pdo->query("SELECT DISTINCT date FROM slots ORDER BY date ASC");

if (isset($_POST['book_slot']) && isset($_POST['slot_id'])) {
  $_SESSION['pending_slot'] = $_POST['slot_id'];
  $_SESSION['pending_date'] = $_POST['date'];

  // Show email or OTP form
  if (!isset($_SESSION['logged_in'])) {
    if (!isset($_SESSION['email'])) {
      $show_email_form = true;
    } else {
      $show_otp_form = true;
    }
  } else {
    // User already verified — book it now
    $stmt = $pdo->prepare("UPDATE slots SET is_booked = 1 WHERE id = ?");
    $stmt->execute([$_POST['slot_id']]);
  }
}


// Step 1: Send OTP
if (isset($_POST['request_otp'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $stmt = $pdo->prepare("INSERT INTO users (email, otp, otp_expiry) VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE otp = ?, otp_expiry = ?");
    $stmt->execute([$email, $otp, $expiry, $otp, $expiry]);

    mail($email, "Your OTP", "Your OTP is: $otp", "From: no-reply@example.com");
    $_SESSION['email'] = $email;
    $show_otp_form = true;
}

// Step 2: Verify OTP
if (isset($_POST['verify_otp'])) {
  $email = $_SESSION['email'] ?? null;
  $entered_otp = $_POST['otp'] ?? '';

  $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $user['otp'] == $entered_otp && $user['otp_expiry'] > date("Y-m-d H:i:s")) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_email'] = $email;

    // ✅ Book slot now
    if (isset($_SESSION['pending_slot'])) {
      $stmt = $pdo->prepare("UPDATE slots SET is_booked = 1 WHERE id = ?");
      $stmt->execute([$_SESSION['pending_slot']]);
      unset($_SESSION['pending_slot'], $_SESSION['pending_date']);
    }
  } else {
    $error = "Invalid or expired OTP.";
    $show_otp_form = true;
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Doctors and Clinics</title>
  <style>
@media (min-width: 378px) {
    .doctor-card {
     display:inline-flex;
     gap: 10px;
    }
    .doctor-slot {
     display:inline-flex;
     gap: 10px;
     flex-wrap: nowrap;
     margin-top: 20px;
     align-items:center;
    }
    .doctor-slots {
     display:inline-flex;
     gap: 10px;
     flex-wrap: nowrap;
     margin-top: 20px;
     align-items:center;
    }
    .doctor-clinic {
     display:inline-flex;
     gap: 20px;
     flex-wrap: nowrap;
    }
    .fee-card {
     display:flex;
     flex-direction: row;
     margin-top: -25px;
     justify-content:space-between;
     align-items:center;
    }
    select{
      border-radius: 5px;
      padding: 5px 10px;
    }
    .select_text{
      color: black;
      font-size:12px;
      width: 500px;
    }
    .doctor-card .img {
      width: 50%; 
      height: 150px;
      object-fit: cover; 
      border-radius: 10px 10px 0 0;
    }
    .fee-card .img {
      width: 70px; 
      height: 100px;
      object-fit: cover; 
      border-radius: 10px 10px 0 0;
    }
    .text_h1{
       font-size: 15px;
       margin-top: 15px;
      font-family: sans-serif;
    }
    .text{
      font-size: 12px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      
    }
    .text_p{
      font-size: 12px;
    font-family: sans-serif;
      
    }
    .button{
      background-color: rgb(255, 128, 0);
      color: white;
      font-weight: bold;
      font-size: 11px;
      width: 120px;
      padding: 10px;
      border: none;
      border-radius: 10px;
    }
    .date-select {
      padding: 6px 10px; 
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid gray;
      width: 160px;
    }
    .slot-container {
      display: flex; 
      flex-direction: row; 
      justify-content: center;
      gap: 10px;
    }
   
    .booked {
      padding: 10px 20px; font-size: 16px;
      background-color: green; color: #666;
      cursor: not-allowed; border: none; border-radius: 5px;
    }
   .line{
    width: 100%;
    height: 0.5px;
    background-color: gray;
    content: '';
    display: block;
    margin: 20px auto;
   }
  }
  @media (min-width: 768px) {
    .doctor-card {
     display:inline-flex;
     gap: 40px;
     flex-wrap: nowrap;
    }
     .doctor-slot {
     display:inline-flex;
     gap: 80px;
     flex-wrap: nowrap;
     margin-top: 30px;
    }
     .doctor-slots {
     display:inline-flex;
     gap: 95px;
     flex-wrap: nowrap;
     margin-top: 30px;
    }
     .doctor-clinic {
     display:inline-flex;
     gap: 120px;
     flex-wrap: nowrap;
    }
    .fee-card {
     display:flex;
     flex-wrap: wrap;
     flex-direction: row;
     justify-content: space-between;
     align-items: center;
     margin-top: -35px;
    }
    select{
      border-radius: 5px;
      padding: 5px 10px;
    }
    .select_text{
      color: black;
      font-size:12px;
      line-height: 15px;
    }
    .doctor-card .img {
      width: 50%; 
      height: 150px;
      object-fit: cover; 
      border-radius: 10px 10px 0 0;
    }
    .fee-card .img {
      width: 70px; 
      height: 100px;
      object-fit: cover; 
      border-radius: 10px 10px 0 0;
    }
    .text_h1{
       font-size: 15px;
       margin-top: 15px;
      font-family: sans-serif;
    }
    .text{
      font-size: 12px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      
    }
    .text_p{
      font-size: 12px;
    font-family: sans-serif;
      
    }
    .button{
      background-color: rgb(255, 128, 0);
      color: white;
      font-weight: bold;
      font-size: 11px;
      width: 120px;
      padding: 10px;
      border: none;
      border-radius: 10px;
    }
    .date-select {
      padding: 6px 10px; 
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid gray;
      width: 160px;
    }
    .slot-container {
      display: flex; flex-wrap: wrap; gap: 10px;
    }
    .booked {
      padding: 10px 20px; font-size: 16px;
      background-color: #ccc; color: #666;
      cursor: not-allowed; border: none; border-radius: 5px;
    }
   .line{
    width: 100%;
    height: 0.5px;
    background-color: gray;
    content: '';
    display: block;
    margin: 20px auto;
   }
  } 
   body{
    padding: 20px 14px;
    overflow: hidden;
   }
   .fee{
     font-size: 11px;
     color: rgb(0, 119, 255);
    font-family: sans-serif;
   }
   .text_card{
     font-size: 13px;
     color: black;
    font-family: sans-serif;
   }
   .button_slot{
    background-color: transparent;
    border:none;
    display: inline-flex;
    gap: 2px;
    align-items: center;
   }
    .available {
      padding: 5px 30px; 
      border: 1px solid #4CAF50;
      cursor: pointer; 
      border-radius: 5px;
    }
    .button_book{
       background-color: transparent;
        border:none;
        display: inline-flex;
        gap: 2px;
        align-items: center;
    }
    .booked-green {
  display: inline-block;
  width: 70px;
  height: 11px;
  background-color: green; /* default state */
  border-radius: 5px;
}

.booked-gray {
  display: inline-block;
   width: 70px;
  height: 11px;
  background-color: gray; /* changes after booking */
  border-radius: 5px;
}

    .slot-button {
  padding: 5px 30px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin: 4px;
  font-size: 13px;
}

.slot-booked {
  background-color: gray;
  color: white;
  cursor: not-allowed;
}

.slot-selected {
  background-color: green;
  color: white;
  border: 1px solid green;
}

.slot-available {
  background-color: white;
  color: #4CAF50;
  border: 2px solid #4CAF50;
}

.input{
  padding: 6px;
  width: 250px;
  border: 1px solid green;
  border-radius: 5px;
}
.verify{
  background-color: rgb(0, 255, 72);
      color: white;
      font-weight: bold;
      font-size: 11px;
      width: 80px;
      padding: 8px;
      border: none;
      border-radius: 10px;
}
.otp-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.otp-popup {
  background-color: white;
  padding: 30px 20px;
  border-radius: 10px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  position: relative;
}

.cancel-btn {
  margin-top: 10px;
  background: #ccc;
  border: none;
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 5px;
}

.cancel-btn:hover {
  background-color: #999;
}
.book_time{
  margin-top: 10px;
  background-color: rgb(61, 236, 61);
  color: white;
  font-weight: bold;
  border: none;
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 5px;

}

  </style>
</head>
<body>

  <?php while ($row = $result_doctors->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="doctor-card">
      <img class='img' src="images/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($row['name'] ?? 'Doctor') ?>">
     <div>
      <h2 class='text'><?= htmlspecialchars($row['name'] ?? 'No Name') ?></h2>
      <p class='text_p'><?= htmlspecialchars($row['description'] ?? 'No Description') ?></p>
      <button class='button'>View Profile</button>
    </div>
    </div>
  <?php endwhile; ?>
  <div class='line'> </div>
  <h1 class='text_h1'>Book Appointment</h1>
  <?php while ($row = $result_fees->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="fee-card">
     <div>
      <h2 class='fee'><?= htmlspecialchars($row['first_visit_fee'] ?? 'No Name') ?></h2>
      <h2 class='fee'><?= htmlspecialchars($row['follow_up_fee'] ?? 'No Description') ?></h2>
    </div>
      <img class='img' src="images/<?= htmlspecialchars('logo.png') ?>" alt="<?= htmlspecialchars('logo') ?>">
    </div>
  <?php endwhile; ?>
  <form method="post">
  <div class='doctor-clinic'>
    <h1 class='text'>Clinic Name</h1>
    <div>
    <select class='select' name="clinic_name" onchange="this.form.submit()">
      <?php
      $result_clinics = $pdo->query("SELECT * FROM clinics");
      $default_clinic_id = null;

      while ($row = $result_clinics->fetch(PDO::FETCH_ASSOC)):
        if (!$default_clinic_id) {
          $default_clinic_id = $row['id']; // Store first one as default
        }
        $selectedClinicId = $_POST['clinic_name'] ?? $default_clinic_id;
        $selected = ($selectedClinicId == $row['id']) ? 'selected' : '';
      ?>
        <option value="<?= $row['id'] ?>" <?= $selected ?>>
          <?= htmlspecialchars($row['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
     <?php if ($selectedClinicRow): ?>
  <p class='select_text'><?= htmlspecialchars($selectedClinicRow['name']) ?><?= htmlspecialchars($selectedClinicRow['address']) ?></p>
<?php endif; ?>

  </div>
  </div>
</form>
  <!-- name & address -->
  

<form method="post">
  <div class='doctor-slot'>
    <h1 class='text_card'>Appointment Slot</h1>
    <input type="date" name="date" class="date-select" required>
     </div>
  <br/>
 <?php
$slotBookedConfirmed = false;

// If a user is logged in and a slot was booked
if (isset($_SESSION['logged_in']) && isset($_SESSION['just_booked'])) {
  $slotBookedConfirmed = true;
  unset($_SESSION['just_booked']); // Clear flag after showing once
}
?>
  <div class='doctor-slots'>
    <h1 class='text_card'>Available Slot</h1>
    <div>
    <button class='button_slot' type="submit"><span class='available'></span> Available</button>
    <!-- booked slot -->
   <button class='button_book' type="button">
      <span class='<?= $slotBookedConfirmed ? 'booked-gray' : 'booked-green' ?>'></span> Booked
   </button>
    </div>
  </div>
  </form>

  <?php if ($result_slots): ?>
  <div class="slot-container">
    <?php foreach ($result_slots as $slot): 
      $is_booked = $slot['is_booked'];
      $classes = 'slot-button ';
      $classes .= $is_booked ? 'slot-booked' : 'slot-available';
    ?>
      <form method="post" style="display:inline;">
        <input type="hidden" name="date" value="<?= htmlspecialchars($selected_date) ?>">
        <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>" >
        <button type="submit" name="book_slot" class='book_time'>
          <?= htmlspecialchars($slot['time']) ?>
        </button>
      </form>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php if (isset($show_email_form) && $show_email_form): ?>
  <div class="otp-modal">
    <div class="otp-popup">
      <h3>Step 1: Enter Email to Confirm Booking</h3>
      <form method="post">
        <input type="email" name="email" required>
        <button type="submit" name="request_otp">Send OTP</button>
      </form>
    </div>
  </div>

<?php elseif (isset($show_otp_form) && $show_otp_form): ?>
  <div class="otp-modal" id="otpModal">
    <div class="otp-popup">
      <h3 class='text'>Enter OTP sent to <?= htmlspecialchars($_SESSION['email']) ?></h3>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form method="post">
        <input class='input' type="text" name="otp" required>
        <button class='verify' type="submit" name="verify_otp">Verify OTP</button>
        <button type="button" class="cancel-btn" onclick="closeOtpModal()">Cancel</button>
      </form>
    </div>
  </div>
<?php endif; ?>



</body>
</html>
<script>
  function closeOtpModal() {
    const modal = document.getElementById("otpModal");
    if (modal) {
      modal.style.display = "none";
    }
  }
</script>
