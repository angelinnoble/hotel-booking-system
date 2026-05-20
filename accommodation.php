<?php
session_start();
include 'db.php';

// Redirect to login if user tries to view this page directly without being authenticated
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
    <title>Accommodations | The Rock Luxury Hotel & Spa</title>
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
            --transition-smooth: all 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--ivory);
            color: var(--pine-green);
            font-weight: 300;
            overflow-x: hidden;
        }

        /* --- NAVIGATION --- */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 6%;
            background: rgba(15, 30, 25, 0.95);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(250, 249, 245, 0.05);
        }

        .nav-logo {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            font-weight: 400;
            letter-spacing: 4px;
            color: var(--ivory);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 35px;
            align-items: center;
        }

        .nav-links a {
            color: var(--ivory);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: color 0.3s;
        }

        .nav-links a:hover { color: var(--gold-accent); }

        .logout-btn {
            border: 1px solid var(--gold-accent) !important;
            color: var(--gold-accent) !important;
            padding: 6px 16px;
            transition: var(--transition-smooth);
        }
        .logout-btn:hover { background: var(--gold-accent); color: var(--pine-green) !important; }

        /* --- HERO HEADER --- */
        .page-header {
            height: 60vh;
            background: linear-gradient(rgba(15, 30, 25, 0.4), rgba(15, 30, 25, 0.85)), 
                        url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--ivory);
            padding-top: 80px;
        }

        .page-header h1 {
            font-family: 'Cinzel', serif;
            font-size: 3.5rem;
            letter-spacing: 10px;
            text-transform: uppercase;
            font-weight: 400;
            margin-bottom: 15px;
        }

        .page-header p {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.2rem;
            color: var(--gold-accent);
            letter-spacing: 2px;
        }

        /* --- ACCOMMODATION CATEGORIES GRID --- */
        .showcase-container {
            padding: 100px 8%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .room-row {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 80px;
            margin-bottom: 120px;
            align-items: center;
        }

        /* Alternate alignment for even elements */
        .room-row:nth-child(even) {
            grid-template-columns: 0.9fr 1.1fr;
        }
        .room-row:nth-child(even) .room-image-wrapper {
            order: 2;
        }

        .room-image-wrapper {
            width: 100%;
            height: 550px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(15,30,25,0.15);
            position: relative;
        }

        .room-image {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            transition: transform 1.2s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .room-image-wrapper:hover .room-image {
            transform: scale(1.05);
        }

        .room-meta {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 4px;
            color: var(--gold-accent);
            text-transform: uppercase;
            margin-bottom: 15px;
            display: block;
        }

        .room-title {
            font-family: 'Cinzel', serif;
            font-size: 2.4rem;
            font-weight: 400;
            letter-spacing: 2px;
            margin-bottom: 25px;
            color: var(--pine-green);
        }

        .room-desc {
            font-size: 0.95rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 35px;
        }

        /* Structural Details Matrix Grid */
        .details-matrix {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-top: 1px solid rgba(15,30,25,0.1);
            border-bottom: 1px solid rgba(15,30,25,0.1);
            padding: 25px 0;
            margin-bottom: 35px;
        }

        .matrix-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .matrix-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--moss-green);
            font-weight: 600;
        }

        .matrix-value {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--pine-green);
        }

        /* CTA Button Design */
        .book-cta-btn {
            display: inline-block;
            background: var(--forest-green);
            color: var(--ivory);
            border: 1px solid var(--forest-green);
            text-decoration: none;
            padding: 15px 40px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: var(--transition-smooth);
        }

        .book-cta-btn:hover {
            background: transparent;
            color: var(--forest-green);
            transform: translateY(-2px);
        }

        /* --- FOOTER --- */
        footer { 
            background-color: var(--pine-green); 
            padding: 80px 8% 40px 8%; 
            border-top: 1px solid rgba(250, 249, 245, 0.04); 
            color: var(--ivory); 
        }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; transition: color 0.3s; }
        .footer-links a:hover { color: var(--ivory); }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); letter-spacing: 1px; }

        @media (max-width: 992px) {
            .room-row, .room-row:nth-child(even) { grid-template-columns: 1fr; gap: 40px; }
            .room-row .room-image-wrapper { order: 1 !important; height: 380px; }
            .room-row .room-content { order: 2 !important; }
            .page-header h1 { font-size: 2.4rem; }
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="index.php" class="nav-logo">The Rock</a>
        <ul class="nav-links">
            <li><a href="about.php">About Us</a></li>
            <li><a href="accommodation.php" style="color: var(--gold-accent);">Accommodation</a></li>
            <li><a href="activities.php">Activities &amp; Experience</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php">Policies &amp; FAQ</a></li>
            <li><span style="color: var(--gold-accent); font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; margin-left: 15px;">Hi, <?php echo $_SESSION['user_name']; ?></span></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="page-header">
        <h1>Sanctuaries</h1>
        <p>Architectural sanctuaries of pure environmental immersion</p>
    </header>

    <main class="showcase-container">

        <div class="room-row">
            <div class="room-image-wrapper">
                <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80');"></div>
            </div>
            <div class="room-content">
                <span class="room-meta">The Ultimate Sanctuary</span>
                <h2 class="room-title">Ocean View Villa $850 </h2>
                <p class="room-desc">
                    Perched out on the furthest metamorphic edges of our cliff face, the Ocean View Villa offers uninterrupted horizon access. Featuring a private infinity plunge pool, a structural lava rock outdoor bathing courtyard, and fully retracting floor-to-ceiling glass systems that seamlessly dissolve the boundaries between architecture and ocean wind.
                </p>
                <div class="details-matrix">
                    <div class="matrix-item">
                        <span class="matrix-label">Spatial Allocation</span>
                        <span class="matrix-value">2,400 Sq. Ft. Indoor/Outdoor</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Guest Limits</span>
                        <span class="matrix-value">Up to 3 Occupants Max</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Environmental View</span>
                        <span class="matrix-value">180° Panoramic Indian Ocean</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Premium Amenities</span>
                        <span class="matrix-value">Private Pool, Butler Pantry</span>
                    </div>
                </div>
               <a href="room_details.php?type=luxury-villa" class="book-cta-btn">Discover Sanctuary →</a>
            </div>
        </div>

        <div class="room-row">
            <div class="room-image-wrapper">
                <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80');"></div>
            </div>
            <div class="room-content">
                <span class="room-meta">Refined Linear Space</span>
                <h2 class="room-title">Clifftop Suite $550</h2>
                <p class="room-desc">
                    Our Clifftop Suites highlight architectural warmth through minimalist natural details and clean spatial frameworks. Constructed directly against the monolithic stone shield, these spaces host dual structural lounges, an open stone hearth fire system, and a deep-soaking concrete tub looking straight out onto deep evening marine skies.
                </p>
                <div class="details-matrix">
                    <div class="matrix-item">
                        <span class="matrix-label">Spatial Allocation</span>
                        <span class="matrix-value">1,350 Sq. Ft. Space</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Guest Limits</span>
                        <span class="matrix-value">2 Adults Comfortably</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Environmental View</span>
                        <span class="matrix-value">Elevated Sea Horizon &amp; Coastline</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Premium Amenities</span>
                        <span class="matrix-value">Concrete Bath, Open Fire Hearth</span>
                    </div>
                </div>
              <a href="room_details.php?type=suites" class="book-cta-btn">Discover Sanctuary →</a>
            </div>
        </div>

        <div class="room-row">
            <div class="room-image-wrapper">
                <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1432303492674-642e9d4871b8?auto=format&fit=crop&w=1200&q=80');"></div>
            </div>
            <div class="room-content">
                <span class="room-meta">Subterranean Forest Glade $400 </span>
                <h2 class="room-title">Forest Retreat Cottage</h2>
                <p class="room-desc">
                    Tucked deep within the coastal canopy where ancient tropical root networks weave into the granite foundations, the Forest Retreat Cottage offers quiet, shaded solitude. Dominated by organic materials, raw structural wood columns, and private timber wrap-around sun decks, this ecosystem is engineered for complete mental silence.
                </p>
                <div class="details-matrix">
                    <div class="matrix-item">
                        <span class="matrix-label">Spatial Allocation</span>
                        <span class="matrix-value">980 Sq. Ft. Footprint</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Guest Limits</span>
                        <span class="matrix-value">2 Adults Max Allocation</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Environmental View</span>
                        <span class="matrix-value">Prime Canopy Greenery Views</span>
                    </div>
                    <div class="matrix-item">
                        <span class="matrix-label">Premium Amenities</span>
                        <span class="matrix-value">Timber Deck, Sun-Shower Canopy</span>
                    </div>
                </div>
               <a href="room_details.php?type=cottage" class="book-cta-btn">Discover Sanctuary →</a>
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
                    <li><a href="index.php#reviews">Reviews</a></li>
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

</body>
</html>