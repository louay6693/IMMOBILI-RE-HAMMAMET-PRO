<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Immobilière Hammamet</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }

    /* HEADER */
    .header {
      background: linear-gradient(135deg, #0f0c29, #302b63);
      color: #fff;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .header h1 { font-size: 20px; }
    .header span { font-size: 13px; opacity: 0.6; }
    .logout {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      text-decoration: none;
    }
    .logout:hover { background: rgba(255,255,255,0.2); }

    /* MAIN */
    .main { padding: 32px; max-width: 1400px; margin: 0 auto; }

    /* TOP BAR */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 12px;
    }
    .topbar h2 { font-size: 22px; color: #302b63; }

    .filters {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }
    .filters input, .filters select {
      padding: 9px 14px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      background: #fff;
    }
    .filters input:focus, .filters select:focus {
      border-color: #302b63;
    }

    .btn-add {
      background: linear-gradient(135deg, #f093fb, #f5576c);
      color: #fff;
      border: none;
      padding: 10px 22px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .btn-add:hover { opacity: 0.85; }

    /* STATS */
    .stats {
      display: flex;
      gap: 16px;
      margin-bottom: 28px;
      flex-wrap: wrap;
    }
    .stat-card {
      background: #fff;
      border-radius: 12px;
      padding: 20px 28px;
      flex: 1;
      min-width: 160px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      border-left: 4px solid #302b63;
    }
    .stat-card .num { font-size: 28px; font-weight: 700; color: #302b63; }
    .stat-card .label { font-size: 13px; color: #888; margin-top: 4px; }

    /* TABLE */
    .table-wrap {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    thead { background: linear-gradient(135deg, #0f0c29, #302b63); color: #fff; }
    thead th { padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 500; letter-spacing: 0.5px; }
    tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; }
    tbody tr:hover { background: #fafafa; }
    tbody td { padding: 14px 16px; font-size: 14px; vertical-align: middle; }

    .img-thumb {
      width: 64px;
      height: 48px;
      object-fit: cover;
      border-radius: 8px;
      background: #eee;
    }
    .no-img {
      width: 64px;
      height: 48px;
      background: #f0f0f0;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: #bbb;
    }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    .badge-dispo { background: #e6f9f0; color: #1a8a5a; }
    .badge-indispo { background: #fde8e8; color: #c0392b; }

    .prix { font-weight: 700; color: #302b63; }

    .actions { display: flex; gap: 8px; }
    .btn-edit {
      background: #eef2ff;
      color: #302b63;
      border: none;
      padding: 6px 14px;
      border-radius: 7px;
      font-size: 13px;
      cursor: pointer;
      font-weight: 500;
    }
    .btn-edit:hover { background: #dde4ff; }
    .btn-del {
      background: #fde8e8;
      color: #c0392b;
      border: none;
      padding: 6px 14px;
      border-radius: 7px;
      font-size: 13px;
      cursor: pointer;
      font-weight: 500;
    }
    .btn-del:hover { background: #fac8c8; }

    .empty { text-align: center; padding: 48px; color: #aaa; font-size: 15px; }

    /* MODAL */
    .modal-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 100;
      align-items: center;
      justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal {
      background: #fff;
      border-radius: 20px;
      padding: 36px;
      width: 560px;
      max-width: 95vw;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }
    .modal h3 { font-size: 20px; color: #302b63; margin-bottom: 24px; }
    .modal-close {
      position: absolute; top: 16px; right: 20px;
      background: none; border: none;
      font-size: 22px; cursor: pointer; color: #aaa;
    }
    .modal-close:hover { color: #333; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-full { grid-column: 1 / -1; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input,
    .form-group textarea,
    .form-group select {
      padding: 10px 14px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      font-family: inherit;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus { border-color: #302b63; }
    .form-group textarea { resize: vertical; min-height: 80px; }

    .upload-zone {
      border: 2px dashed #c0c0c0;
      border-radius: 12px;
      padding: 24px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    .upload-zone:hover { border-color: #302b63; }
    .upload-zone input { display: none; }
    .upload-zone p { color: #888; font-size: 14px; margin-top: 8px; }
    .preview-imgs {
      display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;
    }
    .preview-imgs img {
      width: 80px; height: 60px;
      object-fit: cover; border-radius: 8px;
      border: 2px solid #e0e0e0;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #302b63, #0f0c29);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 20px;
      transition: opacity 0.2s;
    }
    .btn-submit:hover { opacity: 0.88; }
    .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

    #form-msg {
      margin-top: 12px;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 14px;
      display: none;
    }
    .msg-ok { background: #e6f9f0; color: #1a8a5a; display: block !important; }
    .msg-err { background: #fde8e8; color: #c0392b; display: block !important; }
  </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div>
    <h1>🏠 Dashboard Admin</h1>
    <span>Immobilière Hammamet PRO</span>
  </div>
  <a href="logout.php" class="logout">🚪 Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

  <!-- STATS -->
  <div class="stats">
    <div class="stat-card">
      <div class="num" id="stat-total">—</div>
      <div class="label">Total maisons</div>
    </div>
    <div class="stat-card" style="border-left-color:#1a8a5a">
      <div class="num" id="stat-dispo">—</div>
      <div class="label">Disponibles</div>
    </div>
    <div class="stat-card" style="border-left-color:#f5576c">
      <div class="num" id="stat-prix">—</div>
      <div class="label">Prix moyen (DT)</div>
    </div>
  </div>

  <!-- TOP BAR -->
  <div class="topbar">
    <h2>📋 Liste des maisons</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <div class="filters">
        <input type="text" id="filter-nom" placeholder="🔍 Rechercher par nom...">
        <select id="filter-dispo">
          <option value="">Tous les statuts</option>
          <option value="true">Disponible</option>
          <option value="false">Indisponible</option>
        </select>
        <input type="number" id="filter-prix-min" placeholder="Prix min">
        <input type="number" id="filter-prix-max" placeholder="Prix max">
      </div>
      <button class="btn-add" onclick="openModal()">＋ Ajouter une maison</button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Nom</th>
          <th>Prix (DT)</th>
          <th>Chambres</th>
          <th>Statut</th>
          <th>Détails</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="8" class="empty">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL AJOUTER MAISON -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h3>➕ Ajouter une maison</h3>

    <div class="form-grid">
      <div class="form-group form-full">
        <label>Nom de la maison</label>
        <input type="text" id="f-nom" placeholder="Ex: Villa Yasmine">
      </div>
      <div class="form-group">
        <label>Prix (DT)</label>
        <input type="number" id="f-prix" placeholder="850">
      </div>
      <div class="form-group">
        <label>Chambres</label>
        <input type="number" id="f-chambres" placeholder="3" min="1">
      </div>
      <div class="form-group">
        <label>Toilettes</label>
        <input type="number" id="f-toilettes" placeholder="2" min="1">
      </div>
      <div class="form-group">
        <label>Cuisine</label>
        <select id="f-cuisine">
          <option value="true">Oui</option>
          <option value="false">Non</option>
        </select>
      </div>
      <div class="form-group form-full">
  <label>📍 Position sur la carte — cliquez pour placer la maison</label>
  <div id="map" style="height:280px;border-radius:12px;border:1px solid #e0e0e0;margin-top:6px;z-index:1"></div>
  <input type="hidden" id="f-lat">
  <input type="hidden" id="f-lng">
  <div id="map-coords" style="font-size:12px;color:#888;margin-top:6px;text-align:center">
    Aucune position sélectionnée
  </div>
</div>
      <div class="form-group form-full">
        <label>Description</label>
        <textarea id="f-desc" placeholder="Belle villa avec piscine..."></textarea>
      </div>

      <!-- UPLOAD PHOTOS -->
      <div class="form-group form-full">
        <label>Photos de la maison</label>
        <div class="upload-zone" onclick="document.getElementById('f-photos').click()">
          <div style="font-size:36px">📸</div>
          <p>Cliquez pour ajouter des photos</p>
          <p style="font-size:12px;color:#bbb">JPG, PNG — plusieurs photos possibles</p>
          <input type="file" id="f-photos" multiple accept="image/*" onchange="previewPhotos(this)">
        </div>
        <div class="preview-imgs" id="preview-imgs"></div>
      </div>
    </div>

    <button class="btn-submit" id="btn-submit" onclick="submitMaison()">
      🏠 Enregistrer la maison
    </button>
    <div id="form-msg"></div>
  </div>
</div>

<script>
// =====================
// CHARGER LES MAISONS
// =====================
let allMaisons = [];

async function loadMaisons() {
  try {
    const res = await fetch('/controller/MaisonController.php');
    const json = await res.json();
    if (json.success) {
      allMaisons = json.data;
      renderTable(allMaisons);
      updateStats(allMaisons);
    }
  } catch (e) {
    document.getElementById('table-body').innerHTML =
      '<tr><td colspan="8" class="empty">❌ Erreur de connexion</td></tr>';
  }
}

function updateStats(data) {
  document.getElementById('stat-total').textContent = data.length;
  const dispo = data.filter(m => m.disponible === true || m.disponible === 't').length;
  document.getElementById('stat-dispo').textContent = dispo;
  const avg = data.length ? Math.round(data.reduce((s, m) => s + parseFloat(m.prix), 0) / data.length) : 0;
  document.getElementById('stat-prix').textContent = avg.toLocaleString('fr-TN');
}

function renderTable(data) {
  const tbody = document.getElementById('table-body');
  if (!data.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="empty">Aucune maison trouvée</td></tr>';
    return;
  }
  tbody.innerHTML = data.map(m => {
    const dispo = m.disponible === true || m.disponible === 't';
    const img = m.photo_url
      ? `<img class="img-thumb" src="${m.photo_url}" alt="${m.nom}">`
      : `<div class="no-img">🏠</div>`;
    return `
      <tr>
        <td><strong>#${m.id}</strong></td>
        <td>${img}</td>
        <td><strong>${m.nom}</strong></td>
        <td class="prix">${parseFloat(m.prix).toLocaleString('fr-TN')} DT</td>
        <td>🛏 ${m.nb_chambres} | 🚿 ${m.nb_toilettes}</td>
        <td><span class="badge ${dispo ? 'badge-dispo' : 'badge-indispo'}">
          ${dispo ? '✅ Disponible' : '❌ Indisponible'}
        </span></td>
        <td style="max-width:200px;font-size:13px;color:#666">${m.description || '—'}</td>
        <td>
          <div class="actions">
            <button class="btn-edit" onclick="editMaison(${m.id})">✏️ Modifier</button>
            <button class="btn-del" onclick="deleteMaison(${m.id}, '${m.nom}')">🗑 Supprimer</button>
          </div>
        </td>
      </tr>`;
  }).join('');
}

// =====================
// FILTRES
// =====================
function applyFilters() {
  const nom = document.getElementById('filter-nom').value.toLowerCase();
  const dispo = document.getElementById('filter-dispo').value;
  const pMin = parseFloat(document.getElementById('filter-prix-min').value) || 0;
  const pMax = parseFloat(document.getElementById('filter-prix-max').value) || Infinity;

  const filtered = allMaisons.filter(m => {
    const matchNom = m.nom.toLowerCase().includes(nom);
    const matchDispo = dispo === '' || String(m.disponible === true || m.disponible === 't') === dispo;
    const matchPrix = parseFloat(m.prix) >= pMin && parseFloat(m.prix) <= pMax;
    return matchNom && matchDispo && matchPrix;
  });
  renderTable(filtered);
}

['filter-nom','filter-dispo','filter-prix-min','filter-prix-max'].forEach(id => {
  document.getElementById(id).addEventListener('input', applyFilters);
});

// =====================
// MODAL
// =====================
function openModal() {
  document.getElementById('modal').classList.add('active');
}
function closeModal() {
  document.getElementById('modal').classList.remove('active');
  document.getElementById('form-msg').className = '';
  document.getElementById('form-msg').textContent = '';
  document.getElementById('preview-imgs').innerHTML = '';
  document.getElementById('f-photos').value = '';
  ['f-nom','f-prix','f-chambres','f-toilettes','f-lat','f-lng','f-desc'].forEach(id => {
    document.getElementById(id).value = '';
  });
}

// =====================
// PREVIEW PHOTOS
// =====================
function previewPhotos(input) {
  const preview = document.getElementById('preview-imgs');
  preview.innerHTML = '';
  Array.from(input.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

// =====================
// AJOUTER MAISON
// =====================
async function submitMaison() {
  const nom = document.getElementById('f-nom').value.trim();
  const prix = document.getElementById('f-prix').value;
  const chambres = document.getElementById('f-chambres').value;
  const toilettes = document.getElementById('f-toilettes').value;

  if (!nom || !prix || !chambres || !toilettes) {
    showMsg('❌ Veuillez remplir tous les champs obligatoires', 'err');
    return;
  }

  const btn = document.getElementById('btn-submit');
  btn.disabled = true;
  btn.textContent = '⏳ Enregistrement...';

  const formData = new FormData();
  formData.append('nom', nom);
  formData.append('prix', prix);
  formData.append('nb_chambres', chambres);
  formData.append('nb_toilettes', toilettes);
  formData.append('cuisine', document.getElementById('f-cuisine').value);
  formData.append('latitude', document.getElementById('f-lat').value);
  formData.append('longitude', document.getElementById('f-lng').value);
  formData.append('description', document.getElementById('f-desc').value);

  const photos = document.getElementById('f-photos').files;
  Array.from(photos).forEach(file => formData.append('photos[]', file));

  try {
    const res = await fetch('/cloudinary/upload_photo.php', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();

    if (json.success) {
      showMsg('✅ Maison ajoutée avec succès !', 'ok');
      loadMaisons();
      setTimeout(closeModal, 1800);
    } else {
      showMsg('❌ ' + (json.error || 'Erreur inconnue'), 'err');
    }
  } catch (e) {
    showMsg('❌ Erreur de connexion au serveur', 'err');
  }

  btn.disabled = false;
  btn.textContent = '🏠 Enregistrer la maison';
}

// =====================
// DELETE
// =====================
async function deleteMaison(id, nom) {
  if (!confirm(`Supprimer "${nom}" ?`)) return;
  const res = await fetch(`/controller/MaisonController.php?action=delete&id=${id}`, { method: 'DELETE' });
  const json = await res.json();
  if (json.success) loadMaisons();
  else alert('Erreur lors de la suppression');
}

function editMaison(id) {
  alert('Fonctionnalité modifier — à coder prochainement');
}

function showMsg(text, type) {
  const el = document.getElementById('form-msg');
  el.textContent = text;
  el.className = type === 'ok' ? 'msg-ok' : 'msg-err';
}

// INIT
loadMaisons();
// MAP
let map, marker;

function initMap() {
  if (map) return;
  setTimeout(() => {
    map = L.map('map').setView([36.4, 10.61], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    map.on('click', function(e) {
      const lat = e.latlng.lat.toFixed(7);
      const lng = e.latlng.lng.toFixed(7);

      if (marker) marker.setLatLng(e.latlng);
      else marker = L.marker(e.latlng).addTo(map);

      document.getElementById('f-lat').value = lat;
      document.getElementById('f-lng').value = lng;
      document.getElementById('map-coords').innerHTML =
        `📍 <strong>Lat:</strong> ${lat} &nbsp;|&nbsp; <strong>Lng:</strong> ${lng}`;
    });
  }, 200);
}

// Init map quand le modal s'ouvre
const originalOpen = window.openModal;
window.openModal = function() {
  originalOpen();
  initMap();
}
// =====================
// MODIFIER MAISON
// =====================
let mapEdit, markerEdit;

function editMaison(id) {
  const m = allMaisons.find(x => x.id === id);
  if (!m) return;

  // Remplir les champs
  document.getElementById('e-id').value = m.id;
  document.getElementById('e-nom').value = m.nom;
  document.getElementById('e-prix').value = m.prix;
  document.getElementById('e-chambres').value = m.nb_chambres;
  document.getElementById('e-toilettes').value = m.nb_toilettes;
  document.getElementById('e-cuisine').value = (m.cuisine === true || m.cuisine === 't') ? 'true' : 'false';
  document.getElementById('e-dispo').value = (m.disponible === true || m.disponible === 't') ? 'true' : 'false';
  document.getElementById('e-desc').value = m.description || '';
  document.getElementById('e-lat').value = m.latitude || '';
  document.getElementById('e-lng').value = m.longitude || '';
  document.getElementById('map-edit-coords').innerHTML = m.latitude
    ? `📍 <strong>Lat:</strong> ${m.latitude} &nbsp;|&nbsp; <strong>Lng:</strong> ${m.longitude}`
    : 'Aucune position';

  document.getElementById('preview-edit-imgs').innerHTML = '';
  // Afficher photos existantes
if (m.photo_url) {
  const preview = document.getElementById('preview-edit-imgs');
  preview.innerHTML = `
    <div style="width:100%;font-size:12px;color:#888;margin-bottom:6px">
      Photos actuelles :
    </div>`;
  
  // Charger toutes les photos de cette maison
  fetch('/cloudinary/get_photos.php?maison_id=' + m.id)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        data.photos.forEach(p => {
          const div = document.createElement('div');
          div.style.cssText = 'position:relative;display:inline-block';
          div.innerHTML = `
            <img src="${p.url}" style="width:80px;height:60px;object-fit:cover;border-radius:8px;border:2px solid #e0e0e0">
            <button onclick="deletePhoto(${p.id}, this)" style="position:absolute;top:-6px;right:-6px;background:#f5576c;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;line-height:20px">✕</button>
          `;
          preview.appendChild(div);
        });
      }
    });
}
  document.getElementById('edit-msg').className = '';
  document.getElementById('modal-edit').classList.add('active');

  // Init carte modifier
  setTimeout(() => {
    if (!mapEdit) {
      mapEdit = L.map('map-edit').setView([m.latitude || 36.4, m.longitude || 10.61], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
      }).addTo(mapEdit);
      mapEdit.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(7);
        const lng = e.latlng.lng.toFixed(7);
        if (markerEdit) markerEdit.setLatLng(e.latlng);
        else markerEdit = L.marker(e.latlng).addTo(mapEdit);
        document.getElementById('e-lat').value = lat;
        document.getElementById('e-lng').value = lng;
        document.getElementById('map-edit-coords').innerHTML =
          `📍 <strong>Lat:</strong> ${lat} &nbsp;|&nbsp; <strong>Lng:</strong> ${lng}`;
      });
    } else {
      mapEdit.setView([m.latitude || 36.4, m.longitude || 10.61], 13);
    }
    if (m.latitude && m.longitude) {
      const latlng = [parseFloat(m.latitude), parseFloat(m.longitude)];
      if (markerEdit) markerEdit.setLatLng(latlng);
      else markerEdit = L.marker(latlng).addTo(mapEdit);
    }
    mapEdit.invalidateSize();
  }, 250);
}

function closeEditModal() {
  document.getElementById('modal-edit').classList.remove('active');
  document.getElementById('e-photos').value = '';
  document.getElementById('preview-edit-imgs').innerHTML = '';
}

function previewEditPhotos(input) {
  const preview = document.getElementById('preview-edit-imgs');
  preview.innerHTML = '';
  Array.from(input.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

async function submitEdit() {
  const id = document.getElementById('e-id').value;
  const nom = document.getElementById('e-nom').value.trim();
  const prix = document.getElementById('e-prix').value;
  const chambres = document.getElementById('e-chambres').value;
  const toilettes = document.getElementById('e-toilettes').value;

  if (!nom || !prix || !chambres || !toilettes) {
    showEditMsg('❌ Veuillez remplir tous les champs obligatoires', 'err');
    return;
  }

  const btn = document.getElementById('btn-edit-submit');
  btn.disabled = true;
  btn.textContent = '⏳ Sauvegarde...';

  const formData = new FormData();
  formData.append('id', id);
  formData.append('nom', nom);
  formData.append('prix', prix);
  formData.append('nb_chambres', chambres);
  formData.append('nb_toilettes', toilettes);
  formData.append('cuisine', document.getElementById('e-cuisine').value);
  formData.append('disponible', document.getElementById('e-dispo').value);
  formData.append('latitude', document.getElementById('e-lat').value);
  formData.append('longitude', document.getElementById('e-lng').value);
  formData.append('description', document.getElementById('e-desc').value);

  const photos = document.getElementById('e-photos').files;
  Array.from(photos).forEach(file => formData.append('photos[]', file));

  try {
    const res = await fetch('/cloudinary/update_maison.php', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();
    if (json.success) {
      showEditMsg('✅ Maison modifiée avec succès !', 'ok');
      loadMaisons();
      setTimeout(closeEditModal, 1800);
    } else {
      showEditMsg('❌ ' + (json.error || 'Erreur inconnue'), 'err');
    }
  } catch (e) {
    showEditMsg('❌ Erreur de connexion', 'err');
  }

  btn.disabled = false;
  btn.textContent = '💾 Sauvegarder les modifications';
}

function showEditMsg(text, type) {
  const el = document.getElementById('edit-msg');
  el.textContent = text;
  el.className = type === 'ok' ? 'msg-ok' : 'msg-err';
}

async function deletePhoto(photoId, btn) {
  if (!confirm('Supprimer cette photo ?')) return;
  const res = await fetch('/cloudinary/delete_photo.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: photoId })
  });
  const json = await res.json();
  if (json.success) {
    btn.parentElement.remove();
  } else {
    alert('Erreur suppression photo');
  }
}
</script>
<!-- MODAL MODIFIER MAISON -->
<div class="modal-overlay" id="modal-edit">
  <div class="modal">
    <button class="modal-close" onclick="closeEditModal()">✕</button>
    <h3>✏️ Modifier la maison</h3>
    <input type="hidden" id="e-id">

    <div class="form-grid">
      <div class="form-group form-full">
        <label>Nom de la maison</label>
        <input type="text" id="e-nom">
      </div>
      <div class="form-group">
        <label>Prix (DT)</label>
        <input type="number" id="e-prix">
      </div>
      <div class="form-group">
        <label>Chambres</label>
        <input type="number" id="e-chambres" min="1">
      </div>
      <div class="form-group">
        <label>Toilettes</label>
        <input type="number" id="e-toilettes" min="1">
      </div>
      <div class="form-group">
        <label>Cuisine</label>
        <select id="e-cuisine">
          <option value="true">Oui</option>
          <option value="false">Non</option>
        </select>
      </div>
      <div class="form-group">
        <label>Disponible</label>
        <select id="e-dispo">
          <option value="true">Disponible</option>
          <option value="false">Indisponible</option>
        </select>
      </div>
      <div class="form-group form-full">
        <label>📍 Position — cliquez pour modifier</label>
        <div id="map-edit" style="height:260px;border-radius:12px;border:1px solid #e0e0e0;margin-top:6px;z-index:1"></div>
        <input type="hidden" id="e-lat">
        <input type="hidden" id="e-lng">
        <div id="map-edit-coords" style="font-size:12px;color:#888;margin-top:6px;text-align:center"></div>
      </div>
      <div class="form-group form-full">
        <label>Description</label>
        <textarea id="e-desc"></textarea>
      </div>
      <div class="form-group form-full">
        <label>Ajouter nouvelles photos</label>
        <div class="upload-zone" onclick="document.getElementById('e-photos').click()">
          <div style="font-size:36px">📸</div>
          <p>Cliquez pour ajouter des photos</p>
          <input type="file" id="e-photos" multiple accept="image/*" onchange="previewEditPhotos(this)">
        </div>
        <div class="preview-imgs" id="preview-edit-imgs"></div>
      </div>
    </div>

    <button class="btn-submit" id="btn-edit-submit" onclick="submitEdit()">
      💾 Sauvegarder les modifications
    </button>
    <div id="edit-msg"></div>
  </div>
</div>
</body>
</html>
