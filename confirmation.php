<?php
session_start();
include 'db.php';

// Auth Protection Gatekeeper
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Fallback protection: If they didn't arrive via a POST form submit, redirect safely
if (!isset($_POST['room']) || !isset($_POST['final_total'])) {
    header("Location: accommodation.php");
    exit();
}

// 1. Capture and sanitize incoming form payloads
$user_name = $_SESSION['user_name'];
$room_type = mysqli_real_escape_string($conn, $_POST['room']);
$checkin   = mysqli_real_escape_string($conn, $_POST['checkin']);
$checkout  = mysqli_real_escape_string($conn, $_POST['checkout']);
$final_total = mysqli_real_escape_string($conn, $_POST['final_total']);

// 2. Calculate underlying temporal variables for the database fields
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$interval = $date1->diff($date2);
$total_nights = $interval->days;
if ($total_nights <= 0) { $total_nights = 1; }

// Compute tax variable cleanly ($final_total is subtotal + 12% tax, so tax is final_total * 12/112)
$tax_amount = (float)$final_total * (12 / 112);

// Generate unique mock reference identifier string
$booking_reference = "TR-" . strtoupper(substr(md5(time()), 0, 8));


// =======================================================
// LIVE DATABASE INSERTION ENGINE
// =======================================================

// A. Insert Core Reservation into 'bookings' table (Using 'user_name' to perfectly match your SQL structure)
$query_booking = "INSERT INTO `bookings` (`user_name`, `room_type`, `checkin_date`, `checkout_date`, `total_nights`, `tax_amount`, `total_amount_paid`, `booking_reference`) 
                  VALUES ('$user_name', '$room_type', '$checkin', '$checkout', '$total_nights', '$tax_amount', '$final_total', '$booking_reference')";

if (mysqli_query($conn, $query_booking)) {
    // Grab the auto-incremented ID generated for this specific booking row
    $new_booking_id = mysqli_insert_id($conn);
    
    // B. Programmatic, reactive extra activities mapping loop
    $potential_activities = [
        'Heli-Coastal Exploration' => 450.00,
        'Deep Thermal Spa Loop'    => 220.00,
        'Deep Sea Marine Diving'   => 310.00
    ];

    // If your booking_payment.php passes chosen activities through array checkboxes (e.g., name="activities[]")
    if (isset($_POST['activities']) && is_array($_POST['activities'])) {
        foreach ($_POST['activities'] as $selected_activity_name) {
            if (array_key_exists($selected_activity_name, $potential_activities)) {
                $activity_price = $potential_activities[$selected_activity_name];
                $activity_clean = mysqli_real_escape_string($conn, $selected_activity_name);
                
                $query_activity = "INSERT INTO `booking_activities` (`booking_id`, `activity_name`, `activity_price`) 
                                   VALUES ('$new_booking_id', '$activity_clean', '$activity_price')";
                mysqli_query($conn, $query_activity); // FIXED: Added execution statement line
            }
        }
    }
    
} else {
    die("Database Transaction Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmed | The Rock</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pine-green: #0F1E19;
            --forest-green: #1C332A;
            --moss-green: #7D8F82;
            --gold-accent: #C5A880;
            --ivory: #FAF9F5;
            --ivory-dark: #F2EFE9;
            --transition-smooth: all 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background-color: var(--ivory); color: var(--pine-green); font-weight: 300; min-height: 100vh; display: flex; flex-direction: column; }

        /* --- STICKY NAV --- */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 30px 6%;
            background: rgba(15, 30, 25, 0.95); position: fixed; width: 100%; top: 0; z-index: 100;
            backdrop-filter: blur(8px); border-bottom: 1px solid rgba(250, 249, 245, 0.05);
        }
        .nav-logo { font-family: 'Cinzel', serif; font-size: 1.6rem; letter-spacing: 4px; color: var(--ivory); text-decoration: none; }

        /* --- CONFIRMATION CARD CONTAINER --- */
        .success-wrapper {
            max-width: 650px; margin: 160px auto 80px auto; padding: 0 4%; flex: 1; width: 100%;
        }

        .success-card {
            background: white; border: 1px solid rgba(15,30,25,0.06); padding: 50px;
            box-shadow: 0 30px 60px rgba(15,30,25,0.04); text-align: center;
        }

        .seal-icon {
            font-size: 2.5rem; color: var(--gold-accent); margin-bottom: 25px; font-weight: 300;
        }

        .success-card h1 { font-family: 'Cinzel', serif; font-size: 2rem; font-weight: 400; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 15px; color: var(--forest-green); }
        .success-card p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.1rem; color: var(--moss-green); margin-bottom: 40px; }

        /* Documented Manifest Details Grid */
        .receipt-manifest { text-align: left; background: var(--ivory); padding: 30px; border: 1px solid rgba(15,30,25,0.04); margin-bottom: 45px; }
        .manifest-title { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; color: var(--gold-accent); margin-bottom: 20px; border-bottom: 1px solid rgba(15,30,25,0.08); padding-bottom: 8px; font-weight: 600; }
        
        .manifest-row { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 0.85rem; }
        .manifest-lbl { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--moss-green); font-weight: 500; }
        .manifest-val { font-weight: 400; color: var(--pine-green); }

        .divider-dash { border-top: 1px dashed rgba(15,30,25,0.15); margin: 20px 0 15px 0; }
        
        .final-paid-row { display: flex; justify-content: space-between; align-items: center; padding-top: 5px; }
        .final-paid-row .paid-lbl { font-family: 'Cinzel', serif; font-size: 0.95rem; letter-spacing: 1px; font-weight: 600; color: var(--forest-green); text-transform: uppercase; }
        .final-paid-row .paid-val { font-size: 1.5rem; font-weight: 300; color: var(--pine-green); }

        /* Navigation Buttons */
        .return-home-btn {
            display: inline-block; width: 100%; background: var(--forest-green); color: var(--ivory);
            text-decoration: none; padding: 18px; font-size: 0.8rem; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase; transition: var(--transition-smooth); border: 1px solid var(--forest-green);
        }
        .return-home-btn:hover { background: transparent; color: var(--forest-green); }

        @media (max-width: 576px) {
            .success-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="index.php" class="nav-logo">The Rock</a>
    </nav>

    <main class="success-wrapper">
        <div class="success-card">
            <div class="seal-icon">✦</div>
            <h1>Securely Confirmed</h1>
            <p>Your physical sanctuary space has been successfully isolated.</p>

            <div class="receipt-manifest">
                <div class="manifest-title">Official Ledger Statement</div>
                
                <div class="manifest-row">
                    <span class="manifest-lbl">Booking Reference</span>
                    <span class="manifest-val" style="font-family: monospace; font-size: 0.95rem; letter-spacing: 0.5px; font-weight: bold;"><?php echo $booking_reference; ?></span>
                </div>
                <div class="manifest-row">
                    <span class="manifest-lbl">Guest Profile Name</span>
                    <span class="manifest-val"><?php echo htmlspecialchars($user_name); ?></span>
                </div>
                <div class="manifest-row">
                    <span class="manifest-lbl">Allocated Sanctuary</span>
                    <span class="manifest-val"><?php echo $room_type; ?></span>
                </div>
                <div class="manifest-row">
                    <span class="manifest-lbl">Arrival Check-In</span>
                    <span class="manifest-val"><?php echo date("M d, Y", strtotime($checkin)); ?></span>
                </div>
                <div class="manifest-row">
                    <span class="manifest-lbl">Departure Check-Out</span>
                    <span class="manifest-val"><?php echo date("M d, Y", strtotime($checkout)); ?></span>
                </div>

                <div class="divider-dash"></div>

                <div class="final-paid-row">
                    <span class="paid-lbl">Total Amount Paid</span>
                    <span class="paid-val">$<?php echo number_format((float)$final_total, 2); ?></span>
                </div>
            </div>

            <a href="index.php" class="return-home-btn">Return to Main Dashboard</a>
        </div>
    </main>

</body>
</html>