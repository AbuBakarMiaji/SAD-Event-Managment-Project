<?php
error_reporting(0);
session_start();

$host = "localhost";
$user = "root";
$password = "";
$db = "event_managements";
$data = mysqli_connect($host, $user, $password, $db);

// Fetch packages from DB
$sql_wed   = "SELECT * FROM packages WHERE category='wedding' LIMIT 1";
$sql_bir   = "SELECT * FROM packages WHERE category='birthday' LIMIT 1";
$sql_conc  = "SELECT * FROM packages WHERE category='conference' LIMIT 1";
$sql_corp  = "SELECT * FROM packages WHERE category='corporate' LIMIT 1";
$sql_pic   = "SELECT * FROM packages WHERE category='other' LIMIT 1";
$sql_all   = "SELECT * FROM packages WHERE status='active'";

$result_wed  = mysqli_query($data, $sql_wed);
$result_bir  = mysqli_query($data, $sql_bir);
$result_conc = mysqli_query($data, $sql_conc);
$result_corp = mysqli_query($data, $sql_corp);
$result_pic  = mysqli_query($data, $sql_pic);
$result_all  = mysqli_query($data, $sql_all);

// Session messages
if (!empty($_SESSION['message'])) {
    $msg = $_SESSION['message'];
    echo "<script>alert('$msg');</script>";
    unset($_SESSION['message']);
}
if (!empty($_SESSION['wronglogin'])) {
    $wronglogin = $_SESSION['wronglogin'];
    echo "<script>alert('$wronglogin');</script>";
    unset($_SESSION['wronglogin']);
}

// Check login session
$loggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';
$userName = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLANPro - Event Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ===== RESET & BASE ===== */
        * { margin:0; padding:0; box-sizing:border-box; font-family:"Nunito", sans-serif; }
        html { scroll-behavior: smooth; }
        :root {
            --bg: #ad9e9e;
            --bg2: #111;
            --primary: #ffa500;
            --primary-dark: #a8a08c;
            --white: #fff;
            --title-color: #333346;
            --gray: #8a8a8d;
            --border: rgba(255,255,255,0.1);
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width:8px; }
        ::-webkit-scrollbar-track { background:#111; }
        ::-webkit-scrollbar-thumb { background:var(--primary); border-radius:4px; }

        /* ===== HEADER ===== */
        header {
            position: fixed; top:0; left:0; right:0; z-index:1000;
            display: flex; align-items:center; justify-content:space-between;
            padding: 20px 5%;
            background: #333333;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s;
        }
        header.scrolled { padding:14px 5%; background: #333333; }

        #menu-ber {
            font-size:22px; color:var(--white); cursor:pointer;
            display:none;
        }

        .logo {
            font-size:26px; font-weight:800; color:var(--white);
            text-decoration:none; letter-spacing:2px;
        }
        .logo span { color:#ffa115; }

        header .navbar a {
            font-size:14px; font-weight:600; color:var(--light);
            margin-left:28px; text-decoration:none; text-transform:uppercase;
            letter-spacing:0.5px; transition:color 0.2s;
        }
        header .navbar a:hover { color:var(--primary); }

        .icons { display:flex; align-items:center; gap:18px; }
        .icons i { font-size:18px; color:var(--white); cursor:pointer; transition:color 0.2s; }
        .icons i:hover { color:var(--primary); }

        .search-bar-container {
            position:absolute; top:100%; left:0; right:0;
            padding:14px 5%;
            background:rgba(0,0,0,0.95);
            display:none; align-items:center; gap:12px;
            border-bottom:1px solid var(--border);
        }
        .search-bar-container.active { display:flex; }
        .search-bar-container input {
            flex:1; padding:10px 16px; border-radius:6px;
            border:1px solid var(--primary); background:#1a1a1a;
            color:var(--white); font-size:14px; outline:none;
        }
        .search-bar-container label { color:var(--primary); font-size:18px; cursor:pointer; }

        /* ===== LOGIN FORM ===== */
        .login-form-container {
            position:fixed; top:0; left:0; right:0; bottom:0; z-index:2000;
            background:rgba(0,0,0,0.85); backdrop-filter:blur(6px);
            display:flex; align-items:center; justify-content:center;
            opacity:0; pointer-events:none; transition:opacity 0.3s;
        }
        .login-form-container.active { opacity:1; pointer-events:all; }
        .login-form-container form {
            background:#1a1a1a; border:1px solid var(--border);
            border-radius:16px; padding:40px; width:90%; max-width:420px;
            position:relative;
        }
        #form-close {
            position:absolute; top:16px; right:20px;
            font-size:20px; color:var(--gray); cursor:pointer;
            transition:color 0.2s;
        }
        #form-close:hover { color:var(--primary); }
        .login-form-container h3 {
            font-size:24px; color:var(--white); margin-bottom:24px;
            font-weight:700; border-bottom:2px solid var(--primary);
            padding-bottom:10px;
        }
        .login-form-container .box {
            width:100%; padding:12px 16px; margin-bottom:14px;
            background:#111; border:1px solid var(--border);
            border-radius:8px; color:var(--white); font-size:14px; outline:none;
            transition:border-color 0.2s;
        }
        .login-form-container .box:focus { border-color:var(--primary); }
        .login-form-container .box::placeholder { color:var(--gray); }
        .login-form-container .btn {
            width:100%; padding:13px; background:var(--primary);
            color:#000; font-weight:700; border:none; border-radius:8px;
            font-size:15px; cursor:pointer; margin-top:6px;
            transition:background 0.2s;
        }
        .login-form-container .btn:hover { background:var(--primary-dark); }
        .login-form-container p {
            font-size:13px; color:var(--gray); margin-top:12px;
        }
        .login-form-container p a { color:var(--primary); text-decoration:none; }
        .login-form-container label[for="remember"] {
            font-size:13px; color:var(--gray); margin-left:6px;
        }
        .login-error {
            background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3);
            border-radius:8px; padding:10px 14px; color:#f87171;
            font-size:13px; margin-bottom:14px; display:none;
        }

        /* ===== BUTTONS ===== */
        .btn {
            display:inline-block; padding:11px 28px;
            background:var(--primary); color:#000;
            font-weight:700; font-size:14px; border-radius:6px;
            text-decoration:none; text-transform:uppercase;
            letter-spacing:0.5px; cursor:pointer; border:none;
            transition:all 0.2s;
        }
        .btn:hover { background-color:var(--white); border:1px solid var(--primary); 
         transform:translateY(-1px); }

        /* ===== HEADINGS ===== */
        h1.heading {
            text-align:center; font-size:clamp(36px,6vw,60px);
            font-weight:900; margin-bottom:50px;
            text-transform:uppercase; letter-spacing:4px;
        }
        h1.heading span {
            color:var(--primary); display:inline-block;
            transition:color 0.2s, transform 0.2s;
        }
        /* h1.heading span:hover { color:var(--primary); transform:translateY(-4px); } */

        /* ===== HOME / HERO ===== */
        .home {
            position:relative; min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            overflow:hidden;
        }
        .video-container {
            position:absolute; inset:0; z-index:0;
        }
        .video-container::after {
            content:''; position:absolute; inset:0;
            /* background:linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.7) 100%); */
        }
        .video-container video {
            width:100%; height:100%; object-fit:cover;
        }
        .home .content {
            position:relative; z-index:2; text-align:center;
            padding:0 20px;
        }
        .home .content h3 {
            font-size:clamp(28px,5vw,64px); font-weight:900;
            color:var(--white); line-height:1.15; margin-bottom:16px;
            text-shadow:0 2px 20px rgba(0,0,0,0.5);
        }
        .home .content h3 span { color:var(--primary); }
        .home .content p {
            font-size:clamp(14px,2vw,20px); color:rgba(255,255,255,0.85);
            margin-bottom:32px;
        }
        .controls {
            position:absolute; bottom:32px; left:50%; transform:translateX(-50%);
            z-index:3; display:flex; gap:10px;
        }
        .vid-btn {
            width:12px; height:12px; border-radius:50%;
            background:rgba(255,255,255,0.4); cursor:pointer;
            border:2px solid transparent; transition:all 0.3s;
        }
        .vid-btn.active { background:var(--primary); border-color:var(--primary); transform:scale(1.3); }

        /* ===== SECTIONS BASE ===== */
        section { padding:80px 5%; background-color:#FFFFFF; }
        section:nth-child(even) { background-color:#f0f0f0; }

        /* ===== SERVICES ===== */
        .services .box-container {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
            gap:24px;
        }
        .services .box {
            background:#ffffff; border:2px solid #f0f0f0;
            box-shadow:0 8px 24px rgba(200,169,110,0.1);
            border-radius:12px; padding:32px 24px; text-align:center;
            transition:all 0.3s;
        }
        .services .box:hover {
            border-color:var(--primary);
             transform:translateY(-6px);
            box-shadow:0 12px 40px rgba(200,169,110,0.15);
        }
        .services .box i {
            font-size:40px; color:var(--primary); margin-bottom:16px;
            display:block;
        }
        .services .box h3 {
            font-size:18px; color:#333346; margin-bottom:12px;
            font-weight:700; text-transform:capitalize;
        }
        .services .box p { font-size:14px; color:var(--gray); line-height:1.7; }

        /* ===== PACKAGES ===== */
        .packages .box-container {
            display:grid; grid-template-columns:repeat(auto-fill,minmax(330px,1fr));
            gap:28px;
        }
        .packages .box {
            background:#fff; border:1px solid var(--border);
            box-shadow:0 8px 24px rgba(200,169,110,0.1);
            border-radius:14px; overflow:hidden; transition:all 0.3s;
        }
        .packages .box:hover {
            transform:translateY(-6px);
            box-shadow:0 16px 48px rgba(200,169,110,0.2);
            border-color:var(--primary);
        }
        .packages .box img {
            width:100%; height:200px; object-fit:cover;
            transition:transform 0.4s;
        }
        .packages .box:hover img { transform:scale(1.05); }
        .packages .box .content { padding:20px; }
        .packages .box .content h3 {
            font-size:20px; color:var(--title-color); font-weight:700;
            margin-bottom:14px; text-transform:capitalize;
            border-left:3px solid var(--primary); padding-left:10px;
        }
        .packages .box ul { list-style:none; margin-bottom:14px; }
        .packages .box ul p {
            font-size:13px; color:var(--gray); padding:4px 0;
            display:flex; align-items:center; gap:8px;
        }
        .packages .box ul p::before {
            content:'✓'; color:var(--primary); font-weight:700;
        }
        .packages .box .price {
            font-size:22px; font-weight:800; color:var(--primary);
            margin-bottom:16px;
        }
        .packages .box .price span {
            font-size:14px; color:var(--gray);
            text-decoration:line-through; margin-left:8px; font-weight:400;
        }
        /* Emoji fallback box for packages without images */
        .pkg-emoji-box {
            height:200px; display:flex; align-items:center; justify-content:center;
            font-size:72px;
            background:linear-gradient(135deg,#1a1a1a,#2a2a2a);
        }

        /* ===== GALLERY ===== */
        .gallery .box-container {
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap:16px;
        }
        .gallery .box {
            position:relative; border-radius:10px; overflow:hidden;
            aspect-ratio:4/3;
        }
        .gallery .box img {
            width:100%; height:100%; object-fit:cover;
            transition:transform 0.4s;
        }
        .gallery .box:hover img { transform:scale(1.08); }
        .gallery .box .content {
            position:absolute; inset:0;
            background:linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);
            display:flex; flex-direction:column; justify-content:flex-end;
            padding:20px;
            opacity:0; transition:opacity 0.3s;
        }
        .gallery .box:hover .content { opacity:1; }
        .gallery .box .content h3 { color:var(--white); font-size:16px; margin-bottom:6px; }
        .gallery .box .content p { color:var(--white); font-size:13px; margin-bottom:10px; }
        /* Emoji gallery fallback */
        .gallery-emoji {
            width:100%; height:100%;
            background:linear-gradient(135deg,#1a1a1a,#222);
            display:flex; align-items:center; justify-content:center;
            font-size:48px;
        }

        /* ===== REVIEWS ===== */
        .review { overflow:hidden; }
        .review-slider { padding:20px 0 40px; }
        .review .box {
            background:var(--white); border:1px solid var(--border);
            border-radius:14px; padding:28px; text-align:center;
            margin:0 10px; transition:border-color 0.3s;
        }
        .review .box:hover { border-color:var(--primary); }
        .review .box img {
            width:70px; height:70px; border-radius:50%;
            object-fit:cover; border:3px solid var(--primary);
            margin-bottom:14px;
        }
        .review-avatar {
            width:70px; height:70px; border-radius:50%;
            background:linear-gradient(135deg,var(--primary),var(--white));
            display:flex; align-items:center; justify-content:center;
            font-size:24px; font-weight:700; color:#000;
            margin:0 auto 14px;
            border:3px solid var(--primary);
        }
        .review .box h3 { color:var(--title-color); font-size:16px; font-weight:700; margin-bottom:8px; }
        .review .box p { color:var(--gray); font-size:13px; line-height:1.7; margin-bottom:14px; }
        .review .box .stars i { color:var(--primary); font-size:14px; }
        .swiper-pagination-bullet { background:var(--primary) !important; }

        /* ===== CONTACT ===== */
        .contact .container {
            max-width:640px; margin:0 auto;
            background:#F0F0F0; border:1px solid var(--border);
            border-radius:16px; padding:40px;
        }
        .contact label {
            display:block; font-size:13px; font-weight:600;
            color:var(--title-color); 
            margin-bottom:6px; text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .contact input, .contact textarea {
            width:100%; padding:12px 16px; margin-bottom:18px;
            background:var(--white); border:1px solid var(--border);
            border-radius:8px; color:var(--white); font-size:14px;
            outline:none; transition:border-color 0.2s; font-family:inherit;
        }
        .contact input:focus, .contact textarea:focus { border-color:var(--primary); }
        .contact input::placeholder, .contact textarea::placeholder { color:var(--gray); }
        .contact input[type="submit"] {
            background:var(--primary); color:#000; font-weight:700;
            font-size:15px; cursor:pointer; text-transform:uppercase;
            letter-spacing:0.5px; border:none; border-radius:8px;
            transition:all 0.2s; margin-bottom:0;
        }
        .contact input[type="submit"]:hover { background:var(--primary-dark); transform:translateY(-1px); }

        /* ===== FOOTER ===== */
        .footer {
            background:#333333; border-top:1px solid var(--border);
            padding:60px 5% 30px;
        }
        .footer .box-container {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:32px; margin-bottom:40px;
        }
        .footer .box h3 {
            font-size:18px; color:var(--primary); font-weight:700;
            margin-bottom:16px; text-transform:uppercase; letter-spacing:1px;
        }
        .footer .box p {
            font-size:13px; color:var(--white); line-height:1.8;
            margin-bottom:8px; display:flex; align-items:flex-start; gap:8px;
        }
        .footer .box p i { color:var(--primary); margin-top:2px; flex-shrink:0; }
        .footer .box a {
            display:block; font-size:15px; color:var(--white);
            text-decoration:none; margin-bottom:8px; transition:color 0.2s;
        }
        .footer .box a:hover { color:var(--primary); padding-left:4px; }
        .social-icons { display:flex; gap:12px; margin-top:8px; }
        .social-icons a {
            
            display:flex; align-items:center; justify-content:center;
            color:var(--white); font-size:15px; text-decoration:none;
            
        }
        .social-icons a:hover {
    
             border-color:var(--primary);
            
             
        }
        .credit {
            text-align:center; font-size:13px; color:var(--white);
            font-weight:400; border-top:1px solid var(--border);
            padding-top:24px;
        }
        .credit span { color:var(--primary); font-weight:700; }

        /* ===== USER MENU ===== */
        .user-menu {
            position:relative;
        }
        .user-menu .dropdown {
            position:absolute; top:calc(100% + 12px); right:0;
            background:#1a1a1a; border:1px solid var(--border);
            border-radius:10px; padding:8px; min-width:160px;
            display:none; z-index:999;
        }
        .user-menu:hover .dropdown { display:block; }
        .user-menu .dropdown a {
            display:flex; align-items:center; gap:8px;
            padding:9px 14px; border-radius:6px; font-size:13px;
            color:var(--light); text-decoration:none; transition:all 0.2s;
        }
        .user-menu .dropdown a:hover { background:#2a2a2a; color:var(--primary); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            header .navbar { display:none; }
            header .navbar.open {
                display:flex; flex-direction:column;
                position:absolute; top:100%; left:0; right:0;
                background:rgba(0,0,0,0.97); padding:20px 5%;
                border-bottom:1px solid var(--border);
            }
            header .navbar.open a { margin:8px 0; }
            #menu-ber { display:block; }
            .gallery .box-container { grid-template-columns:repeat(2,1fr); }
            h1.heading { font-size:32px; }
        }
        @media (max-width:480px) {
            .gallery .box-container { grid-template-columns:1fr; }
        }
    </style>
</head>
<body style="background:#0a0a0a; color:#eee;">

<!-- ===== HEADER ===== -->
<header id="main-header">
    <div id="menu-ber" class="fas fa-bars"></div>

    <a href="index.php" class="logo"><span>PLAN</span>Pro</a>

    <nav class="navbar" id="main-nav">
        <a href="#home">Home</a>
        <a href="#services">Services</a>
        <a href="#packages">Packages</a>
        <a href="#gallery">Gallery</a>
        <a href="#review">Review</a>
        <a href="#contact">Contact</a>
    </nav>


    <!-- Login Show Section after a user login  -->

    <div class="icons">
        <i class="fas fa-search" id="search-btn"></i>
        <?php if ($loggedIn): ?>
        <div class="user-menu">
            <i class="fas fa-user" style="color:var(--primary);"></i>
            <div class="dropdown">
                <a href="#"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userName); ?></a>
                <?php
                $dashMap = ['admin'=>'pages/admin-dashboard.html','customer'=>'pages/customer-dashboard.html','manager'=>'pages/manager-dashboard.html','staff'=>'pages/staff-dashboard.html'];
                $dash = $dashMap[$userRole] ?? '#';
                ?>
                <a href="<?php echo $dash; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <?php else: ?>
        <i class="fas fa-user" id="login-btn"></i>
        <?php endif; ?>
    </div>

    <form action="" class="search-bar-container" id="search-bar-form">
        <input type="search" id="search-br" placeholder="Search here...">
        <label for="search-br" class="fas fa-search"></label>
    </form>
</header>

<!-- ===== LOGIN FORM ===== -->
<?php if (!$loggedIn): ?>
<div class="login-form-container" id="login-form-container">
    <i class="fas fa-times" id="form-close"></i>
    <form id="login-form">
        <h3>Login</h3>
        <div class="login-error" id="login-error"></div>
        <input type="email" class="box" name="email" id="login-email" placeholder="Enter your email" required>
        <input type="password" class="box" name="password" id="login-password" placeholder="Enter your password" required>
        <input type="submit" value="Login Now" class="btn">
        <br><br>
        <input type="checkbox" id="remember">
        <label for="remember">Remember me</label>
        <p>Forget password? <a href="#">Click here</a></p>
        <p>Don't have an account? <a href="#" id="show-register">Register now</a></p>
        <div style="margin-top:14px;padding:12px;background:rgba(200,169,110,0.08);border:1px solid rgba(200,169,110,0.2);border-radius:8px;font-size:12px;color:var(--gray);">
            <strong style="color:var(--primary);">Demo:</strong> admin@eventpro.com / password
        </div>
    </form>
</div>

<!-- REGISTER FORM -->
<div class="login-form-container" id="register-form-container">
    <i class="fas fa-times" id="reg-close"></i>
    <form id="register-form">
        <h3>Register</h3>
        <div class="login-error" id="reg-error"></div>
        <div class="login-error" id="reg-success" style="background:rgba(16,185,129,0.15);border-color:rgba(16,185,129,0.3);color:#34d399;"></div>
        <input type="text" class="box" id="reg-name" placeholder="Full Name" required>
        <input type="email" class="box" id="reg-email" placeholder="Email Address" required>
        <input type="tel" class="box" id="reg-phone" placeholder="Phone Number">
        <input type="password" class="box" id="reg-password" placeholder="Password (min 6 chars)" required>
        <input type="submit" value="Create Account" class="btn">
        <p style="margin-top:14px;">Already have an account? <a href="#" id="show-login">Login</a></p>
    </form>
</div>
<?php endif; ?>

<!-- ===== HOME / HERO ===== -->
<section class="home" id="home">
    <div class="content">
        <h3>Every Event Is <span>Worthwhile</span></h3>
        <p>Discover unforgettable moments with us — weddings, birthdays, corporate & more</p>
        <a href="#packages" class="btn">Discover More</a>
    </div>
    <div class="controls">
        <span class="vid-btn active" data-src="images/vid-1.mp4"></span>
        <span class="vid-btn" data-src="images/vid-2.mp4"></span>
        <span class="vid-btn" data-src="images/vid-3.mp4"></span>
        <span class="vid-btn" data-src="images/vid-4.mp4"></span>
        <span class="vid-btn" data-src="images/vid-5.mp4"></span>
    </div>
    <div class="video-container">
        <video src="images/vid-1.mp4" id="video-slider" loop autoplay muted playsinline></video>
    </div>
</section>

<!-- ===== SERVICES ===== -->
<section class="services" id="services">
    <h1 class="heading">
        <span>s</span><span>e</span><span>r</span><span>v</span><span>i</span><span>c</span><span>e</span><span>s</span>
    </h1>
    <div class="box-container">
        <div class="box">
            <i class="fas fa-hotel"></i>
            <h3>Event Venue Selection</h3>
            <p>We help you find the perfect venue for your event by matching your budget, location, capacity, and style.</p>
        </div>
        <div class="box">
            <i class="fas fa-envelope-open-text"></i>
            <h3>Invitation Card</h3>
            <p>A beautifully designed invitation card that shares essential event details, helping guests feel welcomed and informed.</p>
        </div>
        <div class="box">
            <i class="fas fa-utensils"></i>
            <h3>Food &amp; Drinks</h3>
            <p>We offer a variety of delicious meals, snacks, desserts, and drinks, bringing fresh flavors to every occasion.</p>
        </div>
        <div class="box">
            <i class="fas fa-music"></i>
            <h3>Entertainment</h3>
            <p>We provide music, games, and fun activities to make every event lively, enjoyable, and memorable for all guests.</p>
        </div>
        <div class="box">
            <i class="fas fa-video"></i>
            <h3>Photos &amp; Videos</h3>
            <p>We capture beautiful photos and high-quality videos to preserve every special and memorable moment of your events.</p>
        </div>
        <div class="box">
            <i class="fas fa-wine-glass"></i>
            <h3>Custom Foods</h3>
            <p>We prepare personalized meals and treats to suit your taste and event theme perfectly.</p>
        </div>
    </div>
</section>

<!-- ===== PACKAGES ===== -->
<section class="packages" id="packages">
    <h1 class="heading">
        <span>p</span><span>a</span><span>c</span><span>k</span><span>a</span><span>g</span><span>e</span><span>s</span>
    </h1>
    <div class="box-container">

        <?php
        $pkgImages = ['wedding'=>'images/p-2.jpg','birthday'=>'images/p-1.jpg','conference'=>'images/p-3.jpg','corporate'=>'images/p-4.jpg','other'=>'images/p-5.jpg'];

        if ($result_all && mysqli_num_rows($result_all) > 0):
            while ($pkg = mysqli_fetch_assoc($result_all)):
                $cat   = $pkg['category'];
                $img   = $pkgImages[$cat] ?? '';
                $final = $pkg['price'] * (1 - $pkg['discount'] / 100);
        ?>
        <div class="box">
            <?php if (file_exists($img)): ?>
            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($pkg['name']); ?>">
            
            <?php endif; ?>
            <div class="content">
                <h3><?php echo htmlspecialchars($pkg['name']); ?></h3>
                <ul>
                    <p><?php echo htmlspecialchars($pkg['description'] ?? ''); ?></p>
                    <p>Venue: <?php echo htmlspecialchars($pkg['venue'] ?? ''); ?></p>
                    <p>Up to <?php echo $pkg['capacity']; ?> Guests</p>
                    <?php if ($pkg['discount'] > 0): ?>
                    <p><?php echo $pkg['discount']; ?>% Discount Applied</p>
                    <?php endif; ?>
                </ul>
                <div class="price">
                    BDT <?php echo number_format($final, 0); ?>
                    <?php if ($pkg['discount'] > 0): ?>
                    <span>BDT <?php echo number_format($pkg['price'], 0); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($loggedIn): ?>
                <a href="pages/customer-dashboard.html" class="btn">Book Now</a>
                <?php else: ?>
                <a href="#" class="btn open-login">Book Now</a>
                <?php endif; ?>
            </div>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <!-- Fallback static packages if DB is empty -->
        <?php
        $staticPkgs = [
            ['emoji'=>'🎂','name'=>'Birthday Package','desc'=>'Fun celebration with cake & decoration','venue'=>'Event Hall, Gulshan','guests'=>100,'price'=>25000,'discount'=>5],
            ['emoji'=>'💍','name'=>'Wedding Package','desc'=>'Luxurious wedding with full services','venue'=>'Grand Ballroom, Dhaka','guests'=>500,'price'=>250000,'discount'=>10],
            ['emoji'=>'🎤','name'=>'Conference Package','desc'=>'Professional conference setup with AV','venue'=>'Conference Center, Motijheel','guests'=>300,'price'=>80000,'discount'=>0],
            ['emoji'=>'🏢','name'=>'Corporate Package','desc'=>'All-inclusive corporate event','venue'=>'Rooftop Venue, Banani','guests'=>200,'price'=>120000,'discount'=>8],
            ['emoji'=>'🎉','name'=>'Anniversary Package','desc'=>'Romantic anniversary celebration','venue'=>'Garden Venue, Uttara','guests'=>100,'price'=>45000,'discount'=>5],
            ['emoji'=>'🌿','name'=>'Picnic Package','desc'=>'Outdoor picnic with games & food','venue'=>'Botanical Garden, Mirpur','guests'=>150,'price'=>30000,'discount'=>0],
        ];
        foreach ($staticPkgs as $p):
            $final = $p['price'] * (1 - $p['discount'] / 100);
        ?>
        <div class="box">
            <div class="pkg-emoji-box"><?php echo $p['emoji']; ?></div>
            <div class="content">
                <h3><?php echo $p['name']; ?></h3>
                <ul>
                    <p><?php echo $p['desc']; ?></p>
                    <p>Venue: <?php echo $p['venue']; ?></p>
                    <p>Up to <?php echo $p['guests']; ?> Guests</p>
                    <?php if ($p['discount'] > 0): ?>
                    <p><?php echo $p['discount']; ?>% Discount Applied</p>
                    <?php endif; ?>
                </ul>
                <div class="price">
                    BDT <?php echo number_format($final, 0); ?>
                    <?php if ($p['discount'] > 0): ?>
                    <span>BDT <?php echo number_format($p['price'], 0); ?></span>
                    <?php endif; ?>
                </div>
                <a href="#" class="btn open-login">Book Now</a>
            </div>
        </div>
        <?php endforeach; endif; ?>

    </div>
</section>

<!-- ===== GALLERY ===== -->
<section class="gallery" id="gallery">
    <h1 class="heading">
        <span>g</span><span>a</span><span>l</span><span>l</span><span>e</span><span>r</span><span>y</span>
    </h1>
    <div class="box-container">
        <?php
        $galleryItems = [
            ['img'=>'images/g-1.jpg','emoji'=>'💍','title'=>'Royal Wedding'],
            ['img'=>'images/g-2.jpg','emoji'=>'🎂','title'=>'Birthday Bash'],
            ['img'=>'images/g-3.jpg','emoji'=>'🎤','title'=>'Grand Concert'],
            ['img'=>'images/g-4.jpg','emoji'=>'🥂','title'=>'Anniversary'],
            ['img'=>'images/g-5.jpg','emoji'=>'🌿','title'=>'Garden Picnic'],
            ['img'=>'images/g-6.jpg','emoji'=>'🏢','title'=>'Corporate Event'],
            ['img'=>'images/g-7.jpg','emoji'=>'🎊','title'=>'Gala Night'],
            ['img'=>'images/g-8.jpg','emoji'=>'📸','title'=>'Photo Session'],
            ['img'=>'images/g-9.jpg','emoji'=>'🎉','title'=>'Celebration'],
        ];
        foreach ($galleryItems as $g):
        ?>
        <div class="box">
            <?php if (file_exists($g['img'])): ?>
            <img src="<?php echo $g['img']; ?>" alt="<?php echo $g['title']; ?>">
            <?php else: ?>
            <div class="gallery-emoji"><?php echo $g['emoji']; ?></div>
            <?php endif; ?>
            <div class="content">
                <h3><?php echo $g['title']; ?></h3>
                <p>Your special moments into unforgettable experiences</p>
                <a href="#" class="btn">See More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== REVIEWS ===== -->
<section class="review" id="review">
    <h1 class="heading">
        <span>r</span><span>e</span><span>v</span><span>i</span><span>e</span><span>w</span>
    </h1>
    <div class="swiper mySwiper review-slider">
        <div class="swiper-wrapper">

            <?php
            // Try to load feedback from DB
            $fbResult = mysqli_query($data, "SELECT f.*, u.name AS customer_name FROM feedback f JOIN users u ON f.customer_id = u.id ORDER BY f.created_at DESC LIMIT 10");
            if ($fbResult && mysqli_num_rows($fbResult) > 0):
                while ($fb = mysqli_fetch_assoc($fbResult)):
                    $initial = strtoupper(substr($fb['customer_name'], 0, 1));
                    $stars   = (int)$fb['rating'];
            ?>
            <div class="swiper-slide">
                <div class="box">
                    <div class="review-avatar"><?php echo $initial; ?></div>
                    <h3><?php echo htmlspecialchars($fb['customer_name']); ?></h3>
                    <p><?php echo htmlspecialchars($fb['comment'] ?? 'Great service!'); ?></p>
                    <div class="stars">
                        <?php for ($i=0;$i<$stars;$i++) echo '<i class="fas fa-star"></i>'; ?>
                        <?php for ($i=$stars;$i<5;$i++) echo '<i class="far fa-star"></i>'; ?>
                    </div>
                </div>
            </div>
            <?php
                endwhile;
            else:
            // Static reviews fallback
            $staticReviews = [
                ['name'=>'Abu Bakar Miaji','img'=>'images/s-1.jpg','text'=>'Excellent coordination and friendly team. Our event was truly memorable because of them.'],
                ['name'=>'David Williams','img'=>'images/s-2.jpg','text'=>'From decoration to food and entertainment, every detail was outstanding. Highly recommended!'],
                ['name'=>'Anna Maria Wilson','img'=>'images/s-3.jpg','text'=>'From decoration to food and entertainment, every detail was outstanding. Highly recommended!'],
                ['name'=>'James Alexander Brown','img'=>'images/s-4.jpg','text'=>'Outstanding planning and execution. The atmosphere, music, and coordination were spot on.'],
                ['name'=>'Alex Jonathan Miller','img'=>'images/s-5.jpg','text'=>'Exceptional service from start to finish. The team managed everything beautifully.'],
                ['name'=>'Daniel Robert Smith','img'=>'images/s-6.jpg','text'=>'Excellent coordination and friendly team. Our event was truly memorable because of them.'],
            ];
            foreach ($staticReviews as $r):
                $initial = strtoupper(substr($r['name'],0,1));
            ?>
            <div class="swiper-slide">
                <div class="box">
                    <?php if (file_exists($r['img'])): ?>
                    <img src="<?php echo $r['img']; ?>" alt="<?php echo $r['name']; ?>">
                    <?php else: ?>
                    <div class="review-avatar"><?php echo $initial; ?></div>
                    <?php endif; ?>
                    <h3><?php echo $r['name']; ?></h3>
                    <p><?php echo $r['text']; ?></p>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>

        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- ===== CONTACT ===== -->
<section class="contact" id="contact">
    <h1 class="heading">
        <span>c</span><span>o</span><span>n</span><span>t</span><span>a</span><span>c</span><span>t</span>
    </h1>
    <div class="container">
        <form action="php/contact.php" method="POST">
            <label for="c-name">Name</label>
            <input type="text" id="c-name" name="name" placeholder="Enter your name..">

            <label for="c-number">Mobile Number</label>
            <input type="tel" id="c-number" name="number" placeholder="+880 01XXXXXXXXX" required>

            <label for="c-email">E-mail</label>
            <input type="email" id="c-email" name="email" placeholder="abc@gmail.com" required>

            <label for="c-subject">Subject</label>
            <input type="text" id="c-subject" name="subject" placeholder="Enter your subject.." required>

            <label for="c-desc">Description</label>
            <textarea id="c-desc" name="description" placeholder="Write something.." style="height:160px;"></textarea>

            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<section style="background:#333;" class="footer">
    <div class="box-container">
        <div class="box">
            <h3>About Us</h3>
            <p>PLANPro simplifies event planning by offering tailored packages for weddings, birthdays, and corporate events. With professional coordination, creative execution, and reliable service, we ensure every event is smooth, memorable, and stress-free.</p>
        </div>
        <div class="box">
            <h3>Contact Us</h3>
            <p><i class="fas fa-phone"></i> +880 1234 567890</p>
            <p><i class="fas fa-envelope"></i> info@planpro.com</p>
            <p><i class="fas fa-map-marker-alt"></i> 123 Event Street, Dhaka, Bangladesh</p>
        </div>
        <div class="box">
            <h3>Quick Links</h3>
            <a href="#home">Home</a>
            <a href="#services">Services</a>
            <a href="#packages">Packages</a>
            <a href="#gallery">Gallery</a>
            <a href="#review">Reviews</a>
            <a href="#contact">Contact</a>
        </div>
        <div class="box">
            <h3>Follow Us</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
    <h1 class="credit">Created by <span>PLANPro Team</span> | All rights reserved &copy; 2025</h1>
</section>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script>
// ===== SWIPER =====
const swiper = new Swiper('.mySwiper', {
    loop: true,
    autoplay: { delay: 3500, disableOnInteraction: false },
    slidesPerView: 1,
    spaceBetween: 20,
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
        640:  { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
    }
});

// ===== HEADER SCROLL =====
window.addEventListener('scroll', () => {
    document.getElementById('main-header').classList.toggle('scrolled', window.scrollY > 60);
});

// ===== MOBILE MENU =====
document.getElementById('menu-ber').addEventListener('click', () => {
    document.getElementById('main-nav').classList.toggle('open');
});

// ===== SEARCH =====
document.getElementById('search-btn').addEventListener('click', () => {
    document.getElementById('search-bar-form').classList.toggle('active');
});

// ===== VIDEO SLIDER =====
const video = document.getElementById('video-slider');
document.querySelectorAll('.vid-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.vid-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (video) {
            video.src = btn.dataset.src;
            video.play().catch(() => {});
        }
    });
});

// ===== LOGIN / REGISTER TOGGLE =====
const loginContainer  = document.getElementById('login-form-container');
const registerContainer = document.getElementById('register-form-container');
const loginBtn = document.getElementById('login-btn');

loginBtn?.addEventListener('click', () => loginContainer?.classList.add('active'));
document.getElementById('form-close')?.addEventListener('click', () => loginContainer?.classList.remove('active'));
document.getElementById('reg-close')?.addEventListener('click', () => registerContainer?.classList.remove('active'));
document.getElementById('show-register')?.addEventListener('click', e => {
    e.preventDefault();
    loginContainer?.classList.remove('active');
    registerContainer?.classList.add('active');
});
document.getElementById('show-login')?.addEventListener('click', e => {
    e.preventDefault();
    registerContainer?.classList.remove('active');
    loginContainer?.classList.add('active');
});

// Open login on "Book Now" for guests
document.querySelectorAll('.open-login').forEach(btn => {
    btn.addEventListener('click', e => { e.preventDefault(); loginContainer?.classList.add('active'); });
});

// Close on overlay click
[loginContainer, registerContainer].forEach(c => {
    c?.addEventListener('click', e => { if (e.target === c) c.classList.remove('active'); });
});

// ===== LOGIN FORM SUBMIT =====
document.getElementById('login-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const errEl = document.getElementById('login-error');
    errEl.style.display = 'none';
    const fd = new FormData();
    fd.append('email', document.getElementById('login-email').value);
    fd.append('password', document.getElementById('login-password').value);
    try {
        const res  = await fetch('php/login.php', { method:'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errEl.textContent = data.message;
            errEl.style.display = 'block';
        }
    } catch(err) {
        errEl.textContent = 'Server error. Make sure XAMPP is running.';
        errEl.style.display = 'block';
    }
});

// ===== REGISTER FORM SUBMIT =====
document.getElementById('register-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const errEl = document.getElementById('reg-error');
    const okEl  = document.getElementById('reg-success');
    errEl.style.display = 'none'; okEl.style.display = 'none';
    const fd = new FormData();
    fd.append('name',     document.getElementById('reg-name').value);
    fd.append('email',    document.getElementById('reg-email').value);
    fd.append('phone',    document.getElementById('reg-phone').value);
    fd.append('password', document.getElementById('reg-password').value);
    try {
        const res  = await fetch('php/register.php', { method:'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            okEl.textContent = data.message; okEl.style.display = 'block';
            setTimeout(() => {
                registerContainer.classList.remove('active');
                loginContainer.classList.add('active');
            }, 2000);
        } else {
            errEl.textContent = data.message; errEl.style.display = 'block';
        }
    } catch(err) {
        errEl.textContent = 'Server error. Try again.'; errEl.style.display = 'block';
    }
});
</script>


<!-- =============================================
     PLANPRO AI CHATBOT
     ============================================= -->

<!-- Chat Bubble Button -->
<div id="chat-bubble" onclick="toggleChat()" title="Chat with PLANPro AI">
  <div id="bubble-icon">🗫</div>
  <div id="bubble-close" style="display:none;">✕</div>
  <span id="chat-notify">1</span>
</div>

<!-- Chat Window -->
<div id="chat-window">
  <!-- Header -->
  <div id="chat-header">
    <div style="display:flex;align-items:center;gap:10px;">
      <div id="chat-avatar">👩‍💼</div>
      <div>
        <div style="font-weight:700;font-size:14px;">PLANPro Assistant</div>
        <div style="font-size:11px;opacity:0.85;display:flex;align-items:center;gap:4px;">
          <span id="status-dot"></span> Online · Powered by AI
        </div>
      </div>
    </div>
    <button onclick="clearChat()" title="Clear chat" style="background:rgba(255,255,255,0.15);border:none;color:white;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:12px;">🗑 Clear</button>
  </div>

  <!-- Messages Area -->
  <div id="chat-messages">
    <div class="chat-msg bot">
      <div class="msg-avatar">👩‍💼</div>
      <div class="msg-bubble">
        Hello sir! I'm the <strong>PLANPro AI Assistant </strong>.<br><br>
        <!-- I can help you with:<br>
        Event packages & pricing<br>
        Booking & payment process<br>
        Our team & services<br>
        Venue information<br><br> -->
        How can I help you today?
      </div>
    </div>
    <!-- Quick reply buttons -->
    <div id="quick-replies">
      <button class="quick-btn" onclick="quickAsk('What event packages do you offer?')"> Packages</button>
      <button class="quick-btn" onclick="quickAsk('How do I book an event?')"> How to Book</button>
      <button class="quick-btn" onclick="quickAsk('What are your prices?')"> Pricing</button>
      <button class="quick-btn" onclick="quickAsk('How can I contact you?')"> Contact</button>
    </div>
  </div>

  <!-- Typing indicator -->
  <div id="typing-indicator" style="display:none;">
    <div class="chat-msg bot">
      <div class="msg-avatar">👩‍💼</div>
      <div class="msg-bubble typing-dots">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>

  <!-- Input Area -->
  <div id="chat-input-area">
    <input type="text" id="chat-input" placeholder="Ask anything about our events..." onkeydown="if(event.key==='Enter')sendMessage()">
    <button id="send-btn" onclick="sendMessage()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    </button>
  </div>
  <div style="text-align:center;font-size:10px;color:#94A3B8;padding:6px 0 8px;background:#F8FAFC;">
    Powered by Claude AI · PLANPro 2025
  </div>
</div>

<!-- =============================================
     CHATBOT STYLES
     ============================================= -->
<style>
/* Bubble */
#chat-bubble {
  position: fixed;
  bottom: 28px;
  right: 28px;
  width: 58px;
  height: 58px;
  background: linear-gradient(135deg, #FF892A, #f2852d);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 9999;
  box-shadow: 0 6px 24px rgb(233, 144, 34);
  transition: transform 0.3s, box-shadow 0.3s;
  animation: pulse-ring 2.5s ease-in-out infinite;
}
#chat-bubble:hover { transform: scale(1.08); box-shadow: 0 8px 32px #FF892A; }
#bubble-icon, #bubble-close { font-size: 24px; position:absolute; transition: opacity 0.2s, transform 0.2s; }
#bubble-close { font-size:20px; color:white; font-weight:700; }

#chat-notify {
  position: absolute;
  top: -3px; right: -3px;
  width: 20px; height: 20px;
  background: #EF4444;
  border-radius: 50%;
  font-size: 11px;
  color: white;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
  animation: bounce-in 0.4s ease;
}

@keyframes pulse-ring {
  0%   { box-shadow: 0 6px 24px rgba(255, 137, 42,0.45), 0 0 0 0 rgba(255, 137, 42,0.4); }
  100% { box-shadow: 0 6px 24px rgba(255, 137, 42,0.45), 0 0 0 0 rgba(255, 137, 42,0); }
  70%  { box-shadow: 0 6px 24px rgba(255, 137, 42,0.45), 0 0 0 14px rgba(255, 137, 42,0); }
}
@keyframes bounce-in {
  0%   { transform: scale(0); }
  60%  { transform: scale(1.2); }
  100% { transform: scale(1); }
}

/* Chat Window */
#chat-window {
  position: fixed;
  bottom: 100px;
  right: 28px;
  width: 360px;
  height: 520px;
  background: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.18), 0 4px 16px rgba(255, 137, 42,0.12);
  display: none;
  flex-direction: column;
  z-index: 9998;
  overflow: hidden;
  animation: slide-up 0.3s ease;
  border: 1px solid #E2E8F0;
}
#chat-window.open { display: flex; }
@keyframes slide-up {
  from { opacity:0; transform: translateY(20px) scale(0.95); }
  to   { opacity:1; transform: translateY(0) scale(1); }
}

/* Header */
#chat-header {
  background: linear-gradient(135deg, #FF892A, #f2852d);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: white;
  flex-shrink: 0;
}
#chat-avatar {
  width: 38px; height: 38px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}
#status-dot {
  width: 7px; height: 7px;
  background: #4ADE80;
  border-radius: 50%;
  display: inline-block;
  animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink {
  0%,100% { opacity:1; } 50% { opacity:0.3; }
}

/* Messages */
#chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 12px 8px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #F8FAFC;
  scroll-behavior: smooth;
}
#chat-messages::-webkit-scrollbar { width: 4px; }
#chat-messages::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }

.chat-msg {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  animation: msg-in 0.25s ease;
}
@keyframes msg-in {
  from { opacity:0; transform:translateY(8px); }
  to   { opacity:1; transform:translateY(0); }
}
.chat-msg.user { flex-direction: row-reverse; }

.msg-avatar {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg,#FF892A, #f2852d);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
.chat-msg.user .msg-avatar {
  background: linear-gradient(135deg,#FF892A, #f2852d);
}

.msg-bubble {
  max-width: 75%;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 13px;
  line-height: 1.55;
  color: #1F2937;
  background: #FFFFFF;
  box-shadow: 0 1px 4px rgba(0,0,0,0.07);
  border: 1px solid #E2E8F0;
}
.chat-msg.user .msg-bubble {
  background: linear-gradient(135deg,#FF892A, #f2852d);
  color: white;
  border: none;
  border-radius: 16px 16px 4px 16px;
}
.chat-msg.bot .msg-bubble {
  border-radius: 16px 16px 16px 4px;
}

/* Typing dots */
.typing-dots { display:flex; gap:5px; align-items:center; padding:12px 16px !important; }
.typing-dots span {
  width: 7px; height: 7px;
  background: #FF892A;
  border-radius: 50%;
  animation: dot-bounce 1.2s ease-in-out infinite;
}
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes dot-bounce {
  0%,60%,100% { transform: translateY(0); }
  30% { transform: translateY(-6px); }
}

/* Quick replies */
#quick-replies {
  display: flex; flex-wrap: wrap; gap: 6px;
  padding: 4px 0 6px;
}
.quick-btn {
  background: white;
  border: 1.5px solid #f5af75;
  color: #837c7c;
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}
.quick-btn:hover { background: #EFF6FF; border-color: #f2dece; }

/* Input area */
#chat-input-area {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 12px;
  border-top: 1px solid #E2E8F0;
  background: white;
  flex-shrink: 0;
}
#chat-input {
  flex: 1;
  padding: 9px 14px;
  border: 1.5px solid #E2E8F0;
  border-radius: 24px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
  color: #161b23;
  background: #F8FAFC;
  transition: border-color 0.2s;
}
#chat-input:focus { border-color: #FF892A; background: white; }
#chat-input::placeholder { color: #94A3B8; }

#send-btn {
  width: 38px; height: 38px;
  background: linear-gradient(135deg, #FF892A, #f2852d);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.2s, box-shadow 0.2s;
}
#send-btn:hover { transform: scale(1.08); box-shadow: 0 4px 12px rgba(255, 137, 42,0.35); }
#send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

@media (max-width: 480px) {
  #chat-window { width: calc(100vw - 20px); right: 10px; bottom: 90px; height: 470px; }
  #chat-bubble { bottom: 18px; right: 18px; }
}
</style>

 <!-- CHATBOT JAVASCRIPT -->

<script>

// CONFIG
const SYSTEM_PROMPT = `You are the official AI assistant for PLANPro — an Online Event Management System based in Dhaka, Bangladesh.

YOUR KNOWLEDGE ABOUT PLANPRO:
=================================

ABOUT THE COMPANY:
- Name: PLANPro (Online Event Management System)
- Location: 123 Event Street, Dhaka, Bangladesh
- Phone: +880 1234 567890
- Email: info@planpro.com
- Website: Available online via XAMPP local server
- Tagline: "Every Event Is Worthwhile"

EVENT PACKAGES (from database):
1. Royal Wedding Package
   - Category: Wedding
   - Venue: Grand Ballroom, Dhaka
   - Capacity: 500 guests
   - Regular Price: BDT 250,000
   - Discount: 10% OFF → Final Price: BDT 225,000
   - Includes: Full decoration, 5-course catering, photography & videography, music & entertainment

2. Birthday Bash Package
   - Category: Birthday
   - Venue: Event Hall, Gulshan
   - Capacity: 100 guests
   - Regular Price: BDT 25,000
   - Discount: 5% OFF → Final Price: BDT 23,750
   - Includes: Birthday decoration, custom cake, catering, photography

3. Corporate Summit Package
   - Category: Conference
   - Venue: Conference Center, Motijheel
   - Capacity: 300 guests
   - Price: BDT 80,000 (no discount)
   - Includes: AV equipment, professional seating, catering, sound system

4. Premium Corporate Package
   - Category: Corporate
   - Venue: Rooftop Venue, Banani
   - Capacity: 200 guests
   - Regular Price: BDT 120,000
   - Discount: 8% OFF → Final Price: BDT 110,400
   - Includes: Branding materials, full catering, team activities

5. Any (Other Events Package)
   - Category: Other
   - Venue: BU
   - Capacity: 500 guests
   - Regular Price: BDT 50,000
   - Discount: 15% OFF → Final Price: BDT 42,500

SERVICES OFFERED:
- Event Venue Selection
- Invitation Card Design
- Food and Drinks (custom menus)
- Entertainment (music, games, activities)
- Professional Photography & Videography
- Custom Food Preparation

HOW TO BOOK:
1. Visit our website at index.php
2. Click "Book Now" on any package
3. Register or login to your account
4. Select your event date and number of guests
5. Add any special requests
6. Confirm your booking
7. Make payment (Cash, Card, Bank Transfer, or Mobile Banking like bKash/Nagad)
8. Receive booking confirmation

USER ROLES IN THE SYSTEM:
- Customer: Can browse packages, book events, make payments, give feedback
- Admin: Manages all packages, bookings, users, payments, and reports
- Event Manager: Coordinates assigned events, manages schedules, assigns tasks to staff
- Staff/Service Provider: Handles specific service tasks (catering, decoration, venue setup, photography, music)

PAYMENT METHODS ACCEPTED:
- Cash
- Credit/Debit Card
- Bank Transfer
- Mobile Banking (bKash, Nagad)

BOOKING STATUS FLOW:
Pending → Approved → Ongoing → Completed

CONTACT & SUPPORT:
- Phone: +880 1234 567890
- Email: info@planpro.com
- Address: 123 Event Street, Dhaka, Bangladesh
- Social: Facebook, Instagram, LinkedIn, Twitter, WhatsApp

GENERAL EVENT PLANNING KNOWLEDGE:
You also know about general event planning — timelines, decoration tips, catering advice, guest management, budgeting, and event coordination best practices.

RESPONSE STYLE:
- Be friendly, helpful, and professional
- Keep answers concise but complete
- Use emojis occasionally to be engaging
- Always mention relevant PLANPro packages when appropriate
- If asked about booking, guide them to register/login
- Respond in the same language the user writes in (Bengali or English)
- If you don't know something specific, offer to connect them with the team via contact info`;

//  STATE 
let chatOpen     = false;
let isTyping     = false;
let msgHistory   = [];
let notifShown   = true;

// TOGGLE 
function toggleChat() {
  chatOpen = !chatOpen;
  const win    = document.getElementById('chat-window');
  const icon   = document.getElementById('bubble-icon');
  const close  = document.getElementById('bubble-close');
  const notif  = document.getElementById('chat-notify');

  if (chatOpen) {
    win.classList.add('open');
    icon.style.display  = 'none';
    close.style.display = 'block';
    notif.style.display = 'none';
    notifShown = false;
    setTimeout(() => document.getElementById('chat-input').focus(), 300);
    scrollToBottom();
  } else {
    win.classList.remove('open');
    icon.style.display  = 'block';
    close.style.display = 'none';
  }
}

//  SEND MESSAGE
async function sendMessage() {
  const input = document.getElementById('chat-input');
  const text  = input.value.trim();
  if (!text || isTyping) return;

  input.value = '';
  document.getElementById('quick-replies')?.remove();
  appendMsg('user', text);
  msgHistory.push({ role: 'user', content: text });

  showTyping(true);
  isTyping = true;
  document.getElementById('send-btn').disabled = true;

  try {
    const response = await fetch('https://api.anthropic.com/v1/messages', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        model: 'claude-sonnet-4-20250514',
        max_tokens: 600,
        system: SYSTEM_PROMPT,
        messages: msgHistory
      })
    });

    const data   = await response.json();
    const reply  = data?.content?.[0]?.text || "Sorry, I couldn't get a response. Please try again!";

    showTyping(false);
    appendMsg('bot', reply);
    msgHistory.push({ role: 'assistant', content: reply });

    // Keep history max 20 messages to avoid token limits
    if (msgHistory.length > 20) msgHistory = msgHistory.slice(-20);

  } 
  
  catch (err) {
    showTyping(false);
    appendMsg('bot', '⚠️ Connection error. Please check your internet and try again.');
  }

  isTyping = false;
  document.getElementById('send-btn').disabled = false;
  document.getElementById('chat-input').focus();
}

function quickAsk(question) {
  document.getElementById('chat-input').value = question;
  sendMessage();
}

// ── DOM HELPERS ──────────────────────────────────────────────────────────────
function appendMsg(role, text) {
  const container = document.getElementById('chat-messages');
  const div = document.createElement('div');
  div.className = `chat-msg ${role}`;

  const avatar = document.createElement('div');
  avatar.className = 'msg-avatar';
  avatar.textContent = role === 'bot' ? '👩‍💼' : '👨🏻‍💼';

  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble';
  // Render basic markdown-like formatting
  bubble.innerHTML = formatText(text);

  div.appendChild(avatar);
  div.appendChild(bubble);
  container.appendChild(div);
  scrollToBottom();
}

function formatText(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g,     '<em>$1</em>')
    .replace(/\n/g,            '<br>');
}

function showTyping(show) {
  document.getElementById('typing-indicator').style.display = show ? 'block' : 'none';
  if (show) scrollToBottom();
}

function scrollToBottom() {
  const msgs = document.getElementById('chat-messages');
  setTimeout(() => msgs.scrollTop = msgs.scrollHeight, 50);
}

function clearChat() {
  msgHistory = [];
  const container = document.getElementById('chat-messages');
  container.innerHTML = `
    <div class="chat-msg bot">
      <div class="msg-avatar">👩‍💼</div>
      <div class="msg-bubble">
        Chat cleared! How can I help you with your event planning today?
      </div>
    </div>`;
}

// Show notification badge after 3 seconds if chat not opened
setTimeout(() => {
  if (!chatOpen && notifShown) {
    document.getElementById('chat-notify').style.display = 'flex';
  }
}, 3000);
</script>
</body>
</html>