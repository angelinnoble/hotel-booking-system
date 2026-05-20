<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Legacy — The Rock Luxury Hotel & Spa</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pine-green: #0F1E19;    /* Deeper, more expensive black-green tint */
            --forest-green: #1C332A;  /* Deep rich green */
            --moss-green: #7D8F82;    /* Refined muted sage */
            --gold-accent: #C5A880;   /* Muted champagne gold leaf */
            --ivory: #FAF9F5;         /* Pure gallery linen white */
            --ivory-dark: #F2EFE9;    /* Shadow ivory */
            --transition-delicate: all 1.2s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* Smooth custom scroll container adjustments */
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--ivory);
            color: var(--pine-green);
            font-family: 'Montserrat', sans-serif;
            font-weight: 300;
            line-height: 1.75;
            overflow-x: hidden;
            letter-spacing: 0.5px;
        }

        /* --- IMMERSIVE INTERSECTION OBSERVER ANIMATION BASE CLASS --- */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: var(--transition-delicate);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Cinematic Hero Display Header */
        .hero-viewport {
            height: 100vh;
            width: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, rgba(15, 30, 25, 0.5), rgba(15, 30, 25, 0.95)), 
                        url('https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            background-attachment: fixed; /* Parallax effect */
            text-align: center;
            padding: 0 20px;
        }

        .hero-viewport::after {
            content: '';
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 150px;
            background: linear-gradient(to bottom, transparent, var(--ivory));
            z-index: 1;
        }

        .hero-viewport h1 {
            font-family: 'Cinzel', serif;
            font-size: 5rem;
            font-weight: 400;
            letter-spacing: 12px;
            text-transform: uppercase;
            color: var(--ivory);
            margin-bottom: 15px;
            text-shadow: 0 10px 30px rgba(0,0,0,0.2);
            opacity: 0;
            transform: scale(0.96);
            animation: elegantFadeIn 2s cubic-bezier(0.25, 1, 0.5, 1) forwards 0.3s;
        }

        .hero-viewport p {
            font-size: 0.9rem;
            color: var(--gold-accent);
            letter-spacing: 8px;
            text-transform: uppercase;
            font-weight: 400;
            opacity: 0;
            animation: elegantFadeIn 2s cubic-bezier(0.25, 1, 0.5, 1) forwards 0.8s;
        }

        @keyframes elegantFadeIn {
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Floating Minimal Return Utility Button */
        .back-nav-link {
            position: absolute;
            top: 40px;
            left: 5%;
            color: var(--ivory);
            text-decoration: none;
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            z-index: 10;
            transition: var(--transition-delicate);
            border-bottom: 1px solid rgba(250,249,245, 0.2);
            padding-bottom: 5px;
        }

        .back-nav-link:hover {
            color: var(--gold-accent);
            border-bottom-color: var(--gold-accent);
            letter-spacing: 5px;
        }

        /* Structural Grid Container Layout */
        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 4% 120px 4%;
            position: relative;
            z-index: 2;
        }

        /* Section Global Typography Header Setup */
        .luxury-section {
            margin-bottom: 160px;
        }

        .editorial-title {
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--gold-accent);
            margin-bottom: 25px;
            display: block;
            font-weight: 500;
        }

        /* Dynamic Split Asymmetric Layout for History Section */
        .history-split {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 100px;
            align-items: flex-start;
        }

        .history-main-text {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            line-height: 1.6;
            color: var(--pine-green);
            font-weight: 400;
        }

        .history-main-text em {
            font-style: italic;
            color: var(--forest-green);
        }

        .history-sub-text {
            font-size: 1rem;
            color: #555;
            line-height: 1.9;
            margin-top: 15px;
        }

        /* Centered Heritage Block Quote Style */
        .heritage-badge {
            background-color: var(--forest-green);
            color: var(--ivory);
            padding: 80px 60px;
            text-align: center;
            border-radius: 2px;
            position: relative;
            box-shadow: 0 30px 60px rgba(15,30,25,0.15);
        }

        .heritage-badge h2 {
            font-family: 'Cinzel', serif;
            font-size: 6rem;
            color: var(--gold-accent);
            font-weight: 300;
            line-height: 1;
        }

        .heritage-badge p {
            font-size: 0.9rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-top: 15px;
            color: var(--ivory-dark);
        }

        /* Symmetric Balance Block for Vision/Mission Components */
        .balance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        .luxury-card {
            background: linear-gradient(to bottom right, var(--ivory), var(--ivory-dark));
            padding: 60px;
            border: 1px solid rgba(15,30,25,0.04);
            border-top: 3px solid var(--gold-accent);
            transition: var(--transition-delicate);
        }

        .luxury-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
        }

        .luxury-card h3 {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            font-weight: 400;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .luxury-card p {
            font-size: 1rem;
            line-height: 1.8;
            color: #444;
        }

        /* Editorial Profile Layout Structure */
        .founders-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }

        .profile-card {
            background: var(--pine-green);
            color: var(--ivory);
            padding: 50px;
            border-radius: 1px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle ambient glow behind profile cards */
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(197,168,128,0.05) 0%, transparent 100%);
            pointer-events: none;
        }

        .profile-card h3 {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .profile-card .title-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--gold-accent);
            display: block;
            margin-bottom: 25px;
        }

        .profile-card p {
            font-size: 0.95rem;
            line-height: 1.8;
            color: var(--moss-green);
        }

        /* Clean List Layout for High-End Awards Statement */
        .awards-table {
            border-top: 1px solid rgba(15,30,25,0.1);
        }

        .awards-row-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 0;
            border-bottom: 1px solid rgba(15,30,25,0.1);
            transition: var(--transition-delicate);
        }

        .awards-row-item:hover {
            padding-left: 15px;
            background-color: rgba(15,30,25,0.01);
        }

        .awards-row-item h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 400;
            color: var(--pine-green);
        }

        .awards-row-item .year-stamp {
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            color: var(--gold-accent);
            letter-spacing: 2px;
            font-weight: 500;
        }

        /* Responsive Configuration Overrides */
        @media (max-width: 992px) {
            .history-split, .balance-grid, .founders-row {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            .hero-viewport h1 { font-size: 3rem; letter-spacing: 6px; }
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-nav-link">← Escape to Sanctuary</a>

    <header class="hero-viewport">
        <h1>The Legacy</h1>
        <p>Est. 2010 — Independent &amp; Sovereign</p>
    </header>

    <div class="wrapper">
        
        <section class="luxury-section history-split reveal">
            <div>
                <span class="editorial-title">Our History</span>
                <h2 class="history-main-text">
                    Carved straight out of rugged coastal metamorphic cliffsides, The Rock was born from an unyielding sketch in 2010: to construct a structure that <em>does not overwrite the earth, but complements its shape.</em>
                </h2>
                <p class="history-sub-text">
                    What required two full years of engineering by European master stone craftsmen stands today as a secluded, masterfully crafted paradise. We choose isolation over hyper-connectivity, providing complete privacy to global creative thinkers and travelers who value spatial stillness.
                </p>
            </div>
            
            <div class="heritage-badge reveal" style="transition-delay: 0.2s;">
                <h2>2010</h2>
                <p>Architectural Genesis</p>
            </div>
        </section>

        <section class="luxury-section balance-grid reveal">
            <div class="luxury-card">
                <h3>Our Vision</h3>
                <p>To perpetually redefine the limits of architectural tourism through protective conservation models—proving that high-end accommodations can harmoniously coexist with delicate ecological frameworks.</p>
            </div>
            <div class="luxury-card">
                <h3>Our Mission</h3>
                <p>To provide custom, hyper-private experiences that help guests slow down, step away from digital overstimulation, and reconnect with their natural rhythms.</p>
            </div>
        </section>

        <section class="luxury-section reveal">
            <span class="editorial-title">The Founders</span>
            <div class="founders-row">
                <div class="profile-card">
                    <h3>Julian Vance</h3>
                    <span class="title-label">Lead Architect &amp; Visionary</span>
                    <p>Julian spent the late nineties designing low-impact ecological reserves across the Scandinavian coastline. His minimalist philosophy shapes every structural line and cavernous suite inside The Rock.</p>
                </div>
                <div class="profile-card">
                    <h3>Elena Sterling</h3>
                    <span class="title-label">Principal Botanist &amp; Alchemist</span>
                    <p>Elena manages our organic baseline infrastructure. She converted our surrounding cliffs into sustainable terrace gardens that feed our culinary rooms and provide the native botanical bases for our spa rituals.</p>
                </div>
            </div>
        </section>

        <section class="luxury-section reveal">
            <span class="editorial-title">Laurels &amp; Accolades</span>
            <div class="awards-table">
                <div class="awards-row-item">
                    <h4>Global Biophilic Architecture Trophy</h4>
                    <span class="year-stamp">Winner — 2024</span>
                </div>
                <div class="awards-row-item">
                    <h4>World Luxury Hotel Awards: Pure Seclusion Category</h4>
                    <span class="year-stamp">Grand Prix — 2025</span>
                </div>
                <div class="awards-row-item">
                    <h4>Sustainable Resort Operations Standard (SROS)</h4>
                    <span class="year-stamp">Five Stars — 2026</span>
                </div>
            </div>
        </section>

    </div>

    <script>
        const scrollRevealEngine = () => {
            const elementsToReveal = document.querySelectorAll('.reveal');
            const windowHeight = window.innerHeight;

            elementsToReveal.forEach(element => {
                const elementTopPosition = element.getBoundingClientRect().top;
                // Elements trigger view transition when they sit 12% above viewport base
                if (elementTopPosition < windowHeight - (windowHeight * 0.12)) {
                    element.classList.add('active');
                }
            });
        };

        // Attach listener arrays to operational render events
        window.addEventListener('scroll', scrollRevealEngine);
        window.addEventListener('DOMContentLoaded', scrollRevealEngine);
    </script>
</body>
</html>