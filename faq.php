<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policies &amp; FAQs | The Rock Luxury Hotel &amp; Spa</title>
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

        /* --- STICKY NAVIGATION HEADER --- */
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

        /* --- HERO HEADER --- */
        .policy-hero {
            height: 45vh;
            background: linear-gradient(rgba(15, 30, 25, 0.4), rgba(15, 30, 25, 0.9)), 
                        url('https://images.unsplash.com/photo-1455587734955-081b22074882?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--ivory); padding-top: 80px;
        }
        .policy-hero h1 { font-family: 'Cinzel', serif; font-size: 3.5rem; letter-spacing: 12px; text-transform: uppercase; font-weight: 400; margin-bottom: 10px; }
        .policy-hero p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.2rem; color: var(--gold-accent); letter-spacing: 1px; }

        /* --- TAB SELECTION SPLIT CONTROL --- */
        .policy-layout-grid { max-width: 1300px; margin: 90px auto; padding: 0 4%; display: grid; grid-template-columns: 0.3fr 0.7fr; gap: 80px; }
        
        .tab-menu-aside { position: sticky; top: 140px; height: fit-content; display: flex; flex-direction: column; gap: 15px; }
        .tab-trigger-btn { background: transparent; border: 1px solid rgba(15,30,25,0.08); padding: 20px 30px; text-align: left; font-size: 0.75rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--moss-green); cursor: pointer; transition: var(--transition-smooth); }
        .tab-trigger-btn:hover, .tab-trigger-btn.active-tab { color: var(--pine-green); background: white; border-left: 3px solid var(--gold-accent); box-shadow: 0 10px 30px rgba(15,30,25,0.03); }

        /* --- CONTENT WRAPPER WINDOWS --- */
        .policy-pane-content { display: none; }
        .policy-pane-content.active-pane { display: block; animation: fadeEffect 0.6s ease-in-out; }

        @keyframes fadeEffect {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pane-heading { font-family: 'Cinzel', serif; font-size: 1.8rem; font-weight: 400; letter-spacing: 2px; margin-bottom: 20px; color: var(--forest-green); padding-bottom: 15px; border-bottom: 1px solid rgba(15,30,25,0.08); }
        .pane-intro-text { font-size: 0.95rem; line-height: 1.8; color: #555; margin-bottom: 40px; }

        /* Accordion Node Formats */
        .policy-node { background: white; border: 1px solid rgba(15,30,25,0.05); margin-bottom: 15px; box-shadow: 0 10px 25px rgba(15,30,25,0.01); }
        .policy-trigger { padding: 24px 30px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.3s; }
        .policy-trigger:hover { background: #FAF9F6; }
        .policy-trigger h4 { font-family: 'Cinzel', serif; font-size: 0.95rem; font-weight: 500; letter-spacing: 1px; color: var(--pine-green); }
        .policy-icon { font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--moss-green); transition: transform 0.4s; }
        
        .policy-drawer { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.25, 1, 0.5, 1); background: var(--ivory); }
        .policy-drawer-inner { padding: 30px; font-size: 0.9rem; line-height: 1.8; color: #444; text-align: justify; border-top: 1px solid rgba(15,30,25,0.04); }
        .policy-drawer-inner ul { list-style-position: inside; margin-top: 10px; display: flex; flex-direction: column; gap: 8px; }

        /* Active Modifiers classes parsed by JavaScript */
        .policy-node.node-open { border-color: var(--gold-accent); }
        .policy-node.node-open .policy-icon { transform: rotate(45deg); color: var(--gold-accent); }
        .policy-node.node-open .policy-drawer { max-height: 400px; }

        /* --- GLOBAL FOOTER --- */
        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; color: var(--ivory); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); }

        @media (max-width: 992px) {
            .policy-layout-grid { grid-template-columns: 1fr; gap: 50px; }
            .tab-menu-aside { position: relative; top: 0; flex-direction: row; flex-wrap: wrap; }
            .tab-trigger-btn { flex: 1 1 40%; text-align: center; padding: 15px; }
            .policy-hero h1 { font-size: 2.4rem; }
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
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php" style="color: var(--gold-accent);">Policies &amp; FAQ</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="policy-hero">
        <h1>Frameworks</h1>
        <p>Operational mandates, legal boundaries, and structural inquiries clarified</p>
    </header>

    <main class="policy-layout-grid">
        
        <aside class="tab-menu-aside">
            <button class="tab-trigger-btn active-tab" onclick="switchPane(event, 'cancellation')">Cancellation &amp; Refund</button>
            <button class="tab-trigger-btn" onclick="switchPane(event, 'rules')">Hotel Regulations</button>
            <button class="tab-trigger-btn" onclick="switchPane(event, 'faqs')">General FAQs</button>
        </aside>

        <section class="tab-content-display">

            <div id="cancellation" class="policy-pane-content active-pane">
                <h2 class="pane-heading">Cancellation &amp; Refund Mandates</h2>
                <p class="pane-intro-text">Our resort loop architectures prioritize absolute environmental stillness, reserving specialized custom labor and properties weeks in advance. Please review our timeline parameters regarding financial deposit liquidations carefully.</p>
                
                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Standard Sanctuary Cancellation Window</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            Cancellations initiated more than **30 business days** prior to your checked schedule frame will receive a **100% refund** of your baseline booking reservation down-payment, minus a standard 3% technical payment gateway management toll.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Late Alterations &amp; Partial Deposit Forfeiture</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            If an adjustment request falls within the **14 to 30-day window** preceding targeted check-in, **50% of the aggregate core reservation invoice** will be retained as system mitigation. Cancellations finalized underneath a 14-day timeline are non-refundable.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Weather Contingencies &amp; Force Majeure Flights</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            In cases where severe regional marine tides or cliff wind states limit private heli-transport lines or maritime vessels completely, our core concierge management coordinates credit mapping vouchers allowing clients to reschedule up to 12 months forward.
                        </div>
                    </div>
                </div>
            </div>

            <div id="rules" class="policy-pane-content">
                <h2 class="pane-heading">The Compound Regulations</h2>
                <p class="pane-intro-text">To preserve strict minimalist acoustics and architectural safety frameworks across our monolithic cliff properties, all clients must strictly honor our spatial boundaries.</p>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Acoustic Sanctuaries &amp; Silence Mandates</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            Our entire structural layout functions as a silent psychological retreat. Public spatial zones, cliff pathways, and lounge steps strictly prohibit high-volume external speaker systems. Personal acoustic media devices must utilize spatial headphones in open zones at all times.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Private Infrastructure &amp; Drone Restrictions</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            To ensure total physical privacy for guests across all open-air rain showers and infinity pools, unauthorized aerial drone tracking is completely restricted across the property bounds. Violations lead to immediate operational exit sequences.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Environmental Protection Laws</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            The Rock integrates seamlessly into protective prehistoric granite frameworks and native coastal root clusters. Damaging coral shelves, removing local basalt geological pieces, or disturbing protected cliff-nesting marine birds will trigger immediate fines under local environmental management acts.
                        </div>
                    </div>
                </div>
            </div>

            <div id="faqs" class="policy-pane-content">
                <h2 class="pane-heading">Frequently Answered Queries</h2>
                <p class="pane-intro-text">Quick, structural clarifications regarding daily resort logistics, premium access tools, and spatial designs.</p>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Are all signature meals and epicurean pairings inclusive?</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            Yes, base villa and suite reservations unlock all curated seasonal menu cycles created by our International Culinary Team, including scheduled sunset pairings. Rare vault vintages or direct personal in-room private chef executions map to custom bills.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>How do guests manage medical protocols or structural emergencies?</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            Our northern compound anchors a modern, fully staffed advanced life-support medical station 24/7. Additionally, the resort retains a permanent emergency heli-evacuation contract for rapid emergency transport to global multi-specialty clinical structures.
                        </div>
                    </div>
                </div>

                <div class="policy-node">
                    <div class="policy-trigger" onclick="toggleAccordion(this)">
                        <h4>Can the property support customized group buyouts?</h4>
                        <div class="policy-icon">+</div>
                    </div>
                    <div class="policy-drawer">
                        <div class="policy-drawer-inner">
                            Absolutely. Whole-island compound takeovers for private artistic, philosophical, or corporate retreats can be structured through our Registry panel. We recommend initializing communication 6 to 9 months prior to your target timeline.
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>

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
                    <li><a href="contact.php">Contact Registry</a></li>
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
        // Tab Pane Switcher Logic
        function switchPane(event, paneId) {
            // Remove active classes from all tab buttons
            document.querySelectorAll('.tab-trigger-btn').forEach(btn => {
                btn.classList.remove('active-tab');
            });
            
            // Hide all content panes
            document.querySelectorAll('.policy-pane-content').forEach(pane => {
                pane.classList.remove('active-pane');
            });

            // Set current targeting elements active
            event.target.classList.add('active-tab');
            document.getElementById(paneId).classList.add('active-pane');
            
            // Close any open accordions within the newly displayed pane for visual cleanliness
            document.querySelectorAll('.policy-node').forEach(node => {
                node.classList.remove('node-open');
            });
        }

        // Inline Slide Accordion logic
        function toggleAccordion(triggerElement) {
            const currentNode = triggerElement.parentElement;
            const isCurrentlyOpen = currentNode.classList.contains('node-open');

            // Close siblings inside the active viewport context pane
            currentNode.parentElement.querySelectorAll('.policy-node').forEach(node => {
                node.classList.remove('node-open');
            });

            // If target block wasn't open, open it now
            if (!isCurrentlyOpen) {
                currentNode.classList.add('node-open');
            }
        }
    </script>
</body>
</html>