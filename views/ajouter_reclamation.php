<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754"> 
    <meta charset="UTF-8">
    <title>Déposer une réclamation - ONAS</title>
    <link rel="stylesheet" href="style.css">
    
    <!-- مكتبة الخريطة Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        #camera-container { text-align: center; margin-top: 15px; }
        #video { width: 100%; max-width: 400px; border: 2px solid #ccc; border-radius: 8px; }
        #canvas { display: none; }
        #photo-preview { display: none; width: 100%; max-width: 400px; margin-top: 10px; border: 2px solid #28a745; border-radius: 8px; }
        .btn-snap { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;}
        #map { height: 280px; width: 100%; max-width: 400px; border-radius: 8px; border: 2px solid #ccc; margin-top: 10px; cursor: pointer; }
        .btn-gps-locate { background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; margin-top: 8px; font-weight: bold; display: inline-block; width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Déposer une nouvelle réclamation</h2>
    
    <form action="index.php?action=ajouter" method="POST">
        
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
            <input type="text" name="repere" id="repere" class="form-control" placeholder="Ex: à côté de la pharmacie, rue X..." required>
        </div>

        <!-- الخريطة التفاعلية مع زر تحديد الموقع بدقة -->
        <div class="form-group">
            <label>Localisation exacte * :</label>
            <div id="map"></div>
            <button type="button" class="btn-gps-locate" id="locate-btn">🎯 اضغط هنا لجلب موقعك بدقة (GPS)</button>
            <small style="color: #0d6efd; display: block; margin-top: 5px;" id="map-status">انقر على الزر أعلاه لتثبيت مكانك بالمليمتر، أو انقر مباشرة على الخريطة.</small>
        </div>

        <div id="camera-container">
            <label>Photo du problème (Directe) * :</label><br>
            <video id="video" autoplay playsinline></video>
            <button type="button" id="snap" class="btn-snap">📸 Prendre la photo</button>
            <canvas id="canvas"></canvas>
            <img id="photo-preview" alt="Aperçu de la photo">
        </div>

        <!-- حقول مخفية للـ GPS والتصويرة -->
        <input type="hidden" name="photo_base64" id="photo_base64" required>
        <input type="hidden" name="latitude" id="lat" required>
        <input type="hidden" name="longitude" id="lng" required>

        <button type="submit" class="btn-submit" style="margin-top: 20px;">Envoyer</button>
    </form>
</div>

<script>
    let defaultLat = 36.7256;
    let defaultLng = 9.1817;

    let map = L.map('map').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    let marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

    function setLocation(lat, lng, message) {
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 18); // Zoom 18 قرب كبير جداً للدقة بالمليمتر
        if(message) {
            document.getElementById('map-status').innerText = message;
            document.getElementById('map-status').style.color = "green";
        }
    }

    // دالة لجلب الموقع الحقيقي بدقة عالية
    function getExactLocation() {
        if (navigator.geolocation) {
            document.getElementById('map-status').innerText = "⏳ جاري تحديد موقعك بدقة عالية عبر الـ GPS...";
            document.getElementById('map-status').style.color = "orange";

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;
                    setLocation(lat, lng, "📍 تم تحديد مكانك بدقة بالمليمتر بنجاح!");
                },
                function(error) {
                    console.error("Erreur GPS :", error.message);
                    document.getElementById('map-status').innerText = "⚠️ تعذر جلب الموقع أوتوماتيكياً. يرجى النقر يدوياً على مكانك في الخريطة.";
                    document.getElementById('map-status').style.color = "red";
                },
                {
                    enableHighAccuracy: true, // إجبار التليفون على استعمل الـ GPS الحقيقي مش الـ IP
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        } else {
            alert("Le navigateur ne supporte pas la géolocalisation.");
        }
    }

    // محاولة جلب الموقع أول ما تفتح الصفحة
    getExactLocation();

    // عند الضغط على الزر، يجيب الموقع بدقة فوراً
    document.getElementById('locate-btn').addEventListener('click', function() {
        getExactLocation();
    });

    // النقر اليدوي على الخريطة لتعديل البلاصة بالتدقيق
    map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng, "📍 تم ضبط المكان يدوياً على الخريطة بنجاح.");
    });

    // ==========================================
    // الكاميرا الحية
    // ==========================================
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snapBtn = document.getElementById('snap');
    const photoPreview = document.getElementById('photo-preview');
    const photoInput = document.getElementById('photo_base64');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(function(stream) {
            video.srcObject = stream;
        })
        .catch(function(err) {
            console.log("Erreur caméra: " + err);
        });

    snapBtn.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const dataURL = canvas.toDataURL('image/jpeg');
        photoInput.value = dataURL;
        photoPreview.src = dataURL;
        photoPreview.style.display = 'block';
        video.style.display = 'none';
        snapBtn.innerText = "🔄 Reprendre la photo";
        
        snapBtn.onclick = function() {
            video.style.display = 'block';
            photoPreview.style.display = 'none';
            photoInput.value = "";
            snapBtn.innerText = "📸 Prendre la photo";
            location.reload(); 
        };
    });
</script>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js');
    }
</script>
</body>
</html>