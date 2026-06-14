<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,viewport-fit=cover">
<title>Hammamet Pro | Luxe & Authenticité</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- ① Load translations BEFORE body renders -->
<script src="translations.js"></script>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --deep: #0b0a1a;
  --aubergine: #211a3b;
  --royal: #3b2c5e;
  --dustyrose: #c49a9c;
  --blush: #e8d1d1;
  --sand: #fbf7f2;
  --white: #ffffff;
  --shadow-sm: 0 15px 35px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
  --transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
}

body {
  font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
  background: var(--sand);
  color: #1e1a2f;
  line-height: 1.5;
}

.gradient-text {
  background: linear-gradient(135deg, var(--dustyrose), #b27d7f);
  background-clip: text;
  -webkit-background-clip: text;
  color: transparent;
  font-weight: 800;
}

.top-bar {
  background: rgba(11, 10, 26, 0.95);
  backdrop-filter: blur(12px);
  padding: 8px 32px;
  display: flex;
  justify-content: flex-end;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.lang-switch {
  display: flex;
  gap: 8px;
}

.lang-btn {
  background: transparent;
  border: none;
  color: #b9b3d4;
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 40px;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.lang-flag-icon {
  width: 18px;
  height: 18px;
  object-fit: cover;
  border-radius: 50%;
  box-shadow: 0 1px 2px rgba(0,0,0,0.2);
  display: inline-block;
  vertical-align: middle;
  background-color: #2a2352;
}

.lang-btn.active, .lang-btn:hover {
  background: rgba(196, 154, 156, 0.2);
  color: var(--dustyrose);
}

.lang-btn.active .lang-flag-icon {
  box-shadow: 0 0 0 2px rgba(196,154,156,0.6);
  transform: scale(1.02);
}

.hero-header {
  background: linear-gradient(110deg, var(--deep) 0%, var(--aubergine) 100%);
  padding: 0 32px;
  position: relative;
  overflow: hidden;
}

.hero-header::before {
  content: "";
  position: absolute;
  top: -30%;
  right: -10%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(196,154,156,0.15) 0%, rgba(0,0,0,0) 70%);
  border-radius: 50%;
  pointer-events: none;
}

.logo-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 28px 0 20px;
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 18px;
}

.brand-img {
  width: 84px;
  height: 84px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid var(--dustyrose);
  box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.4);
  transition: var(--transition);
}

.brand h1 {
  font-size: 1.9rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: white;
}

.brand p {
  color: #aca7cf;
  font-size: 0.8rem;
  letter-spacing: 2px;
  margin-top: 4px;
}

.glass-nav {
  background: rgba(33, 26, 59, 0.85);
  backdrop-filter: blur(12px);
  border-radius: 60px;
  margin: 20px auto 0;
  max-width: 1300px;
  padding: 0 24px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 4px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.nav-link {
  padding: 14px 24px;
  color: #e3daf5;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.3px;
  border-radius: 40px;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.nav-link:hover, .nav-link.active {
  background: rgba(196, 154, 156, 0.2);
  color: white;
}

.admin-btn {
  background: transparent;
  border: 1px solid var(--dustyrose);
  border-radius: 40px;
  padding: 8px 20px;
  color: var(--dustyrose);
  font-weight: 700;
  font-size: 0.8rem;
  margin-left: auto;
  transition: var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}

.admin-btn:hover {
  background: var(--dustyrose);
  color: var(--deep);
}

.slider-modern {
  position: relative;
  height: 560px;
  border-radius: 32px;
  overflow: hidden;
  margin: 24px 32px 0;
  box-shadow: var(--shadow-md);
}

.slide-item {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center 30%;
  opacity: 0;
  transition: opacity 1s ease-in-out;
  filter: brightness(0.9);
}

.slide-item.active {
  opacity: 1;
}

.slider-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(11,10,26,0.6) 0%, rgba(33,26,59,0.7) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  padding: 20px;
}

.slider-overlay h2 {
  font-size: 3.5rem;
  font-weight: 800;
  color: white;
  text-shadow: 0 4px 20px rgba(0,0,0,0.3);
  max-width: 800px;
}

.slider-overlay p {
  font-size: 1.2rem;
  color: rgba(255,255,240,0.9);
  margin: 20px 0 30px;
}

.hero-actions {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn-primary {
  background: var(--dustyrose);
  color: var(--deep);
  padding: 14px 36px;
  border-radius: 50px;
  font-weight: 800;
  text-decoration: none;
  transition: var(--transition);
  box-shadow: 0 10px 20px -5px rgba(0,0,0,0.3);
}

.btn-primary:hover {
  background: #b27d7f;
  transform: translateY(-3px);
}

.btn-outline {
  background: transparent;
  border: 2px solid rgba(255,255,245,0.8);
  color: white;
  padding: 14px 36px;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: var(--transition);
}

.btn-outline:hover {
  border-color: var(--dustyrose);
  color: var(--dustyrose);
}

.slider-dots {
  position: absolute;
  bottom: 20px;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  gap: 12px;
  z-index: 5;
}

.dot-indicator {
  width: 8px;
  height: 8px;
  background: rgba(255,255,245,0.5);
  border-radius: 10px;
  transition: 0.3s;
  cursor: pointer;
}

.dot-indicator.active {
  width: 32px;
  background: var(--dustyrose);
}

.stat-strip {
  background: var(--white);
  border-radius: 50px;
  margin: -30px 32px 0;
  padding: 28px 40px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 20px;
  box-shadow: var(--shadow-md);
  position: relative;
  z-index: 6;
  backdrop-filter: blur(4px);
}

.stat-block {
  text-align: center;
  flex: 1;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--aubergine);
}

.stat-label {
  font-size: 0.8rem;
  color: #7a6f9c;
  letter-spacing: 1px;
}

.search-module {
  background: white;
  border-radius: 70px;
  margin: 40px 32px 0;
  padding: 16px 24px;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: 18px;
}

.search-group {
  flex: 1;
  min-width: 150px;
}

.search-group label {
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--royal);
  letter-spacing: 1px;
}

.search-group input, .search-group select {
  width: 100%;
  padding: 12px 8px;
  border: none;
  border-bottom: 2px solid #e9e2f0;
  background: transparent;
  font-size: 0.9rem;
  transition: var(--transition);
  outline: none;
}

.search-group input:focus, .search-group select:focus {
  border-bottom-color: var(--dustyrose);
}

.btn-modern {
  background: var(--deep);
  padding: 12px 32px;
  border-radius: 40px;
  color: white;
  font-weight: bold;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}

.btn-modern:hover {
  background: var(--royal);
  transform: scale(0.98);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 48px 32px;
}

.section-title {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 12px;
  position: relative;
  display: inline-block;
}

.section-title span {
  background: linear-gradient(120deg, var(--dustyrose), #b27d7f);
  background-clip: text;
  -webkit-background-clip: text;
  color: transparent;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 36px;
  flex-wrap: wrap;
}

.section-link {
  color: var(--royal);
  font-weight: 600;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  transition: 0.2s;
}

.section-link:hover {
  border-bottom-color: var(--dustyrose);
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
  gap: 32px;
  margin-bottom: 60px;
}

.property-card {
  background: white;
  border-radius: 28px;
  overflow: hidden;
  transition: var(--transition);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.03);
  border: 1px solid rgba(0, 0, 0, 0.03);
}

.property-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 30px 45px -20px rgba(33, 26, 59, 0.25);
}

.card-media {
  height: 220px;
  background: linear-gradient(145deg, #dcd4f0, #c9bfe0);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-badge {
  position: absolute;
  top: 18px;
  left: 18px;
  background: rgba(11, 10, 26, 0.8);
  backdrop-filter: blur(8px);
  padding: 5px 14px;
  border-radius: 40px;
  font-size: 0.7rem;
  font-weight: 700;
  color: var(--dustyrose);
  letter-spacing: 1px;
}

.price-badge {
  position: absolute;
  bottom: 18px;
  right: 18px;
  background: var(--dustyrose);
  padding: 8px 18px;
  border-radius: 40px;
  font-weight: 800;
  color: var(--deep);
  font-size: 1rem;
}

.card-content {
  padding: 24px 24px 28px;
}

.card-title {
  font-size: 1.3rem;
  font-weight: 800;
  margin-bottom: 8px;
}

.card-desc {
  color: #6f6a7f;
  font-size: 0.85rem;
  margin-bottom: 18px;
  line-height: 1.45;
}

.feature-list {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 22px;
}

.feature-item {
  background: #f3eff9;
  padding: 5px 12px;
  border-radius: 30px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #3b2c5e;
  display: flex;
  align-items: center;
  gap: 5px;
}

.card-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  border-top: 1.5px solid #f0eaf5;
  padding-top: 18px;
}

.btn-detail {
  background: var(--deep);
  color: white;
  border: none;
  padding: 10px 22px;
  border-radius: 40px;
  font-weight: 600;
  font-size: 0.8rem;
  cursor: pointer;
  transition: 0.2s;
  text-decoration: none;
}

.btn-detail:hover {
  background: var(--royal);
}

.map-trigger {
  background: transparent;
  border: 1.5px solid #dad0ea;
  padding: 9px 18px;
  border-radius: 40px;
  font-size: 0.75rem;
  font-weight: 600;
  display: flex;
  gap: 5px;
  cursor: pointer;
  transition: 0.2s;
}

.map-trigger:hover {
  border-color: var(--dustyrose);
  background: rgba(196, 154, 156, 0.05);
}

.filter-active-bar {
  background: white;
  border-radius: 60px;
  padding: 12px 28px;
  margin-bottom: 32px;
  display: none;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  box-shadow: var(--shadow-sm);
  border-left: 6px solid var(--dustyrose);
}

.filter-active-bar.show {
  display: flex;
}

.filter-reset {
  margin-left: auto;
  background: none;
  border: none;
  font-weight: 700;
  color: var(--royal);
  cursor: pointer;
}

.map-section {
  margin: 50px 0 40px;
  border-radius: 28px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

#map {
  height: 460px;
  width: 100%;
  z-index: 2;
}

.map-caption {
  text-align: center;
  font-size: 0.8rem;
  margin-top: 12px;
  color: #8f89a8;
}

.team-container {
  background: white;
  border-radius: 48px;
  padding: 48px 32px;
  margin-top: 40px;
  text-align: center;
  box-shadow: var(--shadow-sm);
}

.team-grid {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  margin-top: 40px;
}

.member-card {
  background: var(--sand);
  border-radius: 40px;
  padding: 28px 32px;
  width: 240px;
  transition: var(--transition);
}

.member-card:hover {
  transform: translateY(-8px);
}

.avatar {
  background: linear-gradient(145deg, #e2d9f0, #cfc5e4);
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin: 0 auto 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 44px;
}

.footer-premium {
  background: var(--deep);
  color: #cfcbe0;
  border-radius: 40px 40px 0 0;
  margin-top: 60px;
  padding: 56px 40px 24px;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 48px;
  margin-bottom: 48px;
}

.footer-logo img {
  width: 70px;
  border-radius: 50%;
  margin-bottom: 16px;
}

.footer-col h4 {
  color: white;
  font-size: 1rem;
  margin-bottom: 20px;
  letter-spacing: 1px;
}

.footer-col a {
  color: #b2abd6;
  text-decoration: none;
  font-size: 0.85rem;
  display: block;
  margin-bottom: 12px;
  transition: 0.2s;
}

.footer-col a:hover {
  color: var(--dustyrose);
  padding-left: 6px;
}

.social-links {
  display: flex;
  gap: 14px;
  margin-top: 18px;
}

.social-icon {
  width: 38px;
  height: 38px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
}

.social-icon:hover {
  background: var(--dustyrose);
  color: var(--deep);
}

.copyright {
  text-align: center;
  border-top: 1px solid rgba(255,255,255,0.08);
  padding-top: 28px;
  font-size: 0.75rem;
}

/* RTL support */
[dir="rtl"] .filter-active-bar { border-left: none; border-right: 6px solid var(--dustyrose); }
[dir="rtl"] .footer-col a:hover { padding-left: 0; padding-right: 6px; }
[dir="rtl"] .card-badge { left: auto; right: 18px; }
[dir="rtl"] .price-badge { right: auto; left: 18px; }

@media (max-width: 780px) {
  .slider-modern { height: 450px; margin: 16px; }
  .slider-overlay h2 { font-size: 2rem; }
  .stat-strip { flex-direction: column; text-align: center; border-radius: 32px; margin: -20px 16px 0; }
  .container { padding: 28px 20px; }
  .glass-nav { border-radius: 32px; margin: 16px; }
}
</style>
</head>
<body>

<?php
require_once __DIR__ . '/../connection.php';
$stmt = $pdo->query("
    SELECT 
        m.*,
        (SELECT url FROM photos_maison WHERE maison_id = m.id ORDER BY ordre ASC LIMIT 1) AS photo_url
    FROM maisons m
    WHERE m.disponible = true
    ORDER BY m.id DESC
");
$maisons = $stmt->fetchAll();

foreach ($maisons as &$m) {
    if (empty($m['type'])) $m['type'] = 'Bien';
    if (empty($m['lat'])) $m['lat'] = $m['latitude'] ?? 36.4;
    if (empty($m['lng'])) $m['lng'] = $m['longitude'] ?? 10.61;
}
unset($m);

$maisons_json = json_encode($maisons);
$logo_b64 = file_exists(__DIR__ . '/data/logo.png') ? base64_encode(file_get_contents(__DIR__ . '/data/logo.png')) : '';
$img1_b64 = file_exists(__DIR__ . '/data/1.jpg') ? base64_encode(file_get_contents(__DIR__ . '/data/1.jpg')) : '';
$img2_b64 = file_exists(__DIR__ . '/data/2.jpg') ? base64_encode(file_get_contents(__DIR__ . '/data/2.jpg')) : '';
$img3_b64 = file_exists(__DIR__ . '/data/3.jpg') ? base64_encode(file_get_contents(__DIR__ . '/data/3.jpg')) : '';
?>

<!-- TOP BAR — Language switcher -->
<div class="top-bar">
  <div class="lang-switch" id="langGroup">
    <button class="lang-btn active" data-lang="fr" onclick="setLang(this)">
      <img class="lang-flag-icon" src="https://flagcdn.com/fr.svg" alt="FR" width="18" height="18" loading="lazy">
      FR
    </button>
    <button class="lang-btn" data-lang="ar" onclick="setLang(this)">
      <img class="lang-flag-icon" src="https://flagcdn.com/tn.svg" alt="AR" width="18" height="18" loading="lazy">
      AR
    </button>
    <button class="lang-btn" data-lang="en" onclick="setLang(this)">
      <img class="lang-flag-icon" src="https://flagcdn.com/gb.svg" alt="EN" width="18" height="18" loading="lazy">
      EN
    </button>
    <button class="lang-btn" data-lang="it" onclick="setLang(this)">
      <img class="lang-flag-icon" src="https://flagcdn.com/it.svg" alt="IT" width="18" height="18" loading="lazy">
      IT
    </button>
  </div>
</div>

<div class="hero-header">
  <div class="logo-container">
    <div class="brand">
      <?php if($logo_b64): ?>
      <img class="brand-img" src="data:image/png;base64,<?= $logo_b64 ?>" alt="logo">
      <?php endif; ?>
      <div>
        <h1>IMMOBILIÈRE HAMMAMET <span class="gradient-text">PRO</span></h1>
        <p data-i18n="brand_tagline">LUXE · CONFIANCE · EXCELLENCE</p>
      </div>
    </div>
    <div class="glass-nav">
      <a href="#" class="nav-link active" data-i18n="nav_home">🏠 Accueil</a>
      <a href="#" class="nav-link" data-i18n="nav_sales">✨ Ventes</a>
      <a href="#" class="nav-link" data-i18n="nav_rental">🌊 Location</a>
      <a href="login.php" class="admin-btn" data-i18n="nav_admin">🔐 Admin</a>
    </div>
  </div>
</div>

<!-- Hero Slider -->
<div class="slider-modern">
  <div class="slide-item active" style="background-image: url('data:image/png;base64,<?= $img1_b64 ?>');"></div>
  <div class="slide-item" style="background-image: url('data:image/png;base64,<?= $img2_b64 ?>');"></div>
  <div class="slide-item" style="background-image: url('data:image/png;base64,<?= $img3_b64 ?>');"></div>
  <div class="slider-overlay">
    <h2 data-i18n-html="hero_title">Votre rêve <span class="gradient-text">méditerranéen</span> commence ici</h2>
    <p data-i18n="hero_sub">Villas d'exception, vue mer et authenticité à Hammamet</p>
    <div class="hero-actions">
      <a href="#" class="btn-primary" data-i18n="btn_explore">Explorer les biens</a>
      <a href="#" class="btn-outline" data-i18n="btn_contact">Contactez-nous</a>
    </div>
  </div>
  <div class="slider-dots" id="sliderDots"></div>
</div>

<!-- Stats -->
<div class="stat-strip">
  <div class="stat-block">
    <div class="stat-number">50+</div>
    <div class="stat-label" data-i18n="stat_luxe">BIENS DE LUXE</div>
  </div>
  <div class="stat-block">
    <div class="stat-number">98%</div>
    <div class="stat-label" data-i18n="stat_satisfaction">SATISFACTION</div>
  </div>
  <div class="stat-block">
    <div class="stat-number">24/7</div>
    <div class="stat-label" data-i18n="stat_assistance">ASSISTANCE</div>
  </div>
  
</div>
<!-- ── Filtre avancé ── -->
<style>
.adv-filter {
  background: white;
  border-radius: 28px;
  padding: 32px 36px;
  margin-bottom: 32px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.04);
  border: 1px solid rgba(0,0,0,0.04);
}
.adv-filter-row {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  align-items: flex-end;
}
.adv-group { flex: 1; min-width: 200px; }
.adv-label {
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: var(--royal);
  margin-bottom: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.adv-label span { color: var(--dustyrose); font-size: 0.82rem; font-weight: 700; letter-spacing: 0; }

/* Double range */
.range-wrap {
  position: relative;
  height: 36px;
  display: flex;
  align-items: center;
}
.range-track {
  position: absolute;
  left: 0; right: 0;
  height: 4px;
  background: #ede8f5;
  border-radius: 4px;
}
.range-fill {
  position: absolute;
  height: 4px;
  background: var(--dustyrose);
  border-radius: 4px;
}
.range-wrap input[type=range] {
  position: absolute;
  width: 100%;
  height: 4px;
  background: transparent;
  -webkit-appearance: none;
  appearance: none;
  pointer-events: none;
  outline: none;
}
.range-wrap input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: white;
  border: 3px solid var(--dustyrose);
  box-shadow: 0 2px 8px rgba(196,154,156,0.4);
  cursor: pointer;
  pointer-events: all;
  transition: transform 0.15s;
}
.range-wrap input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.15); }
.range-wrap input[type=range]::-moz-range-thumb {
  width: 20px; height: 20px;
  border-radius: 50%;
  background: white;
  border: 3px solid var(--dustyrose);
  box-shadow: 0 2px 8px rgba(196,154,156,0.4);
  cursor: pointer;
  pointer-events: all;
}

/* Rooms */
.rooms-btns { display: flex; gap: 8px; flex-wrap: wrap; }
.rooms-btn {
  padding: 8px 18px;
  border-radius: 40px;
  border: 1.5px solid #dad0ea;
  background: transparent;
  color: var(--royal);
  font-weight: 600;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
}
.rooms-btn:hover { border-color: var(--dustyrose); color: var(--dustyrose); }
.rooms-btn.active {
  background: var(--dustyrose);
  border-color: var(--dustyrose);
  color: var(--deep);
}

/* Reset */
.adv-reset {
  padding: 10px 26px;
  border-radius: 40px;
  border: 1.5px solid #dad0ea;
  background: transparent;
  color: var(--royal);
  font-weight: 700;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.adv-reset:hover { border-color: var(--dustyrose); color: var(--dustyrose); }
</style>

<div class="adv-filter">
  <div class="adv-filter-row">

    <!-- Prix min/max double range -->
    <div class="adv-group" style="flex:2;min-width:260px;">
      <div class="adv-label">
        💰 Prix
        <span><span id="afMinLbl">0</span> — <span id="afMaxLbl">5 000 000</span> DT</span>
      </div>
      <div class="range-wrap">
        <div class="range-track"></div>
        <div class="range-fill" id="afFill"></div>
        <input type="range" id="afMin" min="0" max="10000" step="50" value="0">
        <input type="range" id="afMax" min="0" max="10000" step="50" value="10000">
      </div>
    </div>

    <!-- Chambres -->
    <div class="adv-group">
      <div class="adv-label">🛏️ Chambres min</div>
      <div class="rooms-btns">
        <button class="rooms-btn active" data-rooms="0">Tous</button>
        <button class="rooms-btn" data-rooms="1">1+</button>
        <button class="rooms-btn" data-rooms="2">2+</button>
        <button class="rooms-btn" data-rooms="3">3+</button>
        <button class="rooms-btn" data-rooms="4">4+</button>
      </div>
    </div>

    <!-- Reset -->
    <div style="flex:0 0 auto;padding-bottom:2px;">
      <button class="adv-reset" onclick="afReset()">✖ Réinitialiser</button>
    </div>

  </div>
</div>

<script>
(function(){
  const MAX = 5000000;
  let minRooms = 0;
  const elMin = document.getElementById('afMin');
  const elMax = document.getElementById('afMax');
  const lblMin = document.getElementById('afMinLbl');
  const lblMax = document.getElementById('afMaxLbl');
  const fill   = document.getElementById('afFill');

  function fmt(v){ return parseInt(v).toLocaleString('fr-FR'); }

  function updateFill(){
    const l = elMin.value / MAX * 100;
    const r = 100 - elMax.value / MAX * 100;
    fill.style.left  = l + '%';
    fill.style.right = r + '%';
    lblMin.textContent = fmt(elMin.value);
    lblMax.textContent = fmt(elMax.value);
  }

  function applyFilter(){
    const min = parseInt(elMin.value);
    const max = parseInt(elMax.value);
    document.querySelectorAll('.property-card').forEach(card => {
      const m = maisons.find(x => x.id === parseInt(card.dataset.id));
      if(!m){ card.style.display = 'none'; return; }
      const ok = parseFloat(m.prix) >= min && parseFloat(m.prix) <= max && parseInt(m.nb_chambres) >= minRooms;
      card.style.display = ok ? '' : 'none';
    });
    updateFill();
  }

  elMin.addEventListener('input', function(){
    if(+this.value > +elMax.value) this.value = elMax.value;
    applyFilter();
  });
  elMax.addEventListener('input', function(){
    if(+this.value < +elMin.value) this.value = elMin.value;
    applyFilter();
  });

  document.querySelectorAll('.rooms-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      minRooms = parseInt(this.dataset.rooms);
      document.querySelectorAll('.rooms-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      applyFilter();
    });
  });

  window.afReset = function(){
    elMin.value = 0;
    elMax.value = MAX;
    minRooms = 0;
    document.querySelectorAll('.rooms-btn').forEach((b,i) => b.classList.toggle('active', i===0));
    document.querySelectorAll('.property-card').forEach(c => c.style.display = '');
    updateFill();
  };

  updateFill();
})();
</script>

<main class="container">
  <div class="section-header">
    <h2 class="section-title" data-i18n-html="section_prestige">Sélection <span>prestige</span> ✦</h2>
  </div>

  <div class="filter-active-bar" id="filterBar">
    <span data-i18n="filter_selection">✨ Sélection :</span>
    <strong id="filterName">—</strong>
    <button class="filter-reset" onclick="resetFilter()" data-i18n="filter_reset">✖ Réinitialiser</button>
  </div>

  <div class="card-grid" id="cardsGrid">
    <?php foreach($maisons as $m): ?>
    <div class="property-card" data-id="<?= $m['id'] ?>">
      <div class="card-media" style="<?= !empty($m['photo_url']) ? 'background:none' : '' ?>">
        <?php if (!empty($m['photo_url'])): ?>
          <img src="<?= htmlspecialchars($m['photo_url']) ?>"
               style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;"
               alt="<?= htmlspecialchars($m['nom']) ?>">
        <?php else: ?>
          <svg width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="#ffffff50" stroke-width="0.8">
            <path d="M3 12L12 3l9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
          </svg>
        <?php endif; ?>
        <div class="card-badge"><?= $m['type'] ?? 'Bien' ?></div>
        <div class="price-badge"><?= $m['prix'] ?> DT</div>
      </div>
      <div class="card-content">
        <div class="card-title"><?= htmlspecialchars(strtoupper($m['nom'])) ?></div>
        <div class="card-desc"><?= htmlspecialchars($m['description']) ?></div>
        <div class="feature-list">
          <span class="feature-item">🛏️ <?= $m['nb_chambres'] ?> <span data-i18n="feat_rooms">ch.</span></span>
          <span class="feature-item">🚿 <?= $m['nb_toilettes'] ?> WC</span>
          <?php if($m['cuisine']): ?>
          <span class="feature-item">🍳 <span data-i18n="info_kitchen_yes">Cuisine équipée</span></span>
          <?php endif; ?>
        </div>
        <div class="card-actions">
          <a href="maison.php?id=<?= $m['id'] ?>" class="btn-detail" data-i18n="card_details">📄 Détails</a>
          <button class="map-trigger" onclick="focusMap(<?= $m['id'] ?>)" data-i18n="card_map">📍 Carte</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Map -->
  <div class="map-section">
    <div id="map"></div>
    <div class="map-caption" data-i18n="map_caption">💡 Cliquez sur un marqueur pour voir les détails & filtrer la liste</div>
  </div>

  <!-- Team -->
  <div class="team-container">
    <h2 class="section-title" data-i18n-html="team_title">Notre <span>équipe d'exception</span></h2>
    <p style="color:#6f6a7f; margin-bottom:20px;" data-i18n="team_sub">Des conseillers dédiés, réactifs et passionnés</p>
    <div class="team-grid">
      <div class="member-card">
        <div class="avatar">👨‍💼</div>
        <h4>Raef</h4>
        <p style="font-size:0.8rem;" data-i18n="team_role_assistant">Assistant principal</p>
      </div>
      <div class="member-card">
        <div class="avatar">💻</div>
        <h4>Louay</h4>
        <p style="font-size:0.8rem;" data-i18n="team_role_digital">Digital & webmaster</p>
      </div>
      <div class="member-card">
        <div class="avatar">🏘️</div>
        <h4>Youssef</h4>
        <p style="font-size:0.8rem;" data-i18n="team_role_sales">Directeur des ventes</p>
      </div>
    </div>
  </div>
</main>

<footer class="footer-premium">
  <div class="footer-grid">
    <div class="footer-logo">
      <img src="data:image/png;base64,<?= $logo_b64 ?>" alt="logo">
      <p style="margin-top:12px;" data-i18n="footer_about">Agence d'excellence depuis 2015, plus de 300 transactions en bord de mer.</p>
      <div class="social-links">
        <a href="https://www.facebook.com/hammamet.immobiliere.2025" class="social-icon">f</a>
        <a href="https://www.facebook.com/hammamet.immobiliere.2025" class="social-icon">in</a>
        <a href="https://www.facebook.com/hammamet.immobiliere.2025" class="social-icon">W</a>
      </div>
    </div>
    <div class="footer-col">
      <h4 data-i18n="footer_nav_title">Navigation</h4>
      <a href="#" data-i18n="footer_nav_home">Accueil</a>
      <a href="#" data-i18n="footer_nav_sales">Ventes prestige</a>
      <a href="#" data-i18n="footer_nav_rental">Locations saisonnières</a>
      <a href="#" data-i18n="footer_nav_contact">Contact & rendez-vous</a>
    </div>
    <div class="footer-col">
      <h4 data-i18n="footer_contact_title">Contact</h4>
      <a href="#">📞 +216 27 776 111</a>
      <a href="#">✉️ contact@hammametpro.tn</a>
      <a href="#">📍 Hammamet 8050, Avenue des Nations Unies</a>
    </div>
    <div class="footer-col">
      <h4 data-i18n="footer_support_title">Assistance</h4>
      <a href="#" data-i18n="footer_faq">FAQ & support</a>
      <a href="#" data-i18n="footer_owner">Espace propriétaire</a>
      <a href="#" data-i18n="footer_quote">Demande de devis</a>
    </div>
  </div>
  <div class="copyright" data-i18n="footer_copy">
    © 2025 Immobilière Hammamet PRO — Design & authenticité méditerranéenne
  </div>
</footer>

<script>
const maisons = <?= $maisons_json ?>;

// ── Slider ──
let activeSlide = 0;
const slides = document.querySelectorAll('.slide-item');
const dotsContainer = document.getElementById('sliderDots');
function updateSlider(index) {
  slides.forEach((s,i) => s.classList.toggle('active', i===index));
  document.querySelectorAll('.dot-indicator').forEach((dot,i) => dot.classList.toggle('active', i===index));
}
for(let i=0; i<slides.length; i++) {
  const dot = document.createElement('div');
  dot.classList.add('dot-indicator');
  if(i===0) dot.classList.add('active');
  dot.addEventListener('click', () => { activeSlide=i; updateSlider(activeSlide); });
  dotsContainer.appendChild(dot);
}
setInterval(() => { activeSlide = (activeSlide+1)%slides.length; updateSlider(activeSlide); }, 4800);
updateSlider(0);

// ── Map ──
const map = L.map('map').setView([36.398, 10.614], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
const markers = {};
const createIcon = (active=false) => L.divIcon({
  html: `<div style="width:${active?42:34}px;height:${active?42:34}px;background:${active?'#c49a9c':'#3b2c5e'};border-radius:50%;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.3);transform:scale(${active?1.1:1});transition:0.2s;"></div>`,
  iconSize: [active?42:34, active?42:34], className: ''
});
maisons.forEach(m => {
  const marker = L.marker([m.lat, m.lng], { icon: createIcon(false) }).addTo(map);
  marker.bindPopup(`<div style="font-weight:800;color:#211a3b">${m.nom}</div><div style="color:#c49a9c">${m.prix} DT/nuit</div><div>${m.description}</div>`);
  marker.on('click', () => filterByMaison(m.id));
  markers[m.id] = marker;
});

function filterByMaison(id) {
  const maison = maisons.find(m => m.id === id);
  document.getElementById('filterBar').classList.add('show');
  document.getElementById('filterName').innerText = maison.nom;
  document.querySelectorAll('.property-card').forEach(card => {
    card.style.display = parseInt(card.dataset.id) === id ? 'block' : 'none';
  });
  Object.keys(markers).forEach(mid => markers[mid].setIcon(createIcon(parseInt(mid) === id)));
}
window.focusMap = (id) => {
  const m = maisons.find(x => x.id===id);
  map.setView([m.lat, m.lng], 15);
  markers[id].openPopup();
  filterByMaison(id);
  document.querySelector('.map-section').scrollIntoView({ behavior: 'smooth' });
};
window.resetFilter = () => {
  document.getElementById('filterBar').classList.remove('show');
  document.querySelectorAll('.property-card').forEach(card => card.style.display = 'block');
  Object.keys(markers).forEach(mid => markers[mid].setIcon(createIcon(false)));
};
map.on('click', resetFilter);
</script>

<!-- ── Chat Widget ── -->
<style>
.chat-widget-container {
  position: fixed; bottom: 24px; right: 24px; z-index: 1000;
  font-family: 'Inter', system-ui, sans-serif;
}
.chat-toggle-btn {
  width: 64px; height: 64px;
  background: linear-gradient(135deg, #25D366, #128C7E);
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  box-shadow: 0 6px 24px rgba(0,0,0,0.25);
  transition: all 0.3s cubic-bezier(0.2,0.9,0.4,1.1);
  border: none; position: relative; z-index: 1002;
}
.chat-toggle-btn:hover { transform: scale(1.08); box-shadow: 0 10px 30px rgba(37,211,102,0.4); }
.chat-toggle-btn svg { width: 32px; height: 32px; stroke: white; fill: white; }
.notification-badge {
  position: absolute; top: -4px; right: -4px;
  background: #c49a9c; color: white; border-radius: 50%;
  width: 20px; height: 20px; font-size: 10px; font-weight: bold;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid white; animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(196,154,156,0.7); }
  70% { transform: scale(1.05); box-shadow: 0 0 0 6px rgba(196,154,156,0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(196,154,156,0); }
}
.chat-panel {
  position: absolute; bottom: 80px; right: 0; width: 360px;
  background: white; border-radius: 28px;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
  overflow: hidden; opacity: 0; visibility: hidden;
  transform: translateY(20px) scale(0.96);
  transition: all 0.25s ease-out; transform-origin: bottom right; z-index: 1001;
}
.chat-panel.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.chat-header {
  background: linear-gradient(135deg, #1E1A3C, #2a2352);
  padding: 20px 20px 16px; color: white;
  display: flex; align-items: center; gap: 12px;
}
.chat-avatar {
  width: 52px; height: 52px;
  background: linear-gradient(145deg, #c49a9c, #b27d7f);
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 28px; color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.chat-header-info h3 { font-size: 1.1rem; font-weight: 800; margin: 0; }
.chat-header-info p {
  font-size: 0.7rem; opacity: 0.8; margin: 4px 0 0;
  display: flex; align-items: center; gap: 5px;
}
.status-dot {
  width: 8px; height: 8px; background: #25D366;
  border-radius: 50%; display: inline-block;
  animation: pulse-green 1.8s infinite;
}
@keyframes pulse-green {
  0% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.9); }
  100% { opacity: 1; transform: scale(1); }
}
.chat-body { padding: 20px; background: #fbf7f2; }
.chat-message-bubble {
  background: white; border-radius: 18px; border-bottom-left-radius: 4px;
  padding: 14px 16px; margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03); border: 1px solid #f0eaf5;
}
.chat-message-bubble p { font-size: 0.85rem; color: #2a273f; margin: 0 0 6px; line-height: 1.5; }
.chat-message-bubble .time { font-size: 0.65rem; color: #aaa3c2; display: block; margin-top: 6px; }
.contact-buttons { display: flex; flex-direction: column; gap: 12px; margin-top: 8px; }
.contact-btn {
  display: flex; align-items: center; gap: 14px;
  padding: 12px 16px; border-radius: 60px; text-decoration: none;
  font-weight: 600; font-size: 0.85rem; transition: all 0.2s;
  border: 1px solid #e8e2f0; background: white; color: #1E1A3C;
}
.contact-btn svg { width: 22px; height: 22px; flex-shrink: 0; }
.contact-btn.whatsapp:hover { background: #25D366; border-color: #25D366; color: white; }
.contact-btn.messenger:hover { background: #0084ff; border-color: #0084ff; color: white; }
.contact-btn.phone:hover { background: #c49a9c; border-color: #c49a9c; color: white; }
.contact-btn .btn-desc { font-size: 0.7rem; font-weight: normal; opacity: 0.7; display: block; }
.quick-reply {
  margin-top: 16px; padding-top: 12px;
  border-top: 1px solid #eee5dc; font-size: 0.7rem;
  text-align: center; color: #8f89a8;
}
@media (max-width: 500px) {
  .chat-panel { width: 320px; right: 0; bottom: 72px; }
  .chat-toggle-btn { width: 56px; height: 56px; }
}
</style>

<div class="chat-widget-container">
  <button class="chat-toggle-btn" id="chatToggleBtn">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      <path d="M8 10h.01M12 10h.01M16 10h.01" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    <span class="notification-badge">●</span>
  </button>

  <div class="chat-panel" id="chatPanel">
    <div class="chat-header">
      <div class="chat-avatar">Y</div>
      <div class="chat-header-info">
        <h3>Youssef</h3>
        <p><span class="status-dot"></span> <span data-i18n="chat_online">En ligne · Réponse rapide</span></p>
      </div>
    </div>
    <div class="chat-body">
      <div class="chat-message-bubble">
        <p data-i18n-html="chat_greeting">👋 <strong>Bonjour !</strong> Je suis <strong>Youssef</strong>, votre conseiller immobilier.<br>Comment puis-je vous aider aujourd'hui ?</p>
        <span class="time" data-i18n="chat_location">📍 Hammamet, Tunisie</span>
      </div>
      <div class="contact-buttons">
        <a href="https://wa.me/21627776111?text=Bonjour%20Youssef%2C%20je%20souhaite%20des%20informations%20sur%20vos%20biens%20immobiliers"
           target="_blank" class="contact-btn whatsapp">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21z"/>
          </svg>
          <div>
            <span data-i18n="chat_whatsapp">WhatsApp · +216 27 776 111</span>
            <span class="btn-desc" data-i18n="chat_whatsapp_sub">Message instantané</span>
          </div>
        </a>
        <a href="https://m.me/youssef" target="_blank" class="contact-btn messenger">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 2C6.5 2 2 6.5 2 12c0 2.2 0.8 4.3 2.2 5.9L3 22l4.1-1.2c1.6 0.8 3.4 1.2 5.2 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2z"/>
            <path d="M8 9l4 3 4-3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <div>
            <span data-i18n="chat_messenger">Messenger · Youssef</span>
            <span class="btn-desc" data-i18n="chat_messenger_sub">Chat en direct</span>
          </div>
        </a>
        <a href="tel:+21627776111" class="contact-btn phone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-16.7-7.5 19.8 19.8 0 0 1-7.5-16.7A2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2L9 9.9a16 16 0 0 0 6.1 6.1l1.3-1.3a2 2 0 0 1 2-.5c1 .3 2 .6 2.9.7A2 2 0 0 1 22 17z"/>
          </svg>
          <div>
            <span data-i18n="chat_phone">Appeler · +216 27 776 111</span>
            <span class="btn-desc" data-i18n="chat_phone_sub">Disponible 24h/24</span>
          </div>
        </a>
      </div>
      <div class="quick-reply" data-i18n="chat_quick">⚡ Réponse garantie sous 5 minutes | 24h/24 7j/7</div>
    </div>
  </div>
</div>

<script>
(function () {
  const toggleBtn = document.getElementById('chatToggleBtn');
  const chatPanel = document.getElementById('chatPanel');
  let isOpen = false;
  if (toggleBtn && chatPanel) {
    toggleBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      isOpen = !isOpen;
      if (isOpen) {
        chatPanel.classList.add('open');
        const badge = toggleBtn.querySelector('.notification-badge');
        if (badge) badge.style.opacity = '0';
      } else {
        chatPanel.classList.remove('open');
      }
    });
    document.addEventListener('click', function (event) {
      if (isOpen && !chatPanel.contains(event.target) && !toggleBtn.contains(event.target)) {
        chatPanel.classList.remove('open');
        isOpen = false;
      }
    });
    chatPanel.addEventListener('click', e => e.stopPropagation());
  }
})();
</script>
</body>
</html>