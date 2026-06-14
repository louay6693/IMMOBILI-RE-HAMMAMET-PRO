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
    .main { padding: 32px; max-width: 1400px; margin: 0 auto; }
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
    .filters input:focus, .filters select:focus { border-color: #302b63; }
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
    .img-thumb { width: 64px; height: 48px; object-fit: cover; border-radius: 8px; background: #eee; }
    .no-img {
      width: 64px; height: 48px; background: #f0f0f0; border-radius: 8px;
      display: flex; align-items: center; justify-content: center; font-size: 20px; color: #bbb;
    }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-dispo { background: #e6f9f0; color: #1a8a5a; }
    .badge-indispo { background: #fde8e8; color: #c0392b; }
    .prix { font-weight: 700; color: #302b63; }
    .actions { display: flex; gap: 8px; }
    .btn-edit {
      background: #eef2ff; color: #302b63; border: none;
      padding: 6px 14px; border-radius: 7px; font-size: 13px; cursor: pointer; font-weight: 500;
    }
    .btn-edit:hover { background: #dde4ff; }
    .btn-del {
      background: #fde8e8; color: #c0392b; border: none;
      padding: 6px 14px; border-radius: 7px; font-size: 13px; cursor: pointer; font-weight: 500;
    }
    .btn-del:hover { background: #fac8c8; }
    .empty { text-align: center; padding: 48px; color: #aaa; font-size: 15px; }
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 100;
      align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal {
      background: #fff; border-radius: 20px; padding: 36px;
      width: 560px; max-width: 95vw; max-height: 90vh; overflow-y: auto; position: relative;
    }
    .modal h3 { font-size: 20px; color: #302b63; margin-bottom: 24px; }
    .modal-close {
      position: absolute; top: 16px; right: 20px;
      background: none; border: none; font-size: 22px; cursor: pointer; color: #aaa;
    }
    .modal-close:hover { color: #333; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-full { grid-column: 1 / -1; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input, .form-group textarea, .form-group select {
      padding: 10px 14px; border: 1px solid #e0e0e0;
      border-radius: 8px; font-size: 14px; outline: none; font-family: inherit;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #302b63; }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .upload-zone {
      border: 2px dashed #c0c0c0; border-radius: 12px;
      padding: 24px; text-align: center; cursor: pointer; transition: border-color 0.2s;
    }
    .upload-zone:hover { border-color: #302b63; }
    .upload-zone input { display: none; }
    .upload-zone p { color: #888; font-size: 14px; margin-top: 8px; }
    .preview-imgs { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
    .preview-imgs img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; }
    .btn-submit {
      width: 100%; padding: 14px;
      background: linear-gradient(135deg, #302b63, #0f0c29);
      color: #fff; border: none; border-radius: 10px;
      font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; transition: opacity 0.2s;
    }
    .btn-submit:hover { opacity: 0.88; }
    .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
    #form-msg { margin-top: 12px; padding: 10px 14px; border-radius: 8px; font-size: 14px; display: none; }
    .msg-ok { background: #e6f9f0; color: #1a8a5a; display: block !important; }
    .msg-err { background: #fde8e8; color: #c0392b; display: block !important; }

    /* ✅ Styles drag & drop ordre photos */
    .photos-ordre-grid {
      display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;
    }
    .photo-ordre-item {
      position: relative; width: 90px; cursor: grab;
      border-radius: 10px; overflow: hidden;
      border: 2px solid #e0e0e0; background: #f9f9f9;
      transition: box-shadow 0.2s;
    }
    .photo-ordre-item:active { cursor: grabbing; }
    .photo-ordre-item.sortable-ghost  { opacity: 0.3; border-color: #f5576c; }
    .photo-ordre-item.sortable-chosen { box-shadow: 0 6px 20px rgba(48,43,99,0.35); transform: scale(1.05); }
    .photo-ordre-item img { width: 100%; height: 64px; object-fit: cover; display: block; pointer-events: none; }
    .photo-ordre-num {
      position: absolute; top: 4px; left: 4px;
      background: #302b63; color: #fff;
      font-size: 10px; font-weight: 700;
      padding: 1px 6px; border-radius: 8px;
    }
    .photo-ordre-del {
      position: absolute; top: 3px; right: 3px;
      background: #f5576c; color: #fff; border: none;
      border-radius: 50%; width: 18px; height: 18px;
      font-size: 10px; cursor: pointer; line-height: 18px;
      text-align: center; padding: 0;
    }
    .drag-label {
      text-align: center; font-size: 10px; color: #aaa; padding: 3px 0;
    }
    .btn-save-ordre {
      margin-top: 10px; padding: 8px 18px;
      background: linear-gradient(135deg, #302b63, #0f0c29);
      color: #fff; border: none; border-radius: 8px;
      font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;
      display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-save-ordre:hover { opacity: 0.85; }
    .btn-save-ordre:disabled { opacity: 0.5; cursor: not-allowed; }
    .ordre-feedback {
      display: inline-block; margin-left: 10px;
      font-size: 12px; font-weight: 600;
      padding: 5px 12px; border-radius: 20px;
      opacity: 0; transition: opacity 0.3s;
    }
    .ordre-feedback.show { opacity: 1; }
    .ordre-feedback.ok  { background: #e6f9f0; color: #1a8a5a; }
    .ordre-feedback.err { background: #fde8e8; color: #c0392b; }

    /* ✅ Styles upload zone modal ajouter — ordre preview */
    .preview-add-item {
      position: relative; width: 90px; cursor: grab;
      border-radius: 10px; overflow: hidden;
      border: 2px solid #e0e0e0; background: #f9f9f9;
    }
    .preview-add-item img { width: 100%; height: 64px; object-fit: cover; display: block; }
    .preview-add-num {
      position: absolute; top: 4px; left: 4px;
      background: #f093fb; color: #fff;
      font-size: 10px; font-weight: 700;
      padding: 1px 6px; border-radius: 8px;
    }
    .preview-add-del {
      position: absolute; top: 3px; right: 3px;
      background: #f5576c; color: #fff; border: none;
      border-radius: 50%; width: 18px; height: 18px;
      font-size: 10px; cursor: pointer; line-height: 18px;
      text-align: center; padding: 0;
    }
  </style>
</head>
<body>

<div class="header">
  <div>
    <h1>🏠 Dashboard Admin</h1>
    <span>Immobilière Hammamet PRO</span>
  </div>
  <a href="logout.php" class="logout">🚪 Déconnexion</a>
</div>

<div class="main">
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

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Image</th><th>Nom</th><th>Prix (DT)</th>
          <th>Chambres</th><th>Statut</th><th>Détails</th><th>Actions</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="8" class="empty">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL AJOUTER -->
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
        <div id="map-coords" style="font-size:12px;color:#888;margin-top:6px;text-align:center">Aucune position sélectionnée</div>
      </div>
      <div class="form-group form-full">
        <label>Description</label>
        <textarea id="f-desc" placeholder="Belle villa avec piscine..."></textarea>
      </div>
      <div class="form-group form-full">
        <label>Photos de la maison</label>
        <div class="upload-zone" onclick="document.getElementById('f-photos').click()">
          <div style="font-size:36px">📸</div>
          <p>Cliquez pour ajouter des photos</p>
          <p style="font-size:12px;color:#bbb">JPG, PNG — glissez pour réordonner après sélection</p>
          <input type="file" id="f-photos" multiple accept="image/*" onchange="previewPhotos(this)">
        </div>
        <!-- ✅ Preview avec drag & drop pour définir l'ordre avant upload -->
        <div id="preview-imgs" class="photos-ordre-grid" style="margin-top:12px;"></div>
        <div id="add-ordre-hint" style="display:none;font-size:12px;color:#888;margin-top:6px;">
          ☝️ Glissez les photos pour définir leur ordre d'affichage
        </div>
      </div>
    </div>
    <button class="btn-submit" id="btn-submit" onclick="submitMaison()">🏠 Enregistrer la maison</button>
    <div id="form-msg"></div>
  </div>
</div>

<!-- MODAL MODIFIER -->
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

      <!-- ✅ Section photos existantes avec drag & drop ordre -->
      <div class="form-group form-full" id="existing-photos-section" style="display:none;">
        <label>🔀 Photos existantes — glissez pour réordonner</label>
        <div id="photos-ordre-grid" class="photos-ordre-grid"></div>
        <div style="margin-top:10px;">
          <button class="btn-save-ordre" id="btn-save-ordre" onclick="sauvegarderOrdre()">
            💾 Sauvegarder l'ordre
          </button>
          <span class="ordre-feedback" id="ordre-feedback"></span>
        </div>
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
    <button class="btn-submit" id="btn-edit-submit" onclick="submitEdit()">💾 Sauvegarder les modifications</button>
    <div id="edit-msg"></div>
  </div>
</div>

<!-- ✅ SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

<script>
const BASE = window.location.hostname === 'localhost' ? '..' : '';

let allMaisons = [];

// ─── TABLE ───────────────────────────────────────────────────────────────────
async function loadMaisons() {
  try {
    const res = await fetch(BASE + '/controller/MaisonController.php');
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

// ─── MODAL AJOUTER ───────────────────────────────────────────────────────────
function openModal() {
  document.getElementById('modal').classList.add('active');
  initMap();
}
function closeModal() {
  document.getElementById('modal').classList.remove('active');
  document.getElementById('form-msg').className = '';
  document.getElementById('form-msg').textContent = '';
  document.getElementById('preview-imgs').innerHTML = '';
  document.getElementById('add-ordre-hint').style.display = 'none';
  document.getElementById('f-photos').value = '';
  ['f-nom','f-prix','f-chambres','f-toilettes','f-lat','f-lng','f-desc'].forEach(id => {
    document.getElementById(id).value = '';
  });
  addSortable = null;
  addFiles = [];
}

// ✅ Drag & drop pour les photos dans la modal "Ajouter"
let addSortable = null;
let addFiles    = [];   // on garde les File objects dans l'ordre voulu

function previewPhotos(input) {
  addFiles = Array.from(input.files);
  renderAddPreview();
}

function renderAddPreview() {
  const preview = document.getElementById('preview-imgs');
  const hint    = document.getElementById('add-ordre-hint');
  preview.innerHTML = '';

  if (!addFiles.length) { hint.style.display = 'none'; return; }
  hint.style.display = addFiles.length > 1 ? 'block' : 'none';

  addFiles.forEach((file, i) => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.className = 'preview-add-item';
      div.dataset.index = i;
      div.innerHTML = `
        <img src="${e.target.result}" alt="">
        <span class="preview-add-num">${i + 1}</span>
        <button class="preview-add-del" onclick="removeAddFile(${i})">✕</button>
        <div class="drag-label">☰</div>
      `;
      preview.appendChild(div);
      // Re-init Sortable après chaque ajout (simple, fonctionne)
      if (addSortable) { addSortable.destroy(); addSortable = null; }
      if (addFiles.length > 1) {
        addSortable = Sortable.create(preview, {
          animation: 150,
          ghostClass: 'sortable-ghost',
          chosenClass: 'sortable-chosen',
          onEnd: () => {
            // Réordonner addFiles selon le nouvel ordre DOM
            const items = preview.querySelectorAll('.preview-add-item');
            const newOrder = Array.from(items).map(el => addFiles[parseInt(el.dataset.index)]);
            addFiles = newOrder;
            // Mettre à jour les indices et badges
            items.forEach((el, idx) => {
              el.dataset.index = idx;
              el.querySelector('.preview-add-num').textContent = idx + 1;
            });
          }
        });
      }
    };
    reader.readAsDataURL(file);
  });
}

function removeAddFile(index) {
  addFiles.splice(index, 1);
  renderAddPreview();
}

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

  // ✅ On envoie les photos dans l'ordre voulu par l'utilisateur
  addFiles.forEach(file => formData.append('photos[]', file));

  try {
    const res = await fetch(BASE + '/cloudinary/upload_photo.php', { method: 'POST', body: formData });
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

async function deleteMaison(id, nom) {
  if (!confirm(`Supprimer "${nom}" ?`)) return;
  const res = await fetch(`${BASE}/controller/MaisonController.php?action=delete&id=${id}`, { method: 'DELETE' });
  const json = await res.json();
  if (json.success) loadMaisons();
  else alert('Erreur lors de la suppression');
}

function showMsg(text, type) {
  const el = document.getElementById('form-msg');
  el.textContent = text;
  el.className = type === 'ok' ? 'msg-ok' : 'msg-err';
}
function showEditMsg(text, type) {
  const el = document.getElementById('edit-msg');
  el.textContent = text;
  el.className = type === 'ok' ? 'msg-ok' : 'msg-err';
}

// ─── MAP AJOUTER ─────────────────────────────────────────────────────────────
let map, marker;
function initMap() {
  if (map) return;
  setTimeout(() => {
    map = L.map('map').setView([36.4, 10.61], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    map.on('click', function(e) {
      const lat = e.latlng.lat.toFixed(7);
      const lng = e.latlng.lng.toFixed(7);
      if (marker) marker.setLatLng(e.latlng);
      else marker = L.marker(e.latlng).addTo(map);
      document.getElementById('f-lat').value = lat;
      document.getElementById('f-lng').value = lng;
      document.getElementById('map-coords').innerHTML = `📍 <strong>Lat:</strong> ${lat} &nbsp;|&nbsp; <strong>Lng:</strong> ${lng}`;
    });
  }, 200);
}

// ─── MODAL MODIFIER ──────────────────────────────────────────────────────────
let mapEdit, markerEdit;
let editSortable = null;

function editMaison(id) {
  const m = allMaisons.find(x => x.id === id);
  if (!m) return;
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
  document.getElementById('edit-msg').className = '';

  // ✅ Charger les photos existantes avec drag & drop ordre
  const section = document.getElementById('existing-photos-section');
  const grid    = document.getElementById('photos-ordre-grid');
  grid.innerHTML = '<span style="color:#aaa;font-size:13px">Chargement...</span>';
  section.style.display = 'block';

  fetch(BASE + '/cloudinary/get_photos.php?maison_id=' + m.id)
    .then(r => r.json())
    .then(data => {
      grid.innerHTML = '';
      if (!data.success || !data.photos.length) {
        section.style.display = 'none';
        return;
      }
      data.photos.forEach((p, i) => {
        const div = document.createElement('div');
        div.className = 'photo-ordre-item';
        div.dataset.id = p.id;
        div.innerHTML = `
          <img src="${p.url}" alt="photo ${i+1}">
          <span class="photo-ordre-num">${i + 1}</span>
          <button class="photo-ordre-del" onclick="deletePhoto(${p.id}, this)">✕</button>
          <div class="drag-label">☰ glisser</div>
        `;
        grid.appendChild(div);
      });

      // ✅ Init SortableJS sur la grille des photos existantes
      if (editSortable) { editSortable.destroy(); editSortable = null; }
      editSortable = Sortable.create(grid, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: () => {
          // Mettre à jour les numéros visuels
          grid.querySelectorAll('.photo-ordre-item').forEach((el, idx) => {
            el.querySelector('.photo-ordre-num').textContent = idx + 1;
          });
        }
      });
    });

  document.getElementById('modal-edit').classList.add('active');
  setTimeout(() => {
    if (!mapEdit) {
      mapEdit = L.map('map-edit').setView([m.latitude || 36.4, m.longitude || 10.61], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapEdit);
      mapEdit.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(7);
        const lng = e.latlng.lng.toFixed(7);
        if (markerEdit) markerEdit.setLatLng(e.latlng);
        else markerEdit = L.marker(e.latlng).addTo(mapEdit);
        document.getElementById('e-lat').value = lat;
        document.getElementById('e-lng').value = lng;
        document.getElementById('map-edit-coords').innerHTML = `📍 <strong>Lat:</strong> ${lat} &nbsp;|&nbsp; <strong>Lng:</strong> ${lng}`;
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

// ✅ Sauvegarder l'ordre des photos existantes (modal Modifier)
async function sauvegarderOrdre() {
  const btn      = document.getElementById('btn-save-ordre');
  const feedback = document.getElementById('ordre-feedback');
  btn.disabled   = true;
  btn.textContent = '⏳...';

  const items  = document.querySelectorAll('#photos-ordre-grid .photo-ordre-item');
  const photos = Array.from(items).map((el, i) => ({
    id: parseInt(el.dataset.id),
    ordre: i
  }));

  try {
    const res  = await fetch(BASE + '/cloudinary/update_ordre.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ photos })
    });
    const data = await res.json();
    feedback.className = 'ordre-feedback show ' + (data.success ? 'ok' : 'err');
    feedback.textContent = data.success ? '✅ Ordre sauvegardé !' : '❌ ' + (data.error || 'Erreur');
  } catch (e) {
    feedback.className = 'ordre-feedback show err';
    feedback.textContent = '❌ Erreur réseau';
  }

  btn.disabled = false;
  btn.textContent = '💾 Sauvegarder l\'ordre';
  setTimeout(() => { feedback.className = 'ordre-feedback'; }, 3000);
}

function closeEditModal() {
  document.getElementById('modal-edit').classList.remove('active');
  document.getElementById('e-photos').value = '';
  document.getElementById('preview-edit-imgs').innerHTML = '';
  document.getElementById('photos-ordre-grid').innerHTML = '';
  document.getElementById('existing-photos-section').style.display = 'none';
  if (editSortable) { editSortable.destroy(); editSortable = null; }
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
    const res = await fetch(BASE + '/cloudinary/update_maison.php', { method: 'POST', body: formData });
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

async function deletePhoto(photoId, btn) {
  if (!confirm('Supprimer cette photo ?')) return;
  const res = await fetch(BASE + '/cloudinary/delete_photo.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: photoId })
  });
  const json = await res.json();
  if (json.success) {
    btn.parentElement.remove();
    // Mettre à jour les numéros après suppression
    document.querySelectorAll('#photos-ordre-grid .photo-ordre-item').forEach((el, i) => {
      el.querySelector('.photo-ordre-num').textContent = i + 1;
    });
  } else {
    alert('Erreur suppression photo');
  }
}

// INIT
loadMaisons();
</script>
</body>
</html>