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
    <title>Activities &amp; Experiences | The Rock</title>
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

        /* --- IMMERSIVE HERO VIEW --- */
        .page-header {
            height: 55vh;
            background: linear-gradient(rgba(15, 30, 25, 0.3), rgba(15, 30, 25, 0.85)), 
                        url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--ivory); padding-top: 80px;
        }
        .page-header h1 { font-family: 'Cinzel', serif; font-size: 3.5rem; letter-spacing: 8px; text-transform: uppercase; font-weight: 400; margin-bottom: 15px; }
        .page-header p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.2rem; color: var(--gold-accent); letter-spacing: 2px; }

        /* --- EXPERIENCES STRUCTURAL ACCORDION --- */
        .experiences-container { max-width: 1100px; margin: 100px auto; padding: 0 4%; }
        
        .experience-card {
            background: white;
            border: 1px solid rgba(15,30,25,0.06);
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(15,30,25,0.02);
            overflow: hidden;
            transition: var(--transition-smooth);
        }
        
        /* Interactive Toggle Header */
        .exp-header {
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background: white;
            transition: background 0.3s;
        }
        .exp-header:hover { background: #FAF9F6; }
        
        .exp-title-group { display: flex; flex-direction: column; gap: 4px; }
        .exp-title-group span { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold-accent); font-weight: 600; }
        .exp-title-group h3 { font-family: 'Cinzel', serif; font-size: 1.5rem; font-weight: 400; letter-spacing: 1px; color: var(--pine-green); }
        
        .toggle-icon {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            color: var(--moss-green);
            transition: transform 0.4s;
        }

        /* Hidden Content Drawers */
        .exp-content-drawer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-out;
            background: var(--ivory);
        }
        
        .drawer-inner-layout {
            padding: 40px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            border-top: 1px solid rgba(15,30,25,0.05);
        }
        .drawer-narrative p { font-size: 0.95rem; line-height: 1.8; color: #444; margin-bottom: 25px; }
        
        /* Sub-categories grid (Explicitly used for Water Sports branch) */
        .sub-branch-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
        .sub-branch-node {
            background: white;
            border: 1px solid rgba(15,30,25,0.08);
            padding: 15px;
            text-align: center;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            color: var(--forest-green);
            border-bottom: 2px solid var(--gold-accent);
        }

        .drawer-visual-frame { width: 100%; height: 260px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .drawer-visual-frame img { width: 100%; height: 100%; object-fit: cover; }

        /* Active State Classes controlled by JS */
        .experience-card.active-drawer { border-color: var(--gold-accent); box-shadow: 0 20px 45px rgba(15,30,25,0.06); }
        .experience-card.active-drawer .toggle-icon { transform: rotate(45px); color: var(--gold-accent); }
        .experience-card.active-drawer .exp-content-drawer { max-height: 600px; }

        /* --- GLOBAL FOOTER --- */
        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; color: var(--ivory); margin-top: 15px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); }

        @media(max-width: 768px) {
            .drawer-inner-layout { grid-template-columns: 1fr; gap: 30px; padding: 20px; }
            .page-header h1 { font-size: 2.2rem; }
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
            <li><a href="activities.php" style="color: var(--gold-accent);">Activities &amp; Experience</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php">Policies &amp; FAQ</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="page-header">
        <h1>Curated Journeys</h1>
        <p>Unearth deliberate excursions tailored beyond standard parameters</p>
    </header>

    <main class="experiences-container">

        <div class="experience-card">
            <div class="exp-header" onclick="toggleDrawer(this)">
                <div class="exp-title-group">
                    <span>Ocean Curations</span>
                    <h3>Water Sports</h3>
                </div>
                <div class="toggle-icon">+</div>
            </div>
            <div class="exp-content-drawer">
                <div class="drawer-inner-layout">
                    <div class="drawer-narrative">
                        <p>Engage seamlessly with dynamic marine conditions along our private coastal shelf through high-grade marine gear systems.</p>
                        <div class="sub-branch-grid">
                            <div class="sub-branch-node">Snorkeling</div>
                            <div class="sub-branch-node">Jet Ski</div>
                            <div class="sub-branch-node">Diving</div>
                        </div>
                    </div>
                    <div class="drawer-visual-frame">
                        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=600&q=80" alt="Water Sports Framework">
                    </div>
                </div>
            </div>
        </div>

        <div class="experience-card">
            <div class="exp-header" onclick="toggleDrawer(this)">
                <div class="exp-title-group">
                    <span>Internal Stillness</span>
                    <h3>Spa &amp; Wellness</h3>
                </div>
                <div class="toggle-icon">+</div>
            </div>
            <div class="exp-content-drawer">
                <div class="drawer-inner-layout">
                    <div class="drawer-narrative">
                        <p>Decompress deeply via architectural stone treatment sanctuaries featuring hydrotherapy soundscapes, geothermal baths, and localized botanical custom oils designed for absolute physical reset.</p>
                    </div>
                    <div class="drawer-visual-frame">
                        <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80" alt="Spa & Wellness">
                    </div>
                </div>
            </div>
        </div>

        <div class="experience-card">
            <div class="exp-header" onclick="toggleDrawer(this)">
                <div class="exp-title-group">
                    <span>Geographic Mapping</span>
                    <h3>Adventure &amp; Excursions</h3>
                </div>
                <div class="toggle-icon">+</div>
            </div>
            <div class="exp-content-drawer">
                <div class="drawer-inner-layout">
                    <div class="drawer-narrative">
                        <p>Scale technical geological ridge structures alongside elite wilderness experts or navigate hidden historic rock caverns tracing ancient geographic timelines.</p>
                    </div>
                    <div class="drawer-visual-frame">
                        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80" alt="Adventure Trails">
                    </div>
                </div>
            </div>
        </div>

        <div class="experience-card">
            <div class="exp-header" onclick="toggleDrawer(this)">
                <div class="exp-title-group">
                    <span>Heritage Archives</span>
                    <h3>Cultural Experiences</h3>
                </div>
                <div class="toggle-icon">+</div>
            </div>
            <div class="exp-content-drawer">
                <div class="drawer-inner-layout">
                    <div class="drawer-narrative">
                        <p>Immerse cleanly within regional artistic heritage, private classical music performances inside granite amphitheaters, and historical architectural documentation tours.</p>
                    </div>
                    <div class="drawer-visual-frame">
                        <img src="https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=600&q=80" alt="Heritage Space">
                    </div>
                </div>
            </div>
        </div>

        <div class="experience-card">
            <div class="exp-header" onclick="toggleDrawer(this)">
                <div class="exp-title-group">
                    <span>Shared Assemblage</span>
                    <h3>Resort Events</h3>
                </div>
                <div class="toggle-icon">+</div>
            </div>
            <div class="exp-content-drawer">
                <div class="drawer-inner-layout">
                    <div class="drawer-narrative">
                        <p>Exclusive sunset beach gatherings, epicurean pairings hosted by international Michelin culinary leaders, and stellar astronomical sky mappings at midnight.</p>
                    </div>
                    <div class="drawer-visual-frame">
                        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=600&q=80" alt="Resort Assemblies">
                    </div>
                </div>
            </div>
        </div>

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
        function toggleDrawer(headerElement) {
            const currentCard = headerElement.parentElement;
            
            // Check if clicked drawer is already active
            const isAlreadyActive = currentCard.classList.contains('active-drawer');
            
            // Close any currently open drawers to mirror high-end UI patterns cleanly
            document.querySelectorAll('.experience-card').forEach(card => {
                card.classList.remove('active-drawer');
                card.querySelector('.toggle-icon').textContent = '+';
            });
            
            // If it wasn't open, open it now!
            if (!isAlreadyActive) {
                currentCard.classList.add('active-drawer');
                currentCard.querySelector('.toggle-icon').textContent = '×';
            }
        }
    </script>
</body>
</html>