<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// 1. Fallback Protection Guardrail
if (!isset($_POST['room_type']) || !isset($_POST['checkin']) || !isset($_POST['checkout'])) {
    header("Location: accommodation.php");
    exit();
}

// 2. Extract Sanitized User Data payloads
$user_name = $_SESSION['user_name'];
$room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
$checkin   = mysqli_real_escape_string($conn, $_POST['checkin']);
$checkout  = mysqli_real_escape_string($conn, $_POST['checkout']);

// 3. Room Base Pricing Matrix
$price_matrix = [
    'Ocean View Villa'    => 850,
    'Clifftop Suite'      => 550,
    'Forest Retreat Room' => 400
];
$base_rate = isset($price_matrix[$room_type]) ? $price_matrix[$room_type] : 450;

// 4. Temporal Night Matrix Core Engine
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$interval = $date1->diff($date2);
$total_nights = $interval->days;
if ($total_nights <= 0) { $total_nights = 1; }

// Initial server-side math calculations
$room_subtotal = $base_rate * $total_nights;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailor Your Experience | The Rock</title>
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
        body { background-color: var(--ivory); color: var(--pine-green); font-weight: 300; }

        /* --- STICKY STYLISH NAV --- */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 30px 6%;
            background: rgba(15, 30, 25, 0.95); position: fixed; width: 100%; top: 0; z-index: 100;
            backdrop-filter: blur(8px); border-bottom: 1px solid rgba(250, 249, 245, 0.05);
        }
        .nav-logo { font-family: 'Cinzel', serif; font-size: 1.6rem; letter-spacing: 4px; color: var(--ivory); text-decoration: none; }
        .nav-links a { color: var(--ivory); text-decoration: none; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }

        /* --- SPLIT CHECKOUT INTERFACE --- */
        .checkout-container {
            max-width: 1300px; margin: 140px auto 100px auto; padding: 0 4%;
            display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 60px;
        }

        .section-header { font-family: 'Cinzel', serif; font-size: 1.4rem; font-weight: 400; letter-spacing: 2px; margin-bottom: 25px; text-transform: uppercase; border-bottom: 1px solid rgba(15,30,25,0.08); padding-bottom: 10px; }
        
        /* Left Column Content Elements */
        .card-block { background: white; border: 1px solid rgba(15,30,25,0.06); padding: 40px; box-shadow: 0 15px 40px rgba(15,30,25,0.02); margin-bottom: 40px; }
        
        .profile-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        .data-node { display: flex; flex-direction: column; gap: 4px; }
        .data-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--moss-green); font-weight: 600; }
        .data-value { font-size: 0.95rem; font-weight: 400; color: var(--pine-green); }

        /* Extra Activity Selective Cards Grid */
        .activities-grid { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
        .activity-card { display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(15,30,25,0.1); padding: 20px 25px; background: var(--ivory); transition: var(--transition-smooth); cursor: pointer; }
        .activity-card:hover { border-color: var(--gold-accent); background: white; }
        
        .activity-left-wrapper { display: flex; align-items: center; gap: 20px; }
        .activity-checkbox { width: 18px; height: 18px; accent-color: var(--forest-green); cursor: pointer; }
        
        .activity-title { font-family: 'Cinzel', serif; font-size: 0.9rem; font-weight: 600; letter-spacing: 1px; margin-bottom: 3px; }
        .activity-desc { font-size: 0.75rem; color: #666; line-height: 1.4; }
        .activity-cost { font-family: 'Montserrat', sans-serif; font-size: 1rem; font-weight: 400; color: var(--gold-accent); }

        /* Highlight selection indicator styling */
        .activity-card.selected-tier { border-color: var(--gold-accent); background: white; box-shadow: 0 10px 30px rgba(15,30,25,0.04); }

        /* Right Column Receipt Panel */
        .billing-sticky-summary { position: sticky; top: 140px; height: fit-content; }
        .receipt-card { background: var(--pine-green); color: var(--ivory); padding: 45px; box-shadow: 0 30px 60px rgba(15,30,25,0.15); }
        .receipt-card h3 { font-family: 'Cinzel', serif; font-size: 1.2rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 30px; text-align: center; color: var(--gold-accent); }
        
        .billing-item-line { display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 0.85rem; letter-spacing: 0.5px; }
        .billing-item-line .line-lbl { color: var(--moss-green); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
        .billing-item-line .line-val { font-weight: 400; }

        .dynamic-addons-injection-pane { border-top: 1px dashed rgba(250,249,245,0.15); margin: 20px 0; padding-top: 15px; }
        .price-divider { border-top: 1px solid rgba(250,249,245,0.1); margin: 25px 0 15px 0; }
        
        .total-amount-row { display: flex; justify-content: space-between; align-items: center; padding-top: 10px; }
        .total-amount-row .total-title { font-family: 'Cinzel', serif; font-size: 1.05rem; letter-spacing: 2px; text-transform: uppercase; color: var(--gold-accent); }
        .total-amount-row .total-price { font-size: 1.7rem; font-weight: 200; }

        .checkout-action-btn { width: 100%; background: var(--gold-accent); color: var(--pine-green); border: none; padding: 18px; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: var(--transition-smooth); margin-top: 30px; }
        .checkout-action-btn:hover { background: var(--ivory); color: var(--pine-green); }

        @media (max-width: 992px) {
            .checkout-container { grid-template-columns: 1fr; gap: 40px; }
            .profile-row { grid-template-columns: 1fr; gap: 15px; }
            .billing-sticky-summary { position: relative; top: 0; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="index.php" class="nav-logo">The Rock</a>
        <ul class="nav-links">
            <li><a href="accommodation.php">← Abort &amp; Return</a></li>
        </ul>
    </nav>

    <main class="checkout-container">
        
        <section class="configuration-wrapper">
            
            <div class="card-block">
                <h2 class="section-header">Reservation Manifest</h2>
                <div class="profile-row">
                    <div class="data-node">
                        <span class="data-label">Primary Guest Profile</span>
                        <span class="data-value"><?php echo htmlspecialchars($user_name); ?></span>
                    </div>
                    <div class="data-node">
                        <span class="data-label">Selected Space Concept</span>
                        <span class="data-value"><?php echo $room_type; ?></span>
                    </div>
                </div>
                <div class="profile-row">
                    <div class="data-node">
                        <span class="data-label">Check-In Sequence</span>
                        <span class="data-value"><?php echo date("F j, Y", strtotime($checkin)); ?> (15:00h)</span>
                    </div>
                    <div class="data-node">
                        <span class="data-label">Check-Out Sequence</span>
                        <span class="data-value"><?php echo date("F j, Y", strtotime($checkout)); ?> (11:00h)</span>
                    </div>
                </div>
            </div>

            <div class="card-block">
                <h2 class="section-header">Integrate Extra Activity Luxuries</h2>
                <p style="font-size: 0.85rem; color:#666; line-height:1.6;">Enhance your stay loop by selecting from our hyper-curated wellness and adventure frameworks below.</p>
                
                <div class="activities-grid">
                    
                    <label class="activity-card" id="card-heli">
                        <div class="activity-left-wrapper">
                            <input type="checkbox" class="activity-checkbox" data-price="450" data-label="Heli-Coastal Exploration" onchange="updateLiveLedger(this, 'card-heli')">
                            <div>
                                <h4 class="activity-title">Heli-Coastal Aerial Survey</h4>
                                <p class="activity-desc">Private 45-minute flight mapping out remote metamorphic mountain peaks.</p>
                            </div>
                        </div>
                        <span class="activity-cost">+$450</span>
                    </label>

                    <label class="activity-card" id="card-spa">
                        <div class="activity-left-wrapper">
                            <input type="checkbox" class="activity-checkbox" data-price="220" data-label="Deep Thermal Spa Loop" onchange="updateLiveLedger(this, 'card-spa')">
                            <div>
                                <h4 class="activity-title">Deep Geothermal Massage Therapy</h4>
                                <p class="activity-desc">Full-body basalt stone thermal treatment accompanied by volcanic salt scrub cycles.</p>
                            </div>
                        </div>
                        <span class="activity-cost">+$220</span>
                    </label>

                    <label class="activity-card" id="card-diving">
                        <div class="activity-left-wrapper">
                            <input type="checkbox" class="activity-checkbox" data-price="310" data-label="Deep Sea Marine Diving" onchange="updateLiveLedger(this, 'card-diving')">
                            <div>
                                <h4 class="activity-title">Deep Sea Reef Diving Expedition</h4>
                                <p class="activity-desc">Private boat charter and guided immersion along protected deep marine shelf walls.</p>
                            </div>
                        </div>
                        <span class="activity-cost">+$310</span>
                    </label>

                </div>
            </div>

        </section>

        <section class="billing-sticky-summary">
            <div class="receipt-card">
                <h3>Statement of Accounts</h3>
                
                <div class="billing-item-line">
                    <span class="line-lbl">Base Night Rate</span>
                    <span class="line-val">$<?php echo number_format($base_rate, 2); ?> / night</span>
                </div>
                <div class="billing-item-line">
                    <span class="line-lbl">Temporal Multiplier</span>
                    <span class="line-val"><?php echo $total_nights; ?> <?php echo ($total_nights == 1) ? 'Night' : 'Nights'; ?></span>
                </div>
                <div class="billing-item-line">
                    <span class="line-lbl">Room Subtotal</span>
                    <span class="line-val" style="color: var(--gold-accent); font-weight: 500;">$<?php echo number_format($room_subtotal, 2); ?></span>
                </div>

                <div class="dynamic-addons-injection-pane" id="receipt-addons-box">
                    <p style="font-size:0.75rem; color:var(--moss-green); font-style:italic; text-align:center;" id="no-addons-msg">No premium add-ons flagged</p>
                </div>

                <div class="billing-item-line">
                    <span class="line-lbl">Luxury Resort Levy Tax (12%)</span>
                    <span class="line-val" id="tax-display-field">$<?php echo number_format($room_subtotal * 0.12, 2); ?></span>
                </div>

                <div class="price-divider"></div>

                <div class="total-amount-row">
                    <span class="total-title">Total Amount Paid</span>
                    <span class="total-price" id="grand-total-field">$<?php echo number_format($room_subtotal * 1.12, 2); ?></span>
                </div>

                <form action="confirmation.php" method="POST">
                    <input type="hidden" name="room" value="<?php echo $room_type; ?>">
                    <input type="hidden" name="checkin" value="<?php echo $checkin; ?>">
                    <input type="hidden" name="checkout" value="<?php echo $checkout; ?>">
                    <input type="hidden" name="final_total" id="hidden-total-input" value="<?php echo $room_subtotal * 1.12; ?>">
                    
                    <button type="submit" class="checkout-action-btn">Confirm &amp; Process Wire</button>
                </form>
            </div>
        </section>

    </main>

    <script>
        // Setup base calculation configurations parsed from server variables
        const roomSubtotal = <?php echo $room_subtotal; ?>;
        const taxRate = 0.12;
        let selectedAddons = {};

        function updateLiveLedger(checkboxElement, cardId) {
            const price = parseFloat(checkboxElement.getAttribute('data-price'));
            const label = checkboxElement.getAttribute('data-label');
            const cardNode = document.getElementById(cardId);

            // 1. Manage Selection Element UI States
            if (checkboxElement.checked) {
                cardNode.classList.add('selected-tier');
                selectedAddons[cardId] = { price: price, label: label };
            } else {
                cardNode.classList.remove('selected-tier');
                delete selectedAddons[cardId];
            }

            // 2. Refresh the UI Receipt Block Frame
            const addonsBox = document.getElementById('receipt-addons-box');
            addonsBox.innerHTML = ''; // Wipe container clear

            let addonsSubtotal = 0;
            const keys = Object.keys(selectedAddons);

            if (keys.length === 0) {
                addonsBox.innerHTML = '<p style="font-size:0.75rem; color:var(--moss-green); font-style:italic; text-align:center;">No premium add-ons flagged</p>';
            } else {
                keys.forEach(key => {
                    addonsSubtotal += selectedAddons[key].price;
                    
                    // Construct line element
                    const line = document.createElement('div');
                    line.className = 'billing-item-line';
                    line.innerHTML = `
                        <span class="line-lbl" style="color:var(--gold-accent); text-transform:none;">+ ${selectedAddons[key].label}</span>
                        <span class="line-val">$${selectedAddons[key].price.toFixed(2)}</span>
                    `;
                    addonsBox.appendChild(line);
                });
            }

            // 3. Re-evaluate Master Financial Matrix Summary
            const currentAggregateSubtotal = roomSubtotal + addonsSubtotal;
            const calculatedTax = currentAggregateSubtotal * taxRate;
            const updatedGrandTotal = currentAggregateSubtotal + calculatedTax;

            // 4. Inject Fresh Calculations directly into Dom InnerText Targets
            document.getElementById('tax-display-field').innerText = `$${calculatedTax.toFixed(2)}`;
            document.getElementById('grand-total-field').innerText = `$${updatedGrandTotal.toFixed(2)}`;
            
            // Sync with invisible input structure for next-step processing
            document.getElementById('hidden-total-input').value = updatedGrandTotal.toFixed(2);
        }
    </script>
</body>
</html>