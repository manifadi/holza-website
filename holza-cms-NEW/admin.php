<?php
session_start();
$password = "holza"; // DEIN PASSWORT
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
function getImg($path) { return file_exists('uploads/'.$path) ? 'uploads/'.$path : 'assets/covers/'.$path; }
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holza CMS | Editor</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Chivo:wght@100..900&family=Pacifico&display=swap" rel="stylesheet">

    <style>
        /* ADMIN BAR */
        body { margin-top: 70px !important; }
        .admin-bar {
            position: fixed; top: 0; left: 0; width: 100%; height: 60px;
            background: #1a1616; border-bottom: 3px solid #FF4C29;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 30px; box-sizing: border-box; z-index: 10000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        }
        
        .brand-area { display: flex; align-items: center; gap: 15px; color: white; }
        .brand-logo { height: 30px; width: auto; }
        .brand-text { font-family: 'Chivo', sans-serif; font-weight: 300; opacity: 0.6; font-size: 14px; border-left: 1px solid #555; padding-left: 15px; letter-spacing: 1px;}

        .right-area { display: flex; align-items: center; gap: 20px; }
        .action-area { display: flex; align-items: center; gap: 10px; }

        /* BUTTONS */
        .cms-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 0 16px; border-radius: 4px; border: none; /* Padding angepasst für fixe Höhe */
            font-family: 'Chivo', sans-serif; font-size: 14px; cursor: pointer;
            transition: all 0.2s ease; text-decoration: none; height: 36px; box-sizing: border-box;
            line-height: 1; /* Wichtig für vertikale Zentrierung */
        }
        
        /* Secondary Button (Vorschau) */
        .btn-secondary { background: transparent; color: #ebe8e8; border: 1px solid #ebe8e8; }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); }

        /* Icon Button (Logout) */
        .btn-icon-only { width: fit-content; background: transparent; color: #666; padding: 0 8px; border:none; }
        .btn-icon-only:hover { color: #FF4C29; }

        /* Primary Button (Speichern) */
        .btn-save { background: #FF4C29; color: white; font-weight: bold; border: none;}
        .btn-save:hover { background: #ff6b4a; box-shadow: 0 0 15px rgba(255, 76, 41, 0.4); }

        /* EDITIERBARE BEREICHE */
        [contenteditable="true"] {
            outline: none; border-bottom: 1px dashed transparent;
            transition: all 0.2s; cursor: text;
        }
        [contenteditable="true"]:hover { border-bottom-color: #FF4C29; }
        [contenteditable="true"]:focus { background: rgba(255, 76, 41, 0.05); border-bottom: 2px solid #FF4C29; }

        /* BILD OVERLAY */
        .img-edit-wrap { position: relative; display: block; overflow: hidden; border-radius: 4px; cursor: pointer; }
        .img-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(26, 22, 22, 0.7); backdrop-filter: blur(3px);
            opacity: 0; transition: opacity 0.3s;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;
        }
        .img-edit-wrap:hover .img-overlay { opacity: 1; }
        .icon-upload { width: 30px; height: 30px; margin-bottom: 5px; fill: #FF4C29; }

        /* LINK INPUTS */
        .link-editor {
            margin-top: 5px; margin-bottom: 15px;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .link-icon { width: 14px; height: 14px; opacity: 0.5; }
        .link-input {
            border: none; border-bottom: 1px solid #ccc;
            background: transparent; font-family: monospace; font-size: 11px;
            color: #555; width: 80%; text-align: center; padding: 2px;
        }
        .link-input:focus { outline: none; border-bottom-color: #FF4C29; color: #000; }

        /* FORMATTING TOOLBAR (Bold/Italic) */
        #formatting-toolbar {
            position: absolute; z-index: 20000;
            background: #1a1616; border-radius: 4px; padding: 5px;
            display: flex; gap: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            opacity: 0; pointer-events: none; transition: opacity 0.2s;
            top: 0; left: 0; /* Positioned by JS */
        }
        #formatting-toolbar.show { opacity: 1; pointer-events: auto; }
        .format-btn {
            background: transparent; border: none; color: white;
            width: 30px; height: 30px; border-radius: 3px; cursor: pointer;
            font-weight: bold; font-family: serif;
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    SPEICHERN
                </button>

                <button class="cms-btn btn-secondary" onclick="window.open('index2.php', '_blank')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Vorschau
                </button>
                
                <a href="logout.php" class="cms-btn btn-icon-only" title="Logout" style="border-left:1px solid #333; padding-left:15px; margin-left:5px;">
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

    <div style="height:50px;"></div>

    <div class="fit-content">
        <div class="slider" style="display:flex; flex-wrap:wrap; justify-content:center; gap:30px;">
            <?php foreach($data['slider'] as $i => $item): ?>
            <div class="slider_item" style="flex: 0 1 350px; max-width:350px; margin-bottom:40px;">
                <div class="ticket-flex covers" style="height:100%; display:flex; flex-direction:column;">
                    
                    <div class="cover-image img-edit-wrap" onclick="triggerUpload('slider', <?= $i ?>, 'image')">
                        <img src="<?= getImg($item['image']) ?>" alt="cover" id="img-slider-<?= $i ?>" style="width:100%; border-radius:5px;">
                        <div class="img-overlay">
                            <svg class="icon-upload" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Bild Ändern
                        </div>
                    </div>
                    
                    <h3 contenteditable="true" data-path="slider.<?= $i ?>.title" style="margin-top:15px;"><?= $item['title'] ?></h3>
                    
                    <a href="#" class="hover-mouse" style="margin-top:auto;" contenteditable="true" data-path="slider.<?= $i ?>.text_main">
                       <?= $item['text_main'] ?>
                    </a>
                    <div class="link-editor">
                        <svg class="link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        <input type="text" value="<?= $item['link_main'] ?>" data-path="slider.<?= $i ?>.link_main" class="link-input" placeholder="https://...">
                    </div>

                    <a href="#" class="hover-mouse button-secondary" contenteditable="true" data-path="slider.<?= $i ?>.text_sec">
                       <?= $item['text_sec'] ?>
                    </a>
                     <div class="link-editor">
                        <svg class="link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        <input type="text" value="<?= $item['link_sec'] ?>" data-path="slider.<?= $i ?>.link_sec" class="link-input" placeholder="https://...">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="width:100px; height:2px; background:#eee; margin:20px auto;"></div>

    <div class="music-videos-admin" style="display:flex; width: 90vw; margin: 40px auto; gap: 20px; justify-content: space-between; padding: 0;">
        <?php foreach($data['videos'] as $i => $video): ?>
        <div class="video-container-admin" style="flex: 1; position:relative; border-radius:5px; overflow:hidden; aspect-ratio: 16/9; background:#000;">
            
            <div class="img-edit-wrap" onclick="triggerUpload('videos', <?= $i ?>, 'image')" style="width:100%; height:100%;">
                <img src="<?= getImg($video['image']) ?>" id="img-videos-<?= $i ?>" style="width:100%; height:100%; object-fit:cover; opacity:0.7;">
                <div class="img-overlay">
                    <svg class="icon-upload" style="fill:white;" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
            </div>
            
            <div style="position:absolute; bottom:0; left:0; width:100%; background:linear-gradient(to top, black, transparent); padding:20px 10px 10px 10px; box-sizing:border-box;">
                <p contenteditable="true" data-path="videos.<?= $i ?>.title" style="color:white; margin:0 0 5px 0; font-size:14px; font-weight:bold; text-shadow:0 1px 2px black;"><?= $video['title'] ?></p>
                <div style="display:flex; align-items:center; background:rgba(255,255,255,0.15); border-radius:3px; padding:2px 5px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="opacity:0.7; margin-right:5px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <input type="text" value="<?= $video['link'] ?>" data-path="videos.<?= $i ?>.link" style="width:100%; font-size:10px; background:transparent; color:white; border:none; font-family:monospace;">
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <div style="width:100px; height:2px; background:#eee; margin:20px auto;"></div>

    <div class="fit-content flex-who">
        <div class="who-am-i" style="border: 1px dashed #eee; padding:20px; margin-bottom: 40px;">
            <h2 contenteditable="true" data-path="bio.headline" style="margin-top:0;"><?= $data['bio']['headline'] ?></h2>
            <p contenteditable="true" data-path="bio.text_intro"><?= nl2br($data['bio']['text_intro']) ?></p>
            <p contenteditable="true" data-path="bio.text_parents" style="font-style:italic; color:#666;"><?= $data['bio']['text_parents'] ?></p>
            <p contenteditable="true" data-path="bio.text_full" style="white-space: pre-wrap;"><?= $data['bio']['text_full'] ?></p>
        </div>
    </div>

    <div class="fit-content contact">
        <div class="book-me" style="border-color:#FF4C29;">
            <h3>BOOK ME</h3>
            <p contenteditable="true" data-path="contact.agentur"><?= $data['contact']['agentur'] ?></p>
            <p contenteditable="true" data-path="contact.person"><?= $data['contact']['person'] ?></p>
            <p contenteditable="true" data-path="contact.email"><?= $data['contact']['email'] ?></p>
        </div>
    </div>
    
    <div style="height:100px;"></div>

    <script>
        let currentData = <?= json_encode($data) ?>;
        
        // --- FORMATTING TOOLBAR LOGIC ---
        const toolbar = document.getElementById('formatting-toolbar');

        function checkSelection() {
            const selection = window.getSelection();
            if (selection.rangeCount === 0 || selection.isCollapsed || !selection.toString().trim()) { hideToolbar(); return; }

            const range = selection.getRangeAt(0);
            const ancestor = range.commonAncestorContainer.nodeType === 3 ? range.commonAncestorContainer.parentNode : range.commonAncestorContainer;

            if (ancestor.closest('[contenteditable="true"]')) {
                const rect = range.getBoundingClientRect();
                showToolbar(rect);
            } else {
                hideToolbar();
            }
        }

        function showToolbar(rect) {
            const top = rect.top + window.scrollY - 40;
            const left = rect.left + window.scrollX + (rect.width / 2) - (toolbar.offsetWidth / 2);
            toolbar.style.top = `${top}px`;
            toolbar.style.left = `${left}px`;
            toolbar.classList.add('show');
        }

        function hideToolbar() {
            toolbar.classList.remove('show');
        }

        function formatDoc(cmd) {
            // Führt den Befehl auf der aktuellen Selektion aus
            document.execCommand(cmd, false, null);
        }

        // Events für Toolbar
        document.addEventListener('selectionchange', checkSelection);
        document.addEventListener('mouseup', checkSelection);
        document.addEventListener('keyup', checkSelection);
        window.addEventListener('resize', hideToolbar);
        // ---------------------------


        // Helper to update data object
        function setByPath(obj, path, value) {
            var parts = path.split('.');
            var last = parts.pop();
            var target = parts.reduce((o, key) => o[key], obj);
            target[last] = value;
        }

        // Track changes in contenteditable
        document.querySelectorAll('[contenteditable]').forEach(el => {
            el.addEventListener('blur', function() {
                let path = this.getAttribute('data-path');
                // WICHTIG: Wir nutzen jetzt innerHTML statt innerText, damit <b> und <i> gespeichert werden
                setByPath(currentData, path, this.innerHTML); 
                document.getElementById('status').innerText = "ungespeicherte Änderungen...";
            });
        });

        // Track changes in inputs
        document.querySelectorAll('input[data-path]').forEach(el => {
            el.addEventListener('change', function() {
                let path = this.getAttribute('data-path');
                setByPath(currentData, path, this.value);
                document.getElementById('status').innerText = "ungespeicherte Änderungen...";
            });
        });

        // Upload Logic
        let uploadTarget = null; 
        function triggerUpload(section, index, key) {
            uploadTarget = { section, index, key };
            document.getElementById('globalFileInput').click();
        }

        document.getElementById('globalFileInput').addEventListener('change', function() {
            if(this.files.length === 0) return;
            let formData = new FormData();
            formData.append('file', this.files[0]);

            fetch('api.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(filename => {
                let imgId = 'img-' + uploadTarget.section + '-' + uploadTarget.index;
                document.getElementById(imgId).src = 'uploads/' + filename;
                currentData[uploadTarget.section][uploadTarget.index][uploadTarget.key] = filename;
                showToast("Bild aktualisiert!");
                document.getElementById('status').innerText = "ungespeicherte Änderungen...";
            })
            .catch(e => alert("Upload Fehler"));
        });

        // Save Logic
        function saveAll() {
            let btn = document.querySelector('.btn-save');
            let originalText = btn.innerHTML;
            btn.innerHTML = "Speichert...";
            
            let formData = new FormData();
            // JSON stringify with null, 2 for pretty print in file
            formData.append('json_data', JSON.stringify(currentData, null, 2));

            fetch('api.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(msg => {
                showToast("Erfolgreich gespeichert!");
                document.getElementById('status').innerText = "";
                btn.innerHTML = originalText;
            });
        }

        function showToast(msg) {
            let t = document.getElementById('toast');
            t.querySelector('span').innerText = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }
    </script>
</body>
</html>