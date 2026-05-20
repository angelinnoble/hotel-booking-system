<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Optional: Simple filter processing if you want to expand this later
$filter = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Journal | The Rock Luxury Hotel &amp; Spa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400;1,600&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pine-green: #0F1E19;
            --forest-green: #1C332A;
            --moss-green: #7D8F82;
            --gold-accent: #C5A880;
            --ivory: #FAF9F5;
            --ivory-dark: #F2EFE9;
            --transition-smooth: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
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
        .nav-links a { color: var(--ivory); text-decoration: none; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; transition: color 0.3s; }
        .nav-links a:hover { color: var(--gold-accent); }
        .logout-btn { border: 1px solid var(--gold-accent); color: var(--gold-accent) !important; padding: 6px 16px; text-decoration: none; transition: var(--transition-smooth); }
        .logout-btn:hover { background: var(--gold-accent); color: var(--pine-green) !important; }

        /* --- JOURNAL HERO BANNER --- */
        .blog-hero {
            height: 50vh;
            background: linear-gradient(rgba(15, 30, 25, 0.4), rgba(15, 30, 25, 0.9)), 
                        url('https://images.unsplash.com/photo-1542224566-6e85f2e6772f?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--ivory); padding-top: 80px;
        }
        .blog-hero h1 { font-family: 'Cinzel', serif; font-size: 3.5rem; letter-spacing: 12px; text-transform: uppercase; font-weight: 400; margin-bottom: 10px; }
        .blog-hero p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.2rem; color: var(--gold-accent); letter-spacing: 1px; }

        /* --- CATEGORY FILTERS --- */
        .filter-bar { display: flex; justify-content: center; gap: 30px; margin: 60px 0 20px 0; padding: 0 4%; }
        .filter-btn { background: none; border: none; font-size: 0.75rem; font-weight: 500; letter-spacing: 2px; text-transform: uppercase; color: var(--moss-green); cursor: pointer; transition: color 0.3s; padding-bottom: 8px; border-bottom: 1px solid transparent; }
        .filter-btn:hover, .filter-btn.active { color: var(--pine-green); border-bottom-color: var(--gold-accent); }

        /* --- JOURNAL EDITORIAL MAIN GRID --- */
        .journal-container { max-width: 1300px; margin: 0 auto 120px auto; padding: 0 4%; display: grid; grid-template-columns: repeat(12, 1fr); gap: 40px; }

        .blog-card { background: white; border: 1px solid rgba(15,30,25,0.05); box-shadow: 0 15px 40px rgba(15,30,25,0.02); display: flex; flex-direction: column; overflow: hidden; transition: var(--transition-smooth); }
        .blog-card:hover { transform: translateY(-5px); box-shadow: 0 25px 50px rgba(15,30,25,0.06); }
        
        .card-img-wrapper { width: 100%; overflow: hidden; position: relative; }
        .card-img { width: 100%; height: 100%; object-fit: cover; transition: transform 1.2s ease; }
        .blog-card:hover .card-img { transform: scale(1.03); }

        .card-content { padding: 40px; display: flex; flex-direction: column; flex-grow: 1; }
        .post-meta { font-size: 0.65rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--gold-accent); margin-bottom: 15px; display: flex; gap: 15px; }
        .post-meta span.author { color: var(--moss-green); font-weight: 400; }
        
        .post-title { font-family: 'Cinzel', serif; font-size: 1.5rem; font-weight: 400; line-spacing: 1.3; margin-bottom: 20px; color: var(--pine-green); text-decoration: none; }
        .post-title a { color: inherit; text-decoration: none; }
        .post-title a:hover { color: var(--forest-green); }

        .post-excerpt { font-size: 0.9rem; line-height: 1.8; color: #555; margin-bottom: 30px; text-align: justify; }
        .read-more-link { margin-top: auto; font-size: 0.75rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--pine-green); text-decoration: none; display: inline-block; transition: color 0.3s; }
        .read-more-link:hover { color: var(--gold-accent); }

        /* --- ASYMMETRIC SPAZIAL GRID SIZING (High-End Magazine Layout) --- */
        /* Large Highlight Feature - Travel Guide */
        .grid-col-8 { grid-column: span 8; }
        .grid-col-8 .card-img-wrapper { height: 450px; }
        
        /* Side Small Feature - Guest Experience */
        .grid-col-4 { grid-column: span 4; }
        .grid-col-4 .card-img-wrapper { height: 260px; }

        /* Half-Half Standard Layouts */
        .grid-col-6 { grid-column: span 6; }
        .grid-col-6 .card-img-wrapper { height: 340px; }

        /* --- GLOBAL FOOTER --- */
        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; color: var(--ivory); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); }

        @media (max-width: 992px) {
            .journal-container { grid-template-columns: 1fr; gap: 40px; }
            .grid-col-8, .grid-col-4, .grid-col-6 { grid-column: span 12; }
            .card-img-wrapper { height: 300px !important; }
            .blog-hero h1 { font-size: 2.4rem; }
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
            <li><a href="blog.php" style="color: var(--gold-accent);">Blog</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php">Policies &amp; FAQ</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="blog-hero">
        <h1>The Journal</h1>
        <p>Essays on environmental travel frameworks and lived perspective narratives</p>
    </header>

    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterCategory('all')">All Entries</button>
        <button class="filter-btn" onclick="filterCategory('guides')">Travel Guides</button>
        <button class="filter-btn" onclick="filterCategory('guest-exp')">Guest Experiences</button>
    </div>

    <main class="journal-container">

        <article class="blog-card grid-col-8 cat-guides">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1200&q=80" alt="Coastal Geologies" class="card-img">
            </div>
            <div class="card-content">
                <div class="post-meta">
                    <span>Travel Guides</span>
                    <span class="author">By Marcus Vance (Resort Naturalist)</span>
                </div>
                <h2 class="post-title"><a href="#">Mapping Hidden Monolithic Marine Caves</a></h2>
                <p class="post-excerpt">
                    Beyond our primary cliff face boundaries lies a system of tidal caves cut deep into raw volcanic basalt stone frameworks over thousands of years. This structural guide tracks the absolute best timings, tidal conditions, and safety vectors required to safely kayak into these cathedral-like natural sound chambers during early morning light windows.
                </p>
                <a href="#" class="read-more-link">Read Essay →</a>
            </div>
        </article>

        <article class="blog-card grid-col-4 cat-guest-exp">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1463319611694-4bf9eb5a6e72?auto=format&fit=crop&w=600&q=80" alt="Guest Journaling" class="card-img">
            </div>
            <div class="card-content">
                <div class="post-meta">
                    <span>Guest Experience</span>
                    <span class="author">By Clara &amp; David S.</span>
                </div>
                <h2 class="post-title"><a href="#">Four Nights of Complete Psychological Silence</a></h2>
                <p class="post-excerpt">
                    "We arrived at the Forest Cottage with high levels of operational exhaustion. By morning two, sitting level with the tropical root system networks and breathing clean coastal air, our circadian patterns reset entirely..."
                </p>
                <a href="#" class="read-more-link">Read Narrative →</a>
            </div>
        </article>

        <article class="blog-card grid-col-6 cat-guest-exp">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80" alt="Ocean Immersion" class="card-img">
            </div>
            <div class="card-content">
                <div class="post-meta">
                    <span>Guest Experience</span>
                    <span class="author">By Elena Rostova</span>
                </div>
                <h2 class="post-title"><a href="#">Surrendering to the Horizon: An Ocean Villa Story</a></h2>
                <p class="post-excerpt">
                    An in-depth reflection on spending a full week watching changing weather fronts approach the cliffside from an infinity pool framework. Elena breaks down the beautiful sensation of architectural transparency where the walls completely disappear into deep ocean winds.
                </p>
                <a href="#" class="read-more-link">Read Narrative →</a>
            </div>
        </article>

        <article class="blog-card grid-col-6 cat-guides">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=800&q=80" alt="Epicurean Art" class="card-img">
            </div>
            <div class="card-content">
                <div class="post-meta">
                    <span>Travel Guides</span>
                    <span class="author">By Culinary Director Chef Jean-Luis</span>
                </div>
                <h2 class="post-title"><a href="#">A Geographic Analysis of Coastal Spice Pathways</a></h2>
                <p class="post-excerpt">
                    An educational breakdown detailing how our team sources ancestral botanicals and sea-salts directly from artisanal regional families. Learn about the hyper-local tracking metrics used to build out the daily curated epicurean dinner menus.
                </p>
                <a href="#" class="read-more-link">Read Essay →</a>
            </div>
        </article>

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
                    <li><a href="activities.php">Activities</a></li>
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
        function filterCategory(category) {
            // Update Active Class States on Buttons
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Handle card grid filtering visibility matrix
            const cards = document.querySelectorAll('.blog-card');
            
            cards.forEach(card => {
                if(category === 'all') {
                    card.style.display = 'flex';
                } else if(category === 'guides') {
                    card.style.display = card.classList.contains('cat-guides') ? 'flex' : 'none';
                } else if(category === 'guest-exp') {
                    card.style.display = card.classList.contains('cat-guest-exp') ? 'flex' : 'none';
                }
            });
        }
    </script>

</body>
</html>