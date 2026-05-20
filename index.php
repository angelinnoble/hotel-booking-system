<?php
session_start();
include 'db.php';

$message_alert = "";

// 1. HANDLE REGISTRATION
if (isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmail = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($checkEmail->num_rows > 0) {
        $message_alert = "Email is already registered!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user_name'] = $name;
            header("Location: index.php");
            exit();
        } else {
            $message_alert = "Registration Error: " . $conn->error;
        }
    }
}

// 2. HANDLE LOGIN
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            $message_alert = "Invalid Password!";
        }
    } else {
        $message_alert = "No user found with that email!";
    }
}

// 3. HANDLE BOOKING INQUIRY
if (isset($_POST['book'])) {
    if (!isset($_SESSION['user_name'])) {
        $message_alert = "You must be logged in to book!";
    } else {
        $name = $_SESSION['user_name'];
        $room_type = $conn->real_escape_string($_POST['room_type']);
        $checkin = $conn->real_escape_string($_POST['checkin']);
        $checkout = $conn->real_escape_string($_POST['checkout']);

        $sql = "INSERT INTO bookings (name, room_type, checkin, checkout) VALUES ('$name', '$room_type', '$checkin', '$checkout')";
        if ($conn->query($sql) === TRUE) {
            $message_alert = "Room inquiry submitted successfully!";
        } else {
            $message_alert = "Booking Error: " . $conn->error;
        }
    }
}

// 4. HANDLE FEEDBACK
if (isset($_POST['feedback'])) {
    $name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $conn->real_escape_string($_POST['fb_name']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO feedback (name, message) VALUES ('$name', '$message')";
    if ($conn->query($sql) === TRUE) {
        $message_alert = "Thank you for your beautiful feedback!";
    } else {
        $message_alert = "Feedback Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Rock Luxury Hotel & Spa</title>
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
            --glass-white: rgba(249, 248, 243, 0.06);
            --glass-border: rgba(249, 248, 243, 0.12);
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
            overflow-x: hidden;
            font-weight: 300;
        }

        /* --- AUTH REGISTER/LOGIN SECTION --- */
        .auth-page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(15, 30, 25, 0.7), rgba(15, 30, 25, 0.9)), 
                        url('https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            animation: containerAppear 1.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        @keyframes containerAppear {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-header h1 {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--ivory);
        }

        .brand-header p {
            color: var(--gold-accent);
            font-size: 0.8rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .glass-card {
            background: var(--glass-white);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 4px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .toggle-tabs {
            display: flex;
            margin-bottom: 35px;
            border-bottom: 1px solid rgba(250, 249, 245, 0.1);
            padding-bottom: 10px;
        }

        .tab-btn {
            background: none;
            border: none;
            color: rgba(250, 249, 245, 0.4);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            padding: 5px 20px;
            position: relative;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }

        .tab-btn.active { color: var(--ivory); }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -11px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gold-accent);
        }

        .slider-wrapper {
            display: flex;
            width: 200%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slider-wrapper.slide-active { transform: translateX(-50%); }

        .auth-form { width: 50%; }

        .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 10px 12px 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(250, 249, 245, 0.2);
            outline: none;
            color: var(--ivory);
            font-size: 0.95rem;
        }

        .input-box label {
            position: absolute;
            top: 12px;
            left: 0;
            color: rgba(250, 249, 245, 0.4);
            pointer-events: none;
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }

        .input-box input:focus ~ label,
        .input-box input:not(:placeholder-shown) ~ label {
            top: -12px;
            font-size: 0.75rem;
            color: var(--gold-accent);
        }

        .input-box input:focus { border-bottom-color: var(--ivory); }

        .primary-btn {
            width: 100%;
            padding: 15px;
            background: var(--ivory);
            border: none;
            color: var(--pine-green);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 2px;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .primary-btn:hover {
            background: var(--gold-accent);
            color: var(--ivory);
        }


        /* --- CINEMATIC HOME SYSTEM --- */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 6%;
            background: linear-gradient(to bottom, rgba(15, 30, 25, 0.9), transparent);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .nav-logo {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            font-weight: 400;
            letter-spacing: 4px;
            color: var(--ivory);
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

        /* Multi-Image Hero Slider Viewport */
        .hero-slider-viewport {
            height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .slide-track {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
        }

        .hero-slide {
            position: absolute;
            width: 100%; height: 100%;
            opacity: 0;
            transition: opacity 1.8s cubic-bezier(0.25, 1, 0.5, 1);
            background-size: cover;
            background-position: center;
        }

        .hero-slide::before {
            content: '';
            position: absolute;
            width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(15,30,25,0.3), rgba(15,30,25,0.85));
        }

        .hero-slide.active { opacity: 1; }

        .hero-center-content {
            position: relative;
            z-index: 5;
            text-align: center;
            color: var(--ivory);
            max-width: 900px;
            padding: 0 20px;
            margin-bottom: 40px;
        }

        .hero-center-content h2 {
            font-family: 'Cinzel', serif;
            font-size: 4.5rem;
            letter-spacing: 12px;
            text-transform: uppercase;
            font-weight: 400;
            margin-bottom: 10px;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .hero-center-content p {
            font-size: 0.9rem;
            letter-spacing: 6px;
            color: var(--gold-accent);
            text-transform: uppercase;
        }

        /* Integrated High-End Horizontal Quick Booking Form Bar */
        .quick-booking-bar {
            position: relative;
            z-index: 5;
            width: 88%;
            max-width: 1100px;
            background: rgba(15, 30, 25, 0.65);
            border: 1px solid rgba(250, 249, 245, 0.15);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 25px 35px;
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr auto;
            gap: 25px;
            align-items: end;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4);
            margin-top: -50px;
            margin-left: auto; margin-right: auto;
        }

        .quick-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .quick-field label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--gold-accent);
            font-weight: 500;
        }

        .quick-field select, .quick-field input {
            background: rgba(250, 249, 245, 0.05);
            border: 1px solid rgba(250, 249, 245, 0.2);
            padding: 12px;
            color: var(--ivory);
            outline: none;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: border-color 0.3s;
        }

        .quick-field select option { background: var(--pine-green); color: var(--ivory); }
        .quick-field select:focus, .quick-field input:focus { border-color: var(--gold-accent); }

        .quick-submit-btn {
            background: var(--gold-accent);
            color: var(--pine-green);
            border: none;
            padding: 14px 35px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: var(--transition-smooth);
            height: 45px;
        }
        .quick-submit-btn:hover { background: var(--ivory); color: var(--pine-green); }

        /* Structural Global Layout Sections */
        section { padding: 140px 8%; }

        /* Luxury Split Editorial Overview Layout Block */
        .overview-editorial-section {
            background-color: var(--ivory);
            color: var(--pine-green);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 90px;
            align-items: center;
        }

        .editorial-tag {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 5px;
            color: var(--gold-accent);
            text-transform: uppercase;
            display: block;
            margin-bottom: 20px;
        }

        .editorial-header {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            line-height: 1.5;
            font-weight: 400;
            margin-bottom: 30px;
        }

        .editorial-header em { font-style: italic; color: var(--forest-green); }

        .overview-paragraph {
            font-size: 0.95rem;
            line-height: 1.9;
            color: #444;
            margin-bottom: 25px;
        }

        .discover-link {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: var(--pine-green);
            text-decoration: none;
            text-transform: uppercase;
            border-bottom: 1px solid var(--pine-green);
            padding-bottom: 5px;
            transition: var(--transition-smooth);
        }
        .discover-link:hover { color: var(--gold-accent); border-color: var(--gold-accent); letter-spacing: 5px; }

        .editorial-frame {
            background: var(--forest-green);
            color: var(--ivory);
            padding: 70px 50px;
            position: relative;
            box-shadow: 0 30px 60px rgba(15,30,25,0.1);
        }

        .editorial-frame h4 {
            font-family: 'Cinzel', serif;
            font-size: 5rem;
            color: var(--gold-accent);
            line-height: 1;
            font-weight: 300;
        }

        .editorial-frame p {
            font-size: 0.75rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 10px;
            color: var(--moss-green);
        }

        /* Unified Engagement/Feedback Seemless Panel */
        .reflections-panel {
            background-color: var(--forest-green);
            color: var(--ivory);
            max-width: 800px;
            margin: 0 auto 100px auto;
            padding: 60px;
            border-top: 3px solid var(--gold-accent);
        }

        .panel-title { 
            font-family: 'Cinzel', serif; 
            font-size: 1.8rem; 
            margin-bottom: 30px; 
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(250,249,245, 0.08); 
            padding-bottom: 15px; 
        }

        .dark-form .form-group { margin-bottom: 25px; }
        .dark-form label { display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; color: var(--gold-accent); }
        
        .dark-form textarea {
            width: 100%; padding: 15px; background: rgba(15, 30, 25, 0.4);
            border: 1px solid rgba(250, 249, 245, 0.15); color: var(--ivory); outline: none;
            font-size: 0.9rem; line-height: 1.6; transition: border-color 0.3s;
        }
        .dark-form textarea:focus { border-color: var(--gold-accent); }

        .secondary-btn {
            background: transparent; color: var(--ivory); border: 1px solid var(--ivory);
            padding: 14px 40px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; cursor: pointer; letter-spacing: 2px; transition: 0.3s;
        }
        .secondary-btn:hover { background: var(--ivory); color: var(--forest-green); }

        footer { background-color: var(--pine-green); padding: 80px 8% 40px 8%; border-top: 1px solid rgba(250, 249, 245, 0.04); color: var(--ivory); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; }
        .footer-links h6 { font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 25px; color: var(--gold-accent); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--moss-green); text-decoration: none; font-size: 0.85rem; transition: color 0.3s; }
        .footer-links a:hover { color: var(--ivory); }
        .footer-bottom { text-align: center; margin-top: 60px; font-size: 0.75rem; color: var(--moss-green); letter-spacing: 1px; }

        @media (max-width: 992px) {
            .quick-booking-bar { grid-template-columns: 1fr; gap: 15px; padding: 25px; margin-top: -20px;}
            .overview-editorial-section { grid-template-columns: 1fr; gap: 50px; }
            .hero-center-content h2 { font-size: 2.8rem; letter-spacing: 6px; }
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
        }
    </style>
</head>
<body>

    <?php if (!isset($_SESSION['user_name'])): ?>
    
    <div class="auth-page">
        <div class="auth-container">
            <div class="brand-header">
                <h1>The Rock</h1>
                <p>Luxury Hotel & Spa</p>
            </div>
            
            <div class="glass-card">
                <div class="toggle-tabs">
                    <button class="tab-btn active" id="loginTab" onclick="toggleAuth('login')">Login</button>
                    <button class="tab-btn" id="registerTab" onclick="toggleAuth('register')">Register</button>
                </div>
                
                <div class="slider-wrapper" id="sliderWrapper">
                    <form class="auth-form" method="POST" action="index.php">
                        <div class="input-box">
                            <input type="email" name="email" required placeholder=" " autocomplete="off">
                            <label>Email Address</label>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" required placeholder=" ">
                            <label>Password</label>
                        </div>
                        <button type="submit" name="login" class="primary-btn">SIGN IN</button>
                    </form>

                    <form class="auth-form" method="POST" action="index.php">
                        <div class="input-box">
                            <input type="text" name="name" required placeholder=" " autocomplete="off">
                            <label>Full Name</label>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" required placeholder=" " autocomplete="off">
                            <label>Email Address</label>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" required placeholder=" ">
                            <label>Password</label>
                        </div>
                        <button type="submit" name="register" class="primary-btn">CREATE ACCOUNT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>

    <div>
     <nav>
            <div class="nav-logo">The Rock</div>
            <ul class="nav-links">
                <li><a href="about.php">About Us</a></li>
                <li><a href="accommodation.php">Accommodation</a></li>
                <li><a href="activities.php">Activities &amp; Experience</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="faq.php">Policies &amp; FAQ</a></li>
                <li><span style="color: var(--gold-accent); font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; margin-left: 15px;">Hi, <?php echo $_SESSION['user_name']; ?></span></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </nav>
        <header class="hero-slider-viewport">
            <div class="slide-track">
                <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80');"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1920&q=80');"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1920&q=80');"></div>
            </div>

            <div class="hero-center-content">
                <h2>An Oasis of Seclusion</h2>
                <p>Bespoke Luxury Carved in Metamorphic Cliffside</p>
            </div>
        </header>

        <div class="quick-booking-bar">
            <form action="index.php" method="POST" style="display: contents;">
                <div class="quick-field">
                    <label>Accommodation Tier</label>
                    <select name="room_type" required>
                        <option value="Clifftop Suite">Clifftop Suite</option>
                        <option value="Ocean View Villa">Ocean View Villa</option>
                        <option value="Forest Retreat Room">Forest Retreat Room</option>
                    </select>
                </div>
                <div class="quick-field">
                    <label>Check In</label>
                    <input type="date" name="checkin" required>
                </div>
                <div class="quick-field">
                    <label>Check Out</label>
                    <input type="date" name="checkout" required>
                </div>
                <button type="submit" name="book" class="quick-submit-btn">Reserve Space</button>
            </form>
        </div>

        <section id="overview" class="overview-editorial-section">
            <div>
                <span class="editorial-tag">The Sanctuary Overview</span>
                <h3 class="editorial-header">
                    A structure designed to co-exist with nature, <em>elevating the raw lines of the cliffside landscape.</em>
                </h3>
                <p class="overview-paragraph">
                    Carved directly into pristine coastal cliffsides, The Rock pairs dramatic natural features with polished architectural precision. We offer a rare haven of absolute privacy, catering exclusively to independent global travelers who prioritize environmental harmony and spatial stillness.
                </p>
                <a href="about.php" class="discover-link">Our Heritage Legacy →</a>
            </div>
            
            <div class="editorial-frame">
                <h4>2010</h4>
                <p>Established & Sovereign</p>
            </div>
        </section>

        <section id="reviews" style="background-color: var(--ivory-dark);">
            <div class="reflections-panel">
                <h3 class="panel-title">Guest Reflections</h3>
                <form class="dark-form" method="POST" action="index.php">
                    <div class="form-group">
                        <label>Share Your Experience</label>
                        <textarea name="message" rows="6" placeholder="Document your journey within our sanctuary sanctuaries..." required></textarea>
                    </div>
                    <button type="submit" name="feedback" class="secondary-btn">Submit Review</button>
                </form>
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
                        <li><a href="#overview">Overview</a></li>
                        <li><a href="#reviews">Reviews</a></li>
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
    </div>
    <?php endif; ?>

    <?php if(!empty($message_alert)): ?>
    <script>alert("<?php echo $message_alert; ?>");</script>
    <?php endif; ?>

    <script>
        // Form slide toggle script controller
        function toggleAuth(target) {
            const wrapper = document.getElementById('sliderWrapper');
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');

            if (target === 'register') {
                wrapper.classList.add('slide-active');
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
            } else {
                wrapper.classList.remove('slide-active');
                registerTab.classList.remove('active');
                loginTab.classList.add('active');
            }
        }

        // Cinematic automatic slider transition logic loop
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.hero-slide');
            if(slides.length === 0) return;
            
            let currentSlideIndex = 0;
            
            setInterval(() => {
                slides[currentSlideIndex].classList.remove('active');
                currentSlideIndex = (currentSlideIndex + 1) % slides.length;
                slides[currentSlideIndex].classList.add('active');
            }, 5000); // Transitions background image layouts automatically every 5 seconds
        });
    </script>
</body>
</html>