  <?php
  require_once __DIR__ . '/../connection.php';

  $id = intval($_GET['id'] ?? 0);
  if (!$id) { header('Location: index.php'); exit; }

  $stmt = $pdo->prepare("SELECT * FROM maisons WHERE id = ?");
  $stmt->execute([$id]);
  $m = $stmt->fetch();
  if (!$m) { header('Location: index.php'); exit; }

  $stmt2 = $pdo->prepare("SELECT url FROM photos_maison WHERE maison_id = ? ORDER BY ordre ASC");
  $stmt2->execute([$id]);
  $photos = $stmt2->fetchAll();

  $logo_b64 = file_exists(__DIR__ . '/data/logo.png') ? base64_encode(file_get_contents(__DIR__ . '/data/logo.png')) : '';
  $dispo = ($m['disponible'] === true || $m['disponible'] === 't');

  // Kitchen flag
  $has_kitchen = ($m['cuisine'] === 't' || $m['cuisine'] === true);
  ?>
  <!DOCTYPE html>
  <html lang="fr" dir="ltr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= htmlspecialchars($m['nom']) ?> — Immobilière Hammamet PRO</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- ① Load translations BEFORE body renders -->
  <script src="translations.js"></script>
  <style>
  * { margin:0; padding:0; box-sizing:border-box; }
  :root {
    --deep: #0b0a1a;
    --aubergine: #211a3b;
    --royal: #3b2c5e;
    --dustyrose: #c49a9c;
    --sand: #fbf7f2;
    --white: #ffffff;
    --shadow-md: 0 20px 35px -12px rgba(0,0,0,0.1);
    --transition: all 0.35s cubic-bezier(0.2,0.9,0.4,1.1);
  }
  body { font-family:'Inter',system-ui,sans-serif; background:var(--sand); color:#1e1a2f; }

  .top-bar {
    background:rgba(11,10,26,0.95);
    backdrop-filter:blur(12px);
    padding:8px 32px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid rgba(255,255,255,0.05);
  }
  .back-btn {
    color:#b9b3d4; text-decoration:none; font-size:13px; font-weight:600;
    display:flex; align-items:center; gap:8px; transition:0.2s;
  }
  .back-btn:hover { color:var(--dustyrose); }

  /* Language switcher (reused from index) */
  .lang-switch { display:flex; gap:8px; }
  .lang-btn {
    background:transparent; border:none; color:#b9b3d4;
    font-size:12px; font-weight:600; padding:5px 12px; border-radius:40px;
    cursor:pointer; transition:var(--transition);
    display:inline-flex; align-items:center; gap:8px;
  }
  .lang-flag-icon {
    width:18px; height:18px; object-fit:cover; border-radius:50%;
    box-shadow:0 1px 2px rgba(0,0,0,0.2); background-color:#2a2352;
  }
  .lang-btn.active, .lang-btn:hover { background:rgba(196,154,156,0.2); color:var(--dustyrose); }
  .lang-btn.active .lang-flag-icon { box-shadow:0 0 0 2px rgba(196,154,156,0.6); transform:scale(1.02); }

  .hero-header {
    background:linear-gradient(110deg,var(--deep) 0%,var(--aubergine) 100%);
    padding:0 32px;
  }
  .logo-container {
    max-width:1400px; margin:0 auto; padding:20px 0;
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:16px;
  }
  .brand { display:flex; align-items:center; gap:16px; }
  .brand-img { width:64px; height:64px; border-radius:50%; object-fit:cover; border:3px solid var(--dustyrose); }
  .brand h1 { font-size:1.4rem; font-weight:800; color:white; }
  .brand p { color:#aca7cf; font-size:0.75rem; letter-spacing:2px; margin-top:3px; }

  .gallery-section { max-width:1400px; margin:32px auto 0; padding:0 32px; }
  .gallery-main {
    position:relative; height:520px; border-radius:28px;
    overflow:hidden; background:#1a1535; margin-bottom:16px;
  }
  .gallery-main img { width:100%; height:100%; object-fit:cover; transition:opacity 0.4s; }
  .no-photo-placeholder {
    width:100%; height:100%; display:flex; align-items:center;
    justify-content:center; flex-direction:column; gap:16px; color:rgba(255,255,255,0.3);
  }
  .no-photo-placeholder svg { opacity:0.3; }
  .gallery-nav {
    position:absolute; top:50%; transform:translateY(-50%);
    width:100%; display:flex; justify-content:space-between;
    padding:0 20px; pointer-events:none;
  }
  .gallery-btn {
    width:48px; height:48px; background:rgba(255,255,255,0.15);
    backdrop-filter:blur(8px); border:none; border-radius:50%;
    color:white; font-size:20px; cursor:pointer; pointer-events:all;
    transition:0.2s; display:flex; align-items:center; justify-content:center;
  }
  .gallery-btn:hover { background:rgba(196,154,156,0.4); }
  .gallery-counter {
    position:absolute; bottom:20px; right:24px;
    background:rgba(0,0,0,0.5); backdrop-filter:blur(6px);
    color:white; padding:5px 14px; border-radius:20px; font-size:13px; font-weight:600;
  }
  .gallery-thumbs { display:flex; gap:12px; overflow-x:auto; padding-bottom:4px; }
  .gallery-thumbs::-webkit-scrollbar { height:4px; }
  .gallery-thumbs::-webkit-scrollbar-thumb { background:#c49a9c; border-radius:4px; }
  .thumb {
    width:100px; height:72px; border-radius:12px; object-fit:cover;
    cursor:pointer; border:3px solid transparent; transition:0.2s; flex-shrink:0; opacity:0.65;
  }
  .thumb.active { border-color:var(--dustyrose); opacity:1; }
  .thumb:hover { opacity:1; }

  .detail-layout {
    max-width:1400px; margin:40px auto; padding:0 32px;
    display:grid; grid-template-columns:1fr 380px; gap:32px;
  }
  @media(max-width:900px) { .detail-layout { grid-template-columns:1fr; } }

  .property-header {
    background:white; border-radius:24px; padding:32px;
    margin-bottom:24px; box-shadow:var(--shadow-md);
  }
  .property-title { font-size:2rem; font-weight:800; color:var(--aubergine); margin-bottom:12px; }
  .property-meta { display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
  .badge-dispo {
    background:#e6f9f0; color:#1a8a5a; padding:5px 16px;
    border-radius:20px; font-size:13px; font-weight:700;
  }
  .badge-indispo {
    background:#fde8e8; color:#c0392b; padding:5px 16px;
    border-radius:20px; font-size:13px; font-weight:700;
  }
  .property-price { font-size:2.2rem; font-weight:800; color:var(--dustyrose); }
  .property-price span { font-size:1rem; color:#888; font-weight:400; }

  .features-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:16px; margin-top:24px; }
  .feature-card { background:var(--sand); border-radius:16px; padding:20px; text-align:center; }
  .feature-card .icon { font-size:28px; margin-bottom:8px; }
  .feature-card .val { font-size:1.3rem; font-weight:800; color:var(--aubergine); }
  .feature-card .lbl { font-size:12px; color:#888; margin-top:2px; }

  .description-box { background:white; border-radius:24px; padding:32px; margin-bottom:24px; box-shadow:var(--shadow-md); }
  .description-box h3 { font-size:1.2rem; font-weight:800; margin-bottom:16px; color:var(--aubergine); }
  .description-box p { color:#6f6a7f; line-height:1.8; font-size:0.95rem; }

  .map-box { background:white; border-radius:24px; padding:32px; box-shadow:var(--shadow-md); }
  .map-box h3 { font-size:1.2rem; font-weight:800; margin-bottom:16px; color:var(--aubergine); }
  #detail-map { height:320px; border-radius:16px; }

  .contact-card {
    background:linear-gradient(135deg,var(--deep),var(--aubergine));
    border-radius:24px; padding:32px; color:white;
    margin-bottom:24px; position:sticky; top:24px;
  }
  .contact-card h3 { font-size:1.1rem; font-weight:800; margin-bottom:6px; }
  .contact-card p { font-size:13px; opacity:0.7; margin-bottom:24px; }
  .contact-agent {
    display:flex; align-items:center; gap:14px;
    background:rgba(255,255,255,0.08); border-radius:16px;
    padding:16px; margin-bottom:20px;
  }
  .agent-avatar {
    width:52px; height:52px;
    background:linear-gradient(135deg,var(--dustyrose),#b27d7f);
    border-radius:50%; display:flex; align-items:center;
    justify-content:center; font-size:24px; flex-shrink:0;
  }
  .agent-name { font-weight:700; font-size:15px; }
  .agent-role { font-size:12px; opacity:0.6; margin-top:2px; }
  .agent-status { font-size:11px; color:#25D366; margin-top:4px; display:flex; align-items:center; gap:5px; }
  .status-dot { width:7px; height:7px; background:#25D366; border-radius:50%; display:inline-block; }

  .cta-btn {
    display:flex; align-items:center; justify-content:center; gap:10px;
    width:100%; padding:14px; border-radius:50px; font-weight:700; font-size:15px;
    text-decoration:none; transition:0.2s; margin-bottom:12px; border:none; cursor:pointer;
  }
  .cta-whatsapp { background:#25D366; color:white; }
  .cta-whatsapp:hover { background:#1da855; transform:translateY(-2px); }
  .cta-phone { background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); }
  .cta-phone:hover { background:rgba(255,255,255,0.18); }

  .info-card { background:white; border-radius:24px; padding:24px; box-shadow:var(--shadow-md); }
  .info-card h4 { font-size:14px; font-weight:700; color:var(--aubergine); margin-bottom:16px; }
  .info-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f5f0f8; font-size:14px; }
  .info-row:last-child { border-bottom:none; }
  .info-row .lbl { color:#888; }
  .info-row .val { font-weight:700; color:var(--aubergine); }

  .footer-premium {
    background:var(--deep); color:#cfcbe0;
    border-radius:40px 40px 0 0; margin-top:60px;
    padding:40px; text-align:center;
  }
  .copyright { font-size:0.75rem; opacity:0.5; }

  /* RTL support */
  [dir="rtl"] .back-btn { flex-direction:row-reverse; }
  [dir="rtl"] .gallery-counter { right:auto; left:24px; }
  </style>
  </head>
  <body>

  <!-- TOP BAR: back link + language switcher -->
  <div class="top-bar">
    <a href="index.php" class="back-btn" data-i18n="back_btn">← Retour aux annonces</a>
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
    <span style="color:#b9b3d4;font-size:12px">Immobilière Hammamet PRO</span>
  </div>

  <!-- HEADER -->
  <div class="hero-header">
    <div class="logo-container">
      <div class="brand">
        <?php if($logo_b64): ?>
        <img class="brand-img" src="data:image/png;base64,<?= $logo_b64 ?>" alt="logo">
        <?php endif; ?>
        <div>
          <h1>IMMOBILIÈRE HAMMAMET <span style="background:linear-gradient(135deg,#c49a9c,#b27d7f);-webkit-background-clip:text;-webkit-text-fill-color:transparent">PRO</span></h1>
          <p data-i18n="brand_tagline">LUXE · CONFIANCE · EXCELLENCE</p>
        </div>
      </div>
    </div>
  </div>

  <!-- GALLERY -->
  <div class="gallery-section">
    <div class="gallery-main" id="gallery-main">
      <?php if (!empty($photos)): ?>
        <img id="main-photo" src="<?= htmlspecialchars($photos[0]['url']) ?>" alt="<?= htmlspecialchars($m['nom']) ?>">
        <?php if (count($photos) > 1): ?>
        <div class="gallery-nav">
          <button class="gallery-btn" onclick="changePhoto(-1)">‹</button>
          <button class="gallery-btn" onclick="changePhoto(1)">›</button>
        </div>
        <div class="gallery-counter" id="gallery-counter">1 / <?= count($photos) ?></div>
        <?php endif; ?>
      <?php else: ?>
        <div class="no-photo-placeholder">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8">
            <path d="M3 12L12 3l9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
          </svg>
          <p data-i18n="no_photo">Aucune photo disponible</p>
        </div>
      <?php endif; ?>
    </div>

    <?php if (count($photos) > 1): ?>
    <div class="gallery-thumbs" id="gallery-thumbs">
      <?php foreach($photos as $i => $p): ?>
      <img class="thumb <?= $i===0?'active':'' ?>"
          src="<?= htmlspecialchars($p['url']) ?>"
          onclick="setPhoto(<?= $i ?>)"
          alt="photo <?= $i+1 ?>">
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- DETAIL LAYOUT -->
  <div class="detail-layout">

    <!-- LEFT -->
    <div class="detail-main">

      <div class="property-header">
        <div class="property-title"><?= htmlspecialchars($m['nom']) ?></div>
        <div class="property-meta">
          <span class="<?= $dispo ? 'badge-dispo' : 'badge-indispo' ?>"
                data-i18n="<?= $dispo ? 'available' : 'unavailable' ?>">
            <?= $dispo ? '✅ Disponible' : '❌ Indisponible' ?>
          </span>
          <?php if($m['latitude']): ?>
          <span style="color:#888;font-size:13px" data-i18n="location_label">📍 Hammamet, Tunisie</span>
          <?php endif; ?>
        </div>
        <div class="property-price">
          <?= number_format($m['prix'], 0, ',', ' ') ?> DT
          <span data-i18n="per_night">/ nuit</span>
        </div>

        <div class="features-grid">
          <div class="feature-card">
            <div class="icon">🛏️</div>
            <div class="val"><?= $m['nb_chambres'] ?></div>
            <div class="lbl" data-i18n="feat_rooms">Chambres</div>
          </div>
          <div class="feature-card">
            <div class="icon">🚿</div>
            <div class="val"><?= $m['nb_toilettes'] ?></div>
            <div class="lbl" data-i18n="feat_bathrooms">Salles de bain</div>
          </div>
          <div class="feature-card">
            <div class="icon"><?= $has_kitchen ? '🍳' : '🚫' ?></div>
            <div class="val" data-i18n="<?= $has_kitchen ? 'feat_kitchen_yes' : 'feat_kitchen_no' ?>">
              <?= $has_kitchen ? 'Oui' : 'Non' ?>
            </div>
            <div class="lbl" data-i18n="feat_kitchen">Cuisine</div>
          </div>
          <div class="feature-card">
            <div class="icon">📸</div>
            <div class="val"><?= count($photos) ?></div>
            <div class="lbl" data-i18n="feat_photos">Photos</div>
          </div>
        </div>
      </div>

      <?php if (!empty($m['description'])): ?>
      <div class="description-box">
        <h3 data-i18n="desc_title">📝 Description</h3>
        <p><?= nl2br(htmlspecialchars($m['description'])) ?></p>
      </div>
      <?php endif; ?>

      <?php if ($m['latitude'] && $m['longitude']): ?>
      <div class="map-box">
        <h3 data-i18n="map_title">📍 Localisation</h3>
        <div id="detail-map"></div>
      </div>
      <?php endif; ?>

    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="sidebar">
      <div class="contact-card">
        <h3 data-i18n="contact_interested">Intéressé par ce bien ?</h3>
        <p data-i18n="contact_agent_cta">Contactez notre conseiller directement</p>

        <div class="contact-agent">
          <div class="agent-avatar">👨‍💼</div>
          <div>
            <div class="agent-name">Youssef</div>
            <div class="agent-role" data-i18n="agent_role">Directeur des ventes</div>
            <div class="agent-status">
              <span class="status-dot"></span>
              <span data-i18n="agent_online">En ligne maintenant</span>
            </div>
          </div>
        </div>

        <a href="https://wa.me/21627776111?text=Bonjour%20Youssef%2C%20je%20suis%20intéressé%20par%20<?= urlencode($m['nom']) ?>%20(<?= $m['prix'] ?>%20DT)"
          target="_blank" class="cta-btn cta-whatsapp" data-i18n="cta_whatsapp">
          💬 WhatsApp · +216 27 776 111
        </a>
        <a href="tel:+21627776111" class="cta-btn cta-phone" data-i18n="cta_call">
          📞 Appeler maintenant
        </a>
      </div>

      <div class="info-card">
        <h4 data-i18n="info_title">📋 Informations</h4>
        <div class="info-row">
          <span class="lbl" data-i18n="info_ref">Référence</span>
          <span class="val">#<?= $m['id'] ?></span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_price">Prix</span>
          <span class="val"><?= number_format($m['prix'],0,',',' ') ?> DT</span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_rooms">Chambres</span>
          <span class="val"><?= $m['nb_chambres'] ?></span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_bathrooms">Salle de bain</span>
          <span class="val"><?= $m['nb_toilettes'] ?></span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_kitchen">Cuisine</span>
          <span class="val" data-i18n="<?= $has_kitchen ? 'info_kitchen_yes' : 'info_kitchen_no' ?>">
            <?= $has_kitchen ? 'Équipée' : 'Non' ?>
          </span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_status">Statut</span>
          <span class="val" data-i18n="<?= $dispo ? 'info_status_yes' : 'info_status_no' ?>">
            <?= $dispo ? 'Disponible' : 'Indisponible' ?>
          </span>
        </div>
        <div class="info-row">
          <span class="lbl" data-i18n="info_location">Localisation</span>
          <span class="val" data-i18n="info_location_val">Hammamet, TN</span>
        </div>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <footer class="footer-premium">
    <div class="copyright" data-i18n="footer_copy_maison">
      © 2025 Immobilière Hammamet PRO — Luxe · Confiance · Excellence
    </div>
  </footer>

  <script>
  // GALLERY
  const photos = <?= json_encode(array_column($photos, 'url')) ?>;
  let current = 0;

  function setPhoto(index) {
    current = index;
    if (photos.length === 0) return;
    document.getElementById('main-photo').src = photos[index];
    document.getElementById('gallery-counter').textContent = (index+1) + ' / ' + photos.length;
    document.querySelectorAll('.thumb').forEach((t,i) => t.classList.toggle('active', i===index));
  }

  function changePhoto(dir) {
    let next = (current + dir + photos.length) % photos.length;
    setPhoto(next);
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') changePhoto(1);
    if (e.key === 'ArrowLeft') changePhoto(-1);
  });

  <?php if ($m['latitude'] && $m['longitude']): ?>
  const map = L.map('detail-map').setView([<?= $m['latitude'] ?>, <?= $m['longitude'] ?>], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  const icon = L.divIcon({
    html: `<div style="width:40px;height:40px;background:#c49a9c;border-radius:50%;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;font-size:18px">🏠</div>`,
    iconSize: [40,40], className: ''
  });
  L.marker([<?= $m['latitude'] ?>, <?= $m['longitude'] ?>], {icon})
    .addTo(map)
    .bindPopup('<strong><?= htmlspecialchars($m['nom']) ?></strong><br><?= $m['prix'] ?> DT')
    .openPopup();
  <?php endif; ?>
  </script>
  </body>
  </html>