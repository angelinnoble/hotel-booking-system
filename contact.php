<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Simple backend processor message flag placeholder
$form_status = "";
if (isset($_POST['send_inquiry'])) {
    // In a production setup, you would sanitize these inputs and run an INSERT SQL query or wp_mail/mail action.
    $form_status = "Inquiry successfully cataloged. Our Private Concierge team will review your timeline shortly.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | The Rock Luxury Hotel &amp; Spa</title>
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
        body { background-color: var(--ivory); color: var(--pine-green); font-weight: 300; overflow-x: hidden; }

        /* --- STICKY NAVIGATION --- */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 30px 6%;
            background: rgba(15, 30, 25, 0.95); position: fixed; width: 100%; top: 0; z-index: 100;
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-bottom: 1px solid rgba(250, 249, 245, 0.05);
        }
        .nav-logo { font-family: 'Cinzel', serif; font-size: 1.6rem; letter-spacing: 4px; color: var(--ivory); text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 35px; align-items: center; }
        .nav-links a { color: var(--ivory); text-decoration: none; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }
        .nav-links a:hover { color: var(--gold-accent); }
        .logout-btn { border: 1px solid var(--gold-accent); color: var(--gold-accent) !important; padding: 6px 16px; text-decoration: none; transition: var(--transition-smooth); }
        .logout-btn:hover { background: var(--gold-accent); color: var(--pine-green) !important; }

        /* --- CONTACT HERO BANNER --- */
        .contact-hero {
            height: 45vh;
            background: linear-gradient(rgba(15, 30, 25, 0.4), rgba(15, 30, 25, 0.9)), 
                        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--ivory); padding-top: 80px;
        }
        .contact-hero h1 { font-family: 'Cinzel', serif; font-size: 3.5rem; letter-spacing: 12px; text-transform: uppercase; font-weight: 400; margin-bottom: 10px; }
        .contact-hero p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.2rem; color: var(--gold-accent); letter-spacing: 1px; }

        /* --- MAIN SPLIT PANEL INTERFACE --- */
        .contact-grid-wrapper { max-width: 1300px; margin: 90px auto; padding: 0 4%; display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 90px; }

        /* Left Panel Coordinates styling */
        .coordinates-panel h2 { font-family: 'Cinzel', serif; font-size: 2rem; font-weight: 400; letter-spacing: 2px; margin-bottom: 30px; }
        .coordinates-p { font-size: 0.95rem; line-height: 1.8; color: #444; margin-bottom: 45px; }
        
        .channel-block { margin-bottom: 30px; display: flex; flex-direction: column; gap: 5px; }
        .channel-label { font-size: 0.65rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--moss-green); }
        .channel-value { font-size: 1rem; color: var(--pine-green); text-decoration: none; font-weight: 400; }
        a.channel-value:hover { color: var(--gold-accent); }

        /* Elegant Minimal Social Links Framework */
        .social-signature-box { margin-top: 60px; border-top: 1px solid rgba(15,30,25,0.1); padding-top: 35px; }
        .social-signature-box h5 { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px; color: var(--pine-green); }
        .social-row { display: flex; gap: 25px; list-style: none; }
        .social-row a { color: var(--moss-green); text-decoration: none; font-size: 0.8rem; font-weight: 500; letter-spacing: 2px; text-transform: uppercase; transition: color 0.3s; }
        .social-row a:hover { color: var(--gold-accent); }

        /* Right Panel Luxury Inquiry Form styling */
        .inquiry-form-card { background: white; border: 1px solid rgba(15,30,25,0.06); padding: 50px; box-shadow: 0 30px 60px rgba(15,30,25,0.04); }
        .inquiry-form-card h3 { font-family: 'Cinzel', serif; font-size: 1.3rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 30px; color: var(--forest-green); }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .field-element { display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px; }
        .field-element.full-width { margin-bottom: 30px; }
        .field-element label { font-size: 0.65rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--moss-green); }
        .field-element input, .field-element textarea { padding: 14px; border: 1px solid rgba(15,30,25,0.15); background: var(--ivory); outline: none; font-size: 0.9rem; color: var(--pine-green); transition: border-color 0.3s; }
        .field-element input:focus, .field-element textarea:focus { border-color: var(--gold-accent); background: white; }
        
        .action-submit-btn { width: 100%; background: var(--forest-green); color: var(--ivory); border: 1px solid var(--forest-green); padding: 18px; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: var(--transition-smooth); }
        .action-submit-btn:hover { background: transparent; color: var(--forest-green); }

        .status-alert-banner { background: #E6EEEC; border-left: 3px solid var(--forest-green); padding: 15px; font-size: 0.85rem; color: var(--forest-green); font-weight: 500; margin-bottom: 30px; letter-spacing: 0.5px; }

        /* --- INTEGRATED QUICK FAQ ACCORDION SECTION --- */
        .faq-accordion-section { max-width: 1300px; margin: 40px auto 120px auto; padding: 0 4%; border-top: 1px solid rgba(15,30,25,0.08); padding-top: 80px; }
        .faq-section-title { font-family: 'Cinzel', serif; font-size: 1.8rem; letter-spacing: 3px; text-transform: uppercase; text-align: center; margin-bottom: 50px; }
        
        .faq-node { background: white; border: 1px solid rgba(15,30,25,0.06); margin-bottom: 15px; overflow: hidden; }
        .faq-trigger { padding: 24px 35px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; background: white; transition: background 0.3s; }
        .faq-trigger h4 { font-family: 'Cinzel', serif; font-size: 1rem; font-weight: 500; letter-spacing: 1px; color: var(--pine-green); }
        .faq-icon { font-family: 'Cinzel', serif; font-size: 1.2rem; color: var(--moss-green); transition: transform 0.4s; }
        
        .faq-drawer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: var(--ivory); }
        .faq-drawer p { padding: 30px 35px; font-size: 0.9rem; line-height: 1.8; color: #555; text-align: justify; }

        /* Dynamic FAQ State modifiers */
        .faq-node.faq-active { border-color: var(--gold-accent); }
        .faq-node.faq-active .faq-icon { transform: rotate(45deg); color: var(--gold-accent); }
        .faq-node.faq-active .faq-drawer { max-height: 250px; }

        /* --- GLOBAL FOOTER --- */
        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; color: var(--ivory); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); }

        @media (max-width: 992px) {
            .contact-grid-wrapper { grid-template-columns: 1fr; gap: 60px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .inquiry-form-card { padding: 30px; }
            .contact-hero h1 { font-size: 2.4rem; }
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="index.php" class="nav-logo">The Rock</a>
        <ul class="nav-links">
            <li><a href="about.php">About Us</a></li>
            <li><a href="accommodation.php">Accommodation</a></li>
            <li><a href="activities.php">Activities &amp; Experience</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contact.php" style="color: var(--gold-accent);">Contact Us</a></li>
            <li><a href="faq.php">Policies &amp; FAQ</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="contact-hero">
        <h1>Correspondence</h1>
        <p>Initiate direct communication lines with our architectural compound</p>
    </header>

    <main class="contact-grid-wrapper">
        
        <section class="coordinates-panel">
            <h2>The Registry</h2>
            <p class="coordinates-p">
                Whether organizing structural transport logistics, scheduling precise deep wellness itineraries, or mapping out tailored corporate property buyouts, our global administration team handles all frameworks with absolute discretion.
            </p>

            <div class="channel-block">
                <span class="channel-label">Geographic Foothold</span>
                <span class="channel-value">Cliff Band 04, Southern Metamorphic Shelf, Island Frontier</span>
            </div>

            <div class="channel-block">
                <span class="channel-label">Direct Voice Routing</span>
                <a href="tel:+18005557625" class="channel-value">+1 (800) 555-ROCK</a>
            </div>

            <div class="channel-block">
                <span class="channel-label">Encrypted Matrix Mail</span>
                <a href="mailto:concierge@therockluxury.com" class="channel-value">concierge@therockluxury.com</a>
            </div>

            <div class="social-signature-box">
                <h5>Digital Signatures</h5>
                <ul class="social-row">
                    <li><a href="#" target="_blank">Instagram</a></li>
                    <li><a href="#" target="_blank">Vimeo Journal</a></li>
                    <li><a href="#" target="_blank">LinkedIn Corporate</a></li>
                </ul>
            </div>
        </section>

        <section class="inquiry-form-card">
            <h3>Inquiry Submission</h3>

            <?php if(!empty($form_status)): ?>
                <div class="status-alert-banner"><?php echo $form_status; ?></div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="form-row">
                    <div class="field-element">
                        <label>Given Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="field-element">
                        <label>Family Surname</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div class="field-element full-width">
                    <label>Secure Communication Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="field-element full-width">
                    <label>Nature of Correspondence</label>
                    <input type="text" name="subject" placeholder="e.g., Luxury Villa Buyout, Heli-Transport Details" required>
                </div>

                <div class="field-element full-width">
                    <label>Operational Narrative / Requirements</label>
                    <textarea name="message" rows="5" placeholder="Detail any explicit environmental or dietary frameworks required..." required></textarea>
                </div>

                <button type="submit" name="send_inquiry" class="action-submit-btn">Send Secure Inquiry</button>
            </form>
        </section>
    </main>

    <section class="faq-accordion-section">
        <h2 class="faq-section-title">Policies &amp; Direct Clarifications</h2>
        
        <div class="faq-node">
            <div class="faq-trigger" onclick="toggleFaq(this)">
                <h4>What are the standard arrival and flight coordinates frameworks?</h4>
                <div class="faq-icon">+</div>
            </div>
            <div class="faq-drawer">
                <p>
                    Standard property landing frames begin at 15:00 hours, and absolute room exit sequences close at 11:00 hours. If your group is arriving via private helicopter charters directly onto our northern pad terminal networks, please verify arrival windows with our dispatch team 48 hours prior to liftoff.
                </p>
            </div>
        </div>

        <div class="faq-node">
            <div class="faq-trigger" onclick="toggleFaq(this)">
                <h4>What structural frameworks govern reservation cancellation timelines?</h4>
                <div class="faq-icon">+</div>
            </div>
            <div class="faq-drawer">
                <p>
                    Because we construct highly specific environmental isolation loops for each individual booking, full cancellations must be explicitly requested 30 business days before your targeted arrival frame to secure full deposit mitigation. Cancellations processed under 14 days forfeit primary structural deposits.
                </p>
            </div>
        </div>

        <div class="faq-node">
            <div class="faq-trigger" onclick="toggleFaq(this)">
                <h4>Are children under standard occupancy limits permitted inside the sanctuaries?</h4>
                <div class="faq-icon">+</div>
            </div>
            <div class="faq-drawer">
                <p>
                    To guarantee absolute environmental silence and undisturbed meditative acoustics across the cliff paths for all checked-in clients, our primary property layout is engineered for adult occupancy only. Children over the age of 16 are welcome inside our private Ocean View Villas.
                </p>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <h5 style="font-family:'Cinzel'; font-size:1.4rem; margin-bottom:15px; letter-spacing:2px;">The Rock</h5>
                <p style="color:var(--moss-green); font-size:0.85rem; line-height:1.7;">Delivering sophisticated, environmental isolation across minimalist, classic lines since 2010.</p>
            </div>
            <div class="footer-links">
                <h6>Quick Links</h6>
                <ul>
                    <li><a href="about.php">About Legacy</a></li>
                    <li><a href="accommodation.php">Accommodation</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h6>Explore Layouts</h6>
                <ul>
                    <li><a href="#">Privacy Agreement</a></li>
                    <li><a href="#">Terms &amp; Conditions</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 The Rock Luxury Resort Group. All Rights Reserved.
        </div>
    </footer>

    <script>
        function toggleFaq(triggerElement) {
            const currentFaqNode = triggerElement.parentElement;
            const isNodeCurrentlyActive = currentFaqNode.classList.contains('faq-active');

            // Close alternative nodes if open
            document.querySelectorAll('.faq-node').forEach(node => {
                node.classList.remove('faq-active');
            });

            // Toggle active state on target selector
            if (!isNodeCurrentlyActive) {
                currentFaqNode.classList.add('faq-active');
            }
        }
    </script>
</body>
</html>