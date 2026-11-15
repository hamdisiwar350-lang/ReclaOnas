<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Déposer une réclamation - ONAS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #camera-container { text-align: center; margin-top: 15px; }
        #video { width: 100%; max-width: 400px; border: 2px solid #ccc; border-radius: 8px; }
        #canvas { display: none; }
        #photo-preview { display: none; width: 100%; max-width: 400px; margin-top: 10px; border: 2px solid #28a745; border-radius: 8px; }
        .btn-snap { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        #map { height: 280px; width: 100%; max-width: 400px; border-radius: 8px; border: 2px solid #ccc; margin-top: 10px; cursor: pointer; }
        .btn-gps-locate { background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; margin-top: 8px; font-weight: bold; display: inline-block; width: 100%; max-width: 400px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Déposer une nouvelle réclamation</h2>
    <form action="index.php?action=ajouter" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group">
            <label for="type_probleme">Sujet de la réclamation * :</label>
            <select name="type_probleme" id="type_probleme" required class="form-control">
                <option value="" disabled selected>Choisir le sujet</option>
                <option value="Bouchon sur réseau">Bouchon sur réseau</option>
                <option value="Bouchon sur branchement et retour des eaux">Bouchon sur branchement et retour des eaux</option>
                <option value="Affaissement, casse du réseau">Affaissement, casse du réseau</option>
                <option value="Odeurs">Odeurs</option>
                <option value="Débordements d'eau usées">Débordements d'eau usées</option>
                <option value="Autres">Autres</option>
            </select>
        </div>
        <div class="form-group">
            <label for="repere">Point de repère (Adresse exacte) * :</label>
            <input type="text" name="repere" id="repere" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Localisation exacte * :</label>
            <div id="map"></div>
            <button type="button" class="btn-gps-locate" id="locate-btn">🎯 Obtenir ma position GPS</button>
            <small style="color: #0d6efd; display: block; margin-top: 5px;" id="map-status">Cliquez sur le bouton ou la carte.</small>
        </div>
        <div id="camera-container">
            <label>Photo du problème * :</label><br>
            <video id="video" autoplay playsinline></video>
            <button type="button" id="snap" class="btn-snap">📸 Prendre la photo</button>
            <canvas id="canvas"></canvas>
            <img id="photo-preview" alt="Aperçu de la photo">
        </div>
        <input type="hidden" name="photo_base64" id="photo_base64" required>
        <input type="hidden" name="latitude" id="lat" required>
        <input type="hidden" name="longitude" id="lng" required>
        <button type="submit" class="btn-submit" style="margin-top: 20px;">Envoyer</button>
    </form>
</div>
<script>
let defaultLat = 36.7256, defaultLng = 9.1817;
let map = L.map('map').setView([defaultLat, defaultLng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
function setLocation(lat, lng, message) {
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 18);
    document.getElementById('map-status').innerText = message;
    document.getElementById('map-status').style.color = 'green';
}
function getExactLocation() {
    if (!navigator.geolocation) return;
    document.getElementById('map-status').innerText = 'Détermination de votre position...';
    navigator.geolocation.getCurrentPosition(function(position) {
        setLocation(position.coords.latitude, position.coords.longitude, 'Position définie.');
    }, function() {
        document.getElementById('map-status').innerText = 'Position indisponible : cliquez sur la carte.';
        document.getElementById('map-status').style.color = 'red';
    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
}
getExactLocation();
document.getElementById('locate-btn').addEventListener('click', getExactLocation);
map.on('click', function(e) { setLocation(e.latlng.lat, e.latlng.lng, 'Position définie manuellement.'); });
const video = document.getElementById('video'), canvas = document.getElementById('canvas'), snapBtn = document.getElementById('snap'), preview = document.getElementById('photo-preview'), photoInput = document.getElementById('photo_base64');
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({video: {facingMode: 'environment'}}).then(function(stream) { video.srcObject = stream; }).catch(function() {});
}
snapBtn.addEventListener('click', function() {
    if (!video.videoWidth || !video.videoHeight) return;
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    photoInput.value = canvas.toDataURL('image/jpeg', 0.85);
    preview.src = photoInput.value; preview.style.display = 'block'; video.style.display = 'none'; snapBtn.innerText = '🔄 Reprendre la photo';
    snapBtn.onclick = function() { video.style.display = 'block'; preview.style.display = 'none'; photoInput.value = ''; snapBtn.innerText = '📸 Prendre la photo'; };
});
</script>
</body>
</html>