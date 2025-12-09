<?php
// Daten laden
$data = json_decode(file_get_contents('content.json'), true);

// Hilfsfunktion um Bilder korrekt anzuzeigen (Fallback auf Assets wenn nicht in Uploads)
function getImgPath($filename) {
    if(file_exists('uploads/' . $filename)) return 'uploads/' . $filename;
    return 'assets/covers/' . $filename; // Fallback für alte Bilder
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Holza | Offizielle Website</title>
        <link rel="stylesheet" href="styles/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chivo:wght@100..900&family=Inter:wght@100..900&family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.min.css">
        
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '818146260754677');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=818146260754677&ev=PageView&noscript=1"/></noscript>
    </head>
    <body>
        <img src="./assets/logos/logo_red_lowres.png" preload alt="picture" style="display: none">
        <div id="preloader"></div>
        
        <div class="slide-menu">
            <ul>
                <li class="hover-mouse" onclick="scrollToBottom(-1)">Home</li>
                <li class="hover-mouse" onclick="scrollToBottom(0)">Live</li>
                <li class="hover-mouse" onclick="scrollToBottom(1)">Musik</li>
                <li class="hover-mouse" onclick="scrollToBottom(2)">Videos</li>
                <li class="hover-mouse" onclick="scrollToBottom(3)">Ich</li>
                <li class="hover-mouse" onclick="scrollToBottom(4)">Kontakt</li>
            </ul>
        </div>
        
        <div class="socials">
            <a href="https://youtu.be/1rVY1SlzHs0?si=qTlLwbv87WpFm-nX" id="youtube-icon"><img src="./assets/socials/youtube.png" class="filter hover-mouse"></a>
            <a href="https://www.facebook.com/p/Holza-100085100498084/"><img src="./assets/socials/facebook.png" class="filter hover-mouse"></a>
            <a href="https://www.instagram.com/holza.oida/"><img src="./assets/socials/instagram.png" class="filter hover-mouse"></a>
            <a href="https://www.tiktok.com/@holza.oida"><img src="./assets/socials/tiktok.png" class="filter hover-mouse"></a>
            <a href="https://open.spotify.com/album/7KaBxH7OOUR0IEtKASuk6C"><img src="./assets/socials/spotify.png" class="filter hover-mouse"></a>
        </div>

        <div class="menu hover-mouse" onclick="slideMenu()">
            <div class="menu-line-one"></div>
            <div class="menu-line-two"></div>
        </div>

        <h1>
            <img src="./assets/tv_and_gif/temp-tv-placeholder.png" alt="TV" class="tv" id="animated-gif">
        </h1>

        <div class="arrow-down">
            <img src="./assets/arrow_down_png.png" alt="arrow down" onclick="scrollToBottom(0)" class="bounce-7 hover-mouse">
        </div>

        <div class="fit-content">
            <div id="termine"></div>
            <button class="show-me-more hover-mouse" onclick="writeDates()">ZEIG MIR MEHR!</button>
        </div>
        
        <div class="image-pause">
            <img src="./assets/DSC00029_3.png" alt="Band">
        </div>

        <div class="fit-content">
            <div class="slider">
                <?php foreach($data['slider'] as $item): ?>
                <div class="slider_item">
                    <div class="ticket-flex covers">
                        <div class="cover-image">
                            <img src="<?= getImgPath($item['image']) ?>" alt="cover">
                        </div>
                        <h3><?= $item['title'] ?></h3>
                        
                        <a href="<?= $item['link_main'] ?>" class="hover-mouse" 
                           onclick="fbq('track', 'ViewContent', {content_name: 'Stream <?= $item['title'] ?>'});">
                           <?= $item['text_main'] ?>
                        </a>
                        
                        <a href="<?= $item['link_sec'] ?>" class="hover-mouse button-secondary" 
                           onclick="fbq('track', 'InitiateCheckout', {content_name: 'Secondary <?= $item['title'] ?>'});">
                           <?= $item['text_sec'] ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="image-pause">
            <img src="./assets/holza_zwischenbild_sticker.png" alt="Polaroids">
        </div>

        <div class="music-videos hover-mouse">
            <?php foreach($data['videos'] as $index => $video): ?>
            <div class="video-container <?= ($index === 0) ? 'active' : 'disabled' ?>" onclick="changeVideoPreview(<?= $index ?>)">
                <div class="orange-overlay"></div>
                <img src="<?= getImgPath($video['image']) ?>" alt="preview">
                <a href="<?= $video['link'] ?>">
                    <img src="./assets/socials/youtube.png" alt="play">
                </a>
                <div>
                    <p><?= $video['title'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="image-pause">
            <img src="./assets/Holza_Studio_Floor_1.png" alt="Studio">
        </div>
        
        <div class="fit-content flex-who">
            <div class="who-am-i">
                <h2><?= $data['bio']['headline'] ?></h2>
              
                <p><?= nl2br($data['bio']['text_intro']) ?></p>
                <p><?= $data['bio']['text_parents'] ?></p>
                <p><?= nl2br($data['bio']['text_full']) ?></p>
            </div>
        </div>

        <div class="fit-content contact">
            <img src="./assets/LIFELOVING_CIRCLE.png" alt="Circle" class="circle-letters rotating">
            <div class="space-creator"></div>
            <div class="book-me">
                <h3>BOOK ME</h3>
                <p><?= $data['contact']['agentur'] ?></p>
                <p><?= $data['contact']['person'] ?></p>
                <p><?= $data['contact']['email'] ?></p>
            </div>
            <button class="email-button hover-mouse" onclick="fbq('track', 'Lead'); location.href='mailto:<?= $data['contact']['email'] ?>'">
                <img src="./assets/socials/email-icon.png" alt="email">
            </button>
        </div>

        <div class="footer">
            <img src="./assets/logos/logo_black.png" alt="Logo" onclick="bodyScrollToTop()" class="hover-mouse">
            <div class="socials">
               <a href="https://www.instagram.com/holza.oida/"><img src="./assets/socials/instagram.png" class="filter hover-mouse"></a>
               </div>
            <div>
                <a href="datenschutz.html" class="hover-mouse">Datenschutz</a> | <a href="impresum.html" class="hover-mouse">Impressum</a>
            </div>
        </div>

        <script>
            document.body.classList.add("body-open-menu");
            var loader = document.getElementById("preloader");
            window.addEventListener("load", function(){
                loader.style.display = "none";
                document.body.classList.remove("body-open-menu");
                waitUntilGifSwitch();
            });
        </script>
        <script src="./javascript/fetch_dates.js"></script>
        <script src="./javascript/animations.js"></script>
        <script src="./javascript/scrollTo.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/min/tiny-slider.js"></script>
        <script>
            var slider = tns({
                container : '.slider',
                items: 1,
                center: true,
                loop: true,
                swipeAngle: false,
                speed: 400,
                nav: false,
                mouseDrag: true,
                controlsPosition: "bottom",
                controlsText: ["<", ">"],
                "responsive": {
                    "920": { items: 2 },
                    "1400": { items: 3 }
                },
            })
        </script>
    </body>
</html>