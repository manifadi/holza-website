<?php
session_start();
$password = "Holza!Live2025#GitarrenSolo_Sicher";
$json_file = 'content.json';

// --- LOGIN LOGIK ---
if (isset($_POST['login'])) {
    if ($_POST['pass'] === $password) {
        $_SESSION['loggedin'] = true;
    } else {
        $error = "Falsches Passwort!";
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<body style="font-family:sans-serif; background:#1a1616; color:white; display:flex; justify-content:center; align-items:center; height:100vh;">
            <form method="post" style="text-align:center; background:#333; padding:40px; border-radius:10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <img src="./assets/logos/logo_red_lowres.png" style="width:100px; margin-bottom:20px;">
                <h2 style="color:#FF4C29; margin:0 0 20px 0;">CMS Login</h2>
                <input type="password" name="pass" placeholder="Passwort" style="padding:12px; border-radius:4px; border:none; width:200px;"><br><br>
                <button type="submit" name="login" style="background:#FF4C29; color:white; border:none; padding:12px 30px; cursor:pointer; font-weight:bold; border-radius:4px;">STARTEN</button>
                <p style="color:red; margin-top:10px;">'.($error ?? '').'</p>
            </form>
          </body>';
    exit;
}

// Lade aktuelle Daten
$data = json_decode(file_get_contents($json_file), true);

// Hilfsfunktion für Bilder
function getImg($path) { return file_exists('uploads/'.$path) ? 'uploads/'.$path : 'assets/covers/'.$path; }
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holza CMS | Editor</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Chivo:wght@100..900&family=Pacifico&display=swap" rel="stylesheet">

    <style>
        /* GLOBAL */
        body { margin-top: 80px !important; background-color: #f4f4f4; font-family: 'Chivo', sans-serif; }
        
        /* ADMIN BAR */
        .admin-bar {
            position: fixed; top: 0; left: 0; width: 100%; height: 70px;
            background: #1a1616; border-bottom: 3px solid #FF4C29;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2vw; box-sizing: border-box; z-index: 10000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        }
        
        .brand-area { display: flex; align-items: center; gap: 15px; color: white; }
        .brand-logo { height: 35px; width: auto; }
        .brand-text { 
            font-family: 'Chivo', sans-serif; font-weight: 300; opacity: 0.6; 
            font-size: 14px; border-left: 1px solid #555; padding-left: 15px; 
            letter-spacing: 1px; text-transform: uppercase;
        }

        .right-area { display: flex; align-items: center; gap: 20px; }
        .action-area { display: flex; align-items: center; gap: 12px; }

        /* BUTTONS */
        .cms-btn {
            display: flex; align-items: center; gap: 8px; justify-content: center;
            padding: 0 16px; border-radius: 4px; border: none;
            font-family: 'Chivo', sans-serif; font-size: 14px; cursor: pointer;
            transition: all 0.2s ease; text-decoration: none; height: 40px; box-sizing: border-box;
            line-height: 1; white-space: nowrap;
        }
        
        /* Vorschau Button (Secondary) */
        .btn-secondary { 
            background: #2a2a2a; color: #ebe8e8; border: 1px solid #444; 
        }
        .btn-secondary:hover { background: #333; border-color: #666; color: white; }

        /* Icon Button (Logout) */
        .btn-icon-only { 
            background: transparent; color: #666; padding: 0 10px; border:none; 
            width: fit-content; 
        }
        .btn-icon-only:hover { color: #FF4C29; }

        /* Primary Button (Speichern) */
        .btn-save { 
            background: #FF4C29; color: white; font-weight: bold; border: none; 
            padding: 0 24px; font-size: 15px;
        }
        .btn-save:hover { background: #ff6b4a; box-shadow: 0 0 15px rgba(255, 76, 41, 0.4); }

        /* CARD STYLES (ALBEN) - RESPONSIVE GRID */
        .album-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            width: 95%; max-width: 1400px; margin: 0 auto;
        }

        .album-card {
            background: white; border-radius: 8px; padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: relative;
            border: 1px solid #eee; display: flex; flex-direction: column;
        }
        
        .btn-delete {
            position: absolute; top: -10px; right: -10px;
            width: 28px; height: 28px; border-radius: 50%;
            background: #ff4757; color: white; border: none;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; transition: 0.2s; z-index: 10;
        }
        .album-card:hover .btn-delete { opacity: 1; top: -12px; right: -12px;}

        .card-add-new {
            min-height: 400px; border: 2px dashed #ccc; border-radius: 8px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; color: #aaa; transition: 0.2s; background: rgba(255,255,255,0.5);
        }
        .card-add-new:hover { border-color: #FF4C29; color: #FF4C29; background: white; }
        .icon-plus { width: 50px; height: 50px; margin-bottom: 10px; }


        /* EDITIERBARE BEREICHE & INPUTS */
        [contenteditable="true"] { outline: none; border-bottom: 1px dashed #ddd; transition: all 0.2s; cursor: text; min-height: 1.2em;}
        [contenteditable="true"]:hover { border-bottom-color: #FF4C29; }
        [contenteditable="true"]:focus { background: rgba(255, 76, 41, 0.05); border-bottom: 2px solid #FF4C29; }

        .link-editor {
            margin-top: 8px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            background: #f8f8f8; padding: 6px 10px; border-radius: 4px; border: 1px solid #eee;
        }
        .link-icon { width: 14px; height: 14px; opacity: 0.4; flex-shrink: 0; }
        .link-input {
            border: none; background: transparent; font-family: monospace; font-size: 11px;
            color: #666; width: 100%; padding: 0;
        }
        .link-input:focus { outline: none; color: #000; }

        .img-edit-wrap { position: relative; display: block; overflow: hidden; border-radius: 6px; cursor: pointer; aspect-ratio: 1/1; margin-bottom: 15px;}
        .img-edit-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .img-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0, 0.6); backdrop-filter: blur(2px);
            opacity: 0; transition: opacity 0.3s;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;
        }
        .img-edit-wrap:hover .img-overlay { opacity: 1; }
        .icon-upload { width: 24px; height: 24px; margin-bottom: 8px; fill: white; }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 95%; max-width: 1400px; margin: 0 auto;
        }
        .video-card-admin {
            position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 16/9; background: #000;
        }

        /* ABOUT GRID (NEU: 2 Spalten Layout) */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 350px; /* Text nimmt Platz, Kontakt ist fix */
            gap: 50px;
            width: 95%; max-width: 1400px; margin: 0 auto 100px auto;
            align-items: start;
        }

        .bio-card {
            background: white; padding: 50px; border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;
        }

        .contact-card {
            background: white; padding: 40px; border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;
            border-top: 4px solid #FF4C29; position: sticky; top: 100px;
        }

        @media (max-width: 900px) {
            .about-grid { grid-template-columns: 1fr; } /* Untereinander auf Mobile */
            .contact-card { position: static; }
        }

        /* FORMATTING TOOLBAR */
        #formatting-toolbar {
            position: absolute; z-index: 20000;
            background: #1a1616; border-radius: 4px; padding: 5px;
            display: flex; gap: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            opacity: 0; pointer-events: none; transition: opacity 0.2s; top: 0; left: 0;
        }
        #formatting-toolbar.show { opacity: 1; pointer-events: auto; }
        .format-btn {
            background: transparent; border: none; color: white;
            width: 30px; height: 30px; border-radius: 3px; cursor: pointer; font-weight: bold; font-family: serif;
        }
        .format-btn:hover { background: #333; color: #FF4C29; }

        /* NOTIFICATION */
        #toast {
            position: fixed; bottom: 30px; right: 30px;
            background: #1a1616; color: white; border-left: 4px solid #FF4C29;
            padding: 15px 25px; border-radius: 4px; font-family: sans-serif; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: translateY(100px); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10001; display: flex; align-items: center; gap: 10px;
        }
        #toast.show { transform: translateY(0); }
        
        .section-header { 
            text-align: center; margin: 60px 0 30px 0; color: #aaa; 
            text-transform: uppercase; letter-spacing: 2px; font-size: 13px; font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="admin-bar">
        <div class="brand-area">
            <img src="./assets/logos/logo_red_lowres.png" class="brand-logo" alt="Holza Logo">
            <span class="brand-text">BEARBEITUNGSMODUS</span>
        </div>
        
        <div class="right-area">
             <span id="status" style="color:#666; font-size:12px;"></span>

            <div class="action-area">
                <button class="cms-btn btn-save" onclick="saveAll()">
                    SPEICHERN
                </button>
                
                <button class="cms-btn btn-secondary" onclick="window.open('index2.php', '_blank')">
                    Vorschau
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:4px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </button>
                
                <a href="logout.php" class="cms-btn btn-icon-only" title="Logout" style="border-left:1px solid #333; padding-left:15px; margin-left:10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div id="toast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Erfolgreich gespeichert!</span>
    </div>

    <div id="formatting-toolbar">
        <button class="format-btn" onmousedown="formatDoc('bold'); event.preventDefault();">B</button>
        <button class="format-btn" style="font-style:italic;" onmousedown="formatDoc('italic'); event.preventDefault();">I</button>
    </div>

    <input type="file" id="globalFileInput" style="display:none;" accept="image/*">

    <div class="section-header">Musik / Alben Slider</div>
    
    <div id="album-list" class="album-grid">
        </div>

    <div class="section-header">Videos (Startseite)</div>
    
    <div class="videos-grid">
        <?php foreach($data['videos'] as $i => $video): ?>
        <div class="video-card-admin">
            
            <div class="img-edit-wrap" onclick="triggerUpload('videos', <?= $i ?>, 'image')" style="width:100%; height:100%; margin:0; border-radius:0;">
                <img src="<?= getImg($video['image']) ?>" id="img-videos-<?= $i ?>" style="width:100%; height:100%; object-fit:cover; opacity:0.8;">
                <div class="img-overlay">
                    <svg class="icon-upload" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Bild Ändern
                </div>
            </div>
            
            <div style="position:absolute; bottom:0; left:0; width:100%; background:linear-gradient(to top, black, transparent); padding:20px 15px 15px 15px; box-sizing:border-box;">
                <p contenteditable="true" data-type="video" data-index="<?= $i ?>" data-key="title" style="color:white; margin:0 0 8px 0; font-size:15px; font-weight:bold; text-shadow:0 1px 3px rgba(0,0,0,0.8);"><?= $video['title'] ?></p>
                
                <div style="display:flex; align-items:center; background:rgba(255,255,255,0.1); border-radius:4px; padding:4px 8px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="opacity:0.7; margin-right:8px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <input type="text" value="<?= $video['link'] ?>" data-type="video" data-index="<?= $i ?>" data-key="link" style="width:100%; font-size:11px; background:transparent; color:white; border:none; font-family:monospace;">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <div class="section-header">Texte & Kontakt</div>
    
    <div class="about-grid">
        <div class="bio-card who-am-i">
            <h2 contenteditable="true" data-type="bio" data-key="headline" style="margin-top:0; color:#FF4C29; font-size:24px; text-transform:uppercase;"><?= $data['bio']['headline'] ?></h2>
            <p contenteditable="true" data-type="bio" data-key="text_intro" style="font-weight:500; font-size:16px;"><?= nl2br($data['bio']['text_intro']) ?></p>
            <p contenteditable="true" data-type="bio" data-key="text_parents" style="font-style:italic; color:#888; margin-top:30px;"><?= $data['bio']['text_parents'] ?></p>
            <p contenteditable="true" data-type="bio" data-key="text_full" style="white-space: pre-wrap; line-height:1.7; color:#333;"><?= $data['bio']['text_full'] ?></p>
        </div>
        
        <div class="contact-card book-me">
            <h3 style="margin:0 0 20px 0; font-size:18px; text-transform:uppercase; letter-spacing:1px;">BOOKING</h3>
            <p style="font-size:12px; color:#999; text-transform:uppercase; margin-bottom:5px;">Agentur</p>
            <p contenteditable="true" data-type="contact" data-key="agentur" style="margin:0 0 15px 0; font-weight:500;"><?= $data['contact']['agentur'] ?></p>
            
            <p style="font-size:12px; color:#999; text-transform:uppercase; margin-bottom:5px;">Ansprechpartner</p>
            <p contenteditable="true" data-type="contact" data-key="person" style="margin:0 0 15px 0; font-weight:500;"><?= $data['contact']['person'] ?></p>
            
            <p style="font-size:12px; color:#999; text-transform:uppercase; margin-bottom:5px;">E-Mail</p>
            <p contenteditable="true" data-type="contact" data-key="email" style="font-weight:bold; margin:0; color:#FF4C29; font-size:18px; word-break:break-all;"><?= $data['contact']['email'] ?></p>
        </div>
    </div>
    
    <script>
        let currentData = <?= json_encode($data) ?>;
        const albumListEl = document.getElementById('album-list');

        function renderAlbums() {
            let html = '';
            currentData.slider.forEach((item, index) => {
                let imgPath = item.image.includes('/') ? item.image : (item.image.includes('cover_') || item.image.includes('All_') ? 'assets/covers/'+item.image : 'uploads/'+item.image);
                
                html += `
                <div class="album-card">
                    <button class="btn-delete" onclick="deleteAlbum(${index})" title="Album löschen">✕</button>
                    <div class="cover-image img-edit-wrap" onclick="triggerUpload('slider', ${index}, 'image')">
                        <img src="${imgPath}" id="img-slider-${index}">
                        <div class="img-overlay">
                            <svg class="icon-upload" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Bild Ändern
                        </div>
                    </div>
                    <h3 contenteditable="true" data-type="slider" data-index="${index}" data-key="title" style="margin:5px 0 15px 0; font-size:16px; font-weight:bold;">${item.title}</h3>
                    <div style="margin-top:auto;">
                        <div style="background:black; color:white; padding:12px; text-align:center; border-radius:4px; font-weight:bold; font-size:13px; text-transform:uppercase; cursor:text;" 
                           contenteditable="true" data-type="slider" data-index="${index}" data-key="text_main">${item.text_main}</div>
                        <div class="link-editor">
                            <svg class="link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <input type="text" value="${item.link_main}" data-type="slider" data-index="${index}" data-key="link_main" class="link-input" placeholder="https://...">
                        </div>
                        <div style="border:1px solid black; color:black; padding:12px; text-align:center; border-radius:4px; font-weight:bold; font-size:13px; text-transform:uppercase; cursor:text;"
                           contenteditable="true" data-type="slider" data-index="${index}" data-key="text_sec">${item.text_sec}</div>
                        <div class="link-editor">
                            <svg class="link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <input type="text" value="${item.link_sec}" data-type="slider" data-index="${index}" data-key="link_sec" class="link-input" placeholder="https://...">
                        </div>
                    </div>
                </div>`;
            });

            html += `
            <div class="card-add-new" onclick="addAlbum()">
                <svg class="icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span style="font-weight:bold; font-size:14px; text-transform:uppercase;">ALBUM HINZUFÜGEN</span>
            </div>`;
            albumListEl.innerHTML = html;
        }

        function addAlbum() {
            currentData.slider.push({ "title": "Neues Album", "image": "placeholder.jpg", "link_main": "#", "text_main": "ANHÖREN", "link_sec": "#", "text_sec": "KAUFEN" });
            renderAlbums(); document.getElementById('status').innerText = "Neues Element - nicht vergessen zu speichern!";
        }

        function deleteAlbum(index) {
            if(confirm("Möchtest du dieses Album wirklich löschen?")) {
                currentData.slider.splice(index, 1); renderAlbums();
                document.getElementById('status').innerText = "Element gelöscht - bitte speichern!";
            }
        }
        renderAlbums();

        document.addEventListener('input', function(e) { if(e.target.matches('input[data-type]')) updateData(e.target, e.target.value); });
        document.addEventListener('blur', function(e) { if(e.target.matches('[contenteditable]')) updateData(e.target, e.target.innerHTML); }, true);

        function updateData(el, value) {
            let type = el.getAttribute('data-type'), key = el.getAttribute('data-key');
            if(type === 'slider') currentData.slider[el.getAttribute('data-index')][key] = value;
            else if(type === 'video') currentData.videos[el.getAttribute('data-index')][key] = value;
            else { if(type === 'bio') currentData.bio[key] = value; if(type === 'contact') currentData.contact[key] = value; }
            document.getElementById('status').innerText = "ungespeicherte Änderungen...";
        }

        let uploadTarget = null; 
        function triggerUpload(section, index, key) { uploadTarget = { section, index, key }; document.getElementById('globalFileInput').click(); }

        document.getElementById('globalFileInput').addEventListener('change', function() {
            if(this.files.length === 0) return;
            let formData = new FormData(); formData.append('file', this.files[0]);
            fetch('api.php', { method: 'POST', body: formData }).then(r => r.text()).then(filename => {
                if(uploadTarget.section === 'slider') { currentData.slider[uploadTarget.index][uploadTarget.key] = filename; renderAlbums(); } 
                else { 
                    let imgId = 'img-' + uploadTarget.section + '-' + uploadTarget.index;
                    document.getElementById(imgId).src = 'uploads/' + filename;
                    currentData[uploadTarget.section][uploadTarget.index][uploadTarget.key] = filename;
                }
                showToast("Bild aktualisiert!"); document.getElementById('status').innerText = "ungespeicherte Änderungen...";
            }).catch(e => alert("Upload Fehler"));
        });

        const toolbar = document.getElementById('formatting-toolbar');
        function checkSelection() {
            const selection = window.getSelection();
            if (selection.rangeCount === 0 || selection.isCollapsed || !selection.toString().trim()) { hideToolbar(); return; }
            const range = selection.getRangeAt(0);
            const ancestor = range.commonAncestorContainer.nodeType === 3 ? range.commonAncestorContainer.parentNode : range.commonAncestorContainer;
            if (ancestor.closest('[contenteditable="true"]')) { showToolbar(range.getBoundingClientRect()); } else { hideToolbar(); }
        }
        function showToolbar(rect) {
            const top = rect.top + window.scrollY - 40; const left = rect.left + window.scrollX + (rect.width / 2) - (toolbar.offsetWidth / 2);
            toolbar.style.top = `${top}px`; toolbar.style.left = `${left}px`; toolbar.classList.add('show');
        }
        function hideToolbar() { toolbar.classList.remove('show'); }
        function formatDoc(cmd) { document.execCommand(cmd, false, null); }
        document.addEventListener('selectionchange', checkSelection); document.addEventListener('mouseup', checkSelection); document.addEventListener('keyup', checkSelection); window.addEventListener('resize', hideToolbar);

        function saveAll() {
            let btn = document.querySelector('.btn-save'), originalText = btn.innerHTML; btn.innerHTML = "SPEICHERT...";
            let formData = new FormData(); formData.append('json_data', JSON.stringify(currentData, null, 2));
            fetch('api.php', { method: 'POST', body: formData }).then(r => r.text()).then(msg => {
                showToast("Erfolgreich gespeichert!"); document.getElementById('status').innerText = ""; btn.innerHTML = originalText;
            });
        }
        function showToast(msg) {
            let t = document.getElementById('toast'); t.querySelector('span').innerText = msg; t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }
    </script>
</body>
</html>