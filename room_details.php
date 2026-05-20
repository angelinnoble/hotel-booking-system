<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Catch the room type parameter from the URL string. Default to luxury-villa if missing.
$room_type = isset($_GET['type']) ? $_GET['type'] : 'luxury-villa';

// Dynamic Content Dictionary Matrix 
$room_dataset = [
    'luxury-villa' => [
        'title' => 'Ocean View Villa',
        'subtitle' => 'The Ultimate Architectural Sanctuary',
        'hero_img' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1920&q=80',
        'gallery_1' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
        'gallery_2' => 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?auto=format&fit=crop&w=800&q=80',
        'db_value' => 'Ocean View Villa',
        'size' => '2,400 Sq. Ft. Total Spatial Footprint',
        'capacity' => '3 Adults Max Occupancy',
        'view' => '180° Clean Horizon Indian Ocean View',
        'bed' => '1 Emperor Premium King Mattress',
        'long_desc' => 'Suspended over raw metamorphic cliff bands, the Ocean View Villa functions as a clean masterclass in environmental architectural design. The master pavilion features fully custom basalt columns, wire-brushed teak surfaces, and smart glass panels that dissolve into the floors at the touch of a single button. The exterior living area flows naturally into a 15-meter heated saltwater infinity pool, accompanied by a secluded subterranean courtyard containing an open-air rain shower carved straight into prehistoric granite.',
        'features' => ['Private Heated Infinity Pool', '24/7 Dedicated Butler Service', 'Lava Rock Outdoor Courtyard Bath', 'Custom Bang & Olufsen Acoustics', 'Premium In-Room Wine Vault']
    ],
    'suites' => [
        'title' => 'Clifftop Suite',
        'subtitle' => 'Refined Linear Structural Space',
        'hero_img' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1920&q=80',
        'gallery_1' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80',
        'gallery_2' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80',
        'db_value' => 'Clifftop Suite',
        'size' => '1,350 Sq. Ft. Indoor Lounge Frame',
        'capacity' => '2 Adults Comfortably',
        'view' => 'Elevated Golden Coastlines & Surrounding Sea',
        'bed' => '1 Californian Hand-Stitched King Bed',
        'long_desc' => 'The Clifftop Suite balances raw stone textures with highly refined luxury textiles. Designed explicitly into the protective mountain shield, its deep spatial footprint features split-level lounge pathways, a monolithic wood-burning hearth, and a master bedroom alcove lined in pure raw linen wall treatments. The architectural centerpiece is a massive, hand-cast raw concrete soaking bath framed perfectly by a panoramic window overlooking the dramatic, crashing marine tides below.',
        'features' => ['Hand-Cast Raw Concrete Soaking Bath', 'Wood-Burning Integrated Open Hearth', 'Private Sunken Sundeck Pavilion', 'Custom In-Room Mixology Station', 'Pure Flax Linen Bed Treatments']
    ],
    'cottage' => [
        'title' => 'Forest Retreat Cottage',
        'subtitle' => 'Subterranean Botanical Solitude',
        'hero_img' => 'https://images.unsplash.com/photo-1432303492674-642e9d4871b8?auto=format&fit=crop&w=1100&q=80',
        'gallery_1' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=800&q=80',
        'gallery_2' => 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&w=800&q=80',
        'db_value' => 'Forest Retreat Room',
        'size' => '980 Sq. Ft. Shaded Footprint',
        'capacity' => '2 Adults Maximum Allocation',
        'view' => 'Deep Emerald Tropical Jungle Canopy',
        'bed' => '1 Organic Latex King Bed Framework',
        'long_desc' => 'Hidden safely underneath a dense canopy of ancient tropical trees where wilderness roots integrate cleanly into the resort foundations, the Forest Retreat Cottage offers supreme psychological decompression. Built using low-impact structural timbers, reclaimed stone pavers, and raw copper piping, the cottage is cool, earthy, and perfectly quiet. A wraparound cantilevered deck puts you level with the canopy ecosystem, perfect for early sunrise contemplation.',
        'features' => ['Cantilevered Timber Viewing Platform', 'Outdoor Organic Sun-Shower Canopy', 'Integrated Wellness Yoga Mat Space', 'Acoustic Soundproofing Insulation', 'Artisanal Loose Tea Apothecary']
    ]
];

// Fallback safety route if a user manually inserts a broken query value
if (!array_key_exists($room_type, $room_dataset)) {
    $room = $room_dataset['luxury-villa'];
} else {
    $room = $room_dataset[$room_type];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['title']; ?> | The Rock Details</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background-color: var(--ivory); color: var(--pine-green); font-weight: 300; overflow-x: hidden; }

        /* --- STICKY NAV --- */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 30px 6%;
            background: rgba(15, 30, 25, 0.95); position: fixed; width: 100%; top: 0; z-index: 100;
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-bottom: 1px solid rgba(250, 249, 245, 0.05);
        }
        .nav-logo { font-family: 'Cinzel', serif; font-size: 1.6rem; letter-spacing: 4px; color: var(--ivory); text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 35px; align-items: center; }
        .nav-links a { color: var(--ivory); text-decoration: none; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }
        .logout-btn { border: 1px solid var(--gold-accent); color: var(--gold-accent) !important; padding: 6px 16px; text-decoration: none; transition: var(--transition-smooth); }
        .logout-btn:hover { background: var(--gold-accent); color: var(--pine-green) !important; }

        /* --- IMMERSIVE HERO VIEW --- */
        .details-hero {
            height: 75vh;
            background: linear-gradient(rgba(15,30,25,0.2), rgba(15,30,25,0.85)), url('<?php echo $room['hero_img']; ?>') no-repeat center center/cover;
            display: flex; flex-direction: column; justify-content: flex-end; padding: 80px 8%; color: var(--ivory);
        }
        .hero-title-area { max-width: 800px; margin-bottom: 40px; }
        .hero-title-area span { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 5px; color: var(--gold-accent); text-transform: uppercase; }
        .hero-title-area h1 { font-family: 'Cinzel', serif; font-size: 4rem; font-weight: 400; letter-spacing: 4px; margin-top: 10px; text-transform: uppercase; }
        .hero-title-area p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.3rem; margin-top: 5px; opacity: 0.9; }

        /* --- DETAILED LAYOUT CORES --- */
        .details-grid-wrapper { padding: 100px 8%; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 90px; max-width: 1400px; margin: 0 auto; }
        
        /* Editorial Left Content */
        .editorial-narrative h2 { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 400; margin-bottom: 30px; line-height: 1.4; }
        .narrative-p { font-size: 1rem; line-height: 2; color: #444; margin-bottom: 40px; text-align: justify; }
        
        .amenities-list-box { margin-top: 50px; }
        .amenities-list-box h4 { font-family: 'Cinzel', serif; font-size: 1.1rem; letter-spacing: 2px; margin-bottom: 25px; border-bottom: 1px solid rgba(15,30,25,0.1); padding-bottom: 10px; }
        .amenity-tag-item { display: inline-block; background: var(--ivory-dark); padding: 10px 20px; font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; margin: 0 10px 12px 0; border-left: 2px solid var(--gold-accent); }

        /* Structural Specifications Right Panel Matrix */
        .specifications-panel-card { background: white; border: 1px solid rgba(15,30,25,0.06); padding: 45px; box-shadow: 0 30px 60px rgba(15,30,25,0.05); height: fit-content; position: sticky; top: 140px; }
        .specifications-panel-card h3 { font-family: 'Cinzel', serif; font-size: 1.2rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 30px; text-align: center; color: var(--forest-green); }
        
        .spec-row-item { display: flex; justify-content: space-between; padding: 18px 0; border-bottom: 1px solid rgba(15,30,25,0.08); font-size: 0.9rem; }
        .spec-row-item:last-of-type { border-bottom: none; margin-bottom: 30px; }
        .spec-label { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; color: var(--moss-green); font-weight: 600; }
        .spec-val { font-weight: 400; color: var(--pine-green); text-align: right; max-width: 60%; }

        /* Integrated Custom Fast Booking CTA Block */
        .fast-booking-cta-form h4 { font-family: 'Cinzel', serif; font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; color: var(--gold-accent); text-align: center; }
        .fast-field-group { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .fast-field-group div { display: flex; flex-direction: column; gap: 5px; }
        .fast-field-group label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; color: var(--moss-green); font-weight: 600; }
        .fast-field-group input { padding: 10px; border: 1px solid rgba(15,30,25,0.15); outline: none; font-size: 0.85rem; }
        .fast-submit-action { width: 100%; background: var(--forest-green); color: var(--ivory); border: none; padding: 15px; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
        .fast-submit-action:hover { background: var(--gold-accent); color: var(--pine-green); }

        /* --- VISUAL INTERIOR GALLERY GALLERY --- */
        .editorial-image-gallery { padding: 0 8% 120px 8%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 1400px; margin: 0 auto; }
        .gallery-frame { width: 100%; height: 480px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.08); }
        .gallery-frame img { width: 100%; height: 100%; object-fit: cover; transition: transform 1.2s cubic-bezier(0.25, 1, 0.5, 1); }
        .gallery-frame:hover img { transform: scale(1.04); }

        /* --- FOOTER Layout --- */
        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; border-top: 1px solid rgba(250, 249, 245, 0.04); color: var(--ivory); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); }

        @media (max-width: 992px) {
            .details-grid-wrapper { grid-template-columns: 1fr; gap: 60px; }
            .editorial-image-gallery { grid-template-columns: 1fr; gap: 20px; }
            .gallery-frame { height: 320px; }
            .hero-title-area h1 { font-size: 2.6rem; }
            .specifications-panel-card { position: relative; top: 0; padding: 25px; }
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
            <li><a href="faq.php">Policies &amp; FAQ</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <header class="details-hero">
        <div class="hero-title-area">
            <span><?php echo $room['subtitle']; ?></span>
            <h1><?php echo $room['title']; ?></h1>
            <p>Every structural layout engineered for complete physical stillness.</p>
        </div>
    </header>

    <main class="details-grid-wrapper">
        
        <article class="editorial-narrative">
            <h2>The Space Concept</h2>
            <p class="narrative-p"><?php echo $room['long_desc']; ?></p>
            
            <div class="amenities-list-box">
                <h4>Signature Luxuries Included</h4>
                <?php foreach($room['features'] as $feature): ?>
                    <span class="amenity-tag-item"><?php echo $feature; ?></span>
                <?php endforeach; ?>
            </div>
        </article>

        <aside class="specifications-panel-card">
            <h3>Sanctuary Overview</h3>
            
            <div class="spec-row-item">
                <span class="spec-label">Dimensions</span>
                <span class="spec-val"><?php echo $room['size']; ?></span>
            </div>
            <div class="spec-row-item">
                <span class="spec-label">Capacity</span>
                <span class="spec-val"><?php echo $room['capacity']; ?></span>
            </div>
            <div class="spec-row-item">
                <span class="spec-label">Environment</span>
                <span class="spec-val"><?php echo $room['view']; ?></span>
            </div>
            <div class="spec-row-item">
                <span class="spec-label">Bed Configuration</span>
                <span class="spec-val"><?php echo $room['bed']; ?></span>
            </div>

            <div style="margin: 40px 0 20px 0; border-top: 1px dashed rgba(15,30,25,0.15);"></div>

            <form class="fast-booking-cta-form" action="booking_payment.php" method="POST">
                <input type="hidden" name="room_type" value="<?php echo $room['db_value']; ?>">
                
                <h4>Secure This Space</h4>
                <div class="fast-field-group">
                    <div>
                        <label>Check In</label>
                        <input type="date" name="checkin" required>
                    </div>
                    <div>
                        <label>Check Out</label>
                        <input type="date" name="checkout" required>
                    </div>
                </div>
                <button type="submit" name="book" class="fast-submit-action">Submit Booking Inquiry</button>
            </form>
        </aside>
    </main>

    <section class="editorial-image-gallery">
        <div class="gallery-frame">
            <img src="<?php echo $room['gallery_1']; ?>" alt="Sanctuary Interior Frame 1">
        </div>
        <div class="gallery-frame">
            <img src="<?php echo $room['gallery_2']; ?>" alt="Sanctuary Interior Frame 2">
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

</body>
</html>