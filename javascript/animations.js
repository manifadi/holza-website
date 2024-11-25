var openMenu = false;

const menu_icons = document.getElementsByClassName("socials")[0].getElementsByTagName("img");
const hamburger_menu = document.getElementsByClassName("menu")[0];
const slide_in = document.getElementsByClassName("slide-menu")[0];

function slideMenu(){
    if(openMenu){
        document.body.classList.remove("body-open-menu");
        for(let i = 0; i < menu_icons.length; i++){
            menu_icons[i].classList.add("filter");
        }
        hamburger_menu.classList.remove("menu-closing");
        slide_in.classList.remove("menu-slide-in");
    }
    else{
        document.body.scrollIntoView({
            behavior: 'instant',
            block: 'start',
        });
        document.body.classList.add("body-open-menu");
        for(let i = 0; i < menu_icons.length; i++){
            menu_icons[i].classList.remove("filter");
        }
        hamburger_menu.classList.add("menu-closing");
        slide_in.classList.add("menu-slide-in");
    }
    openMenu = !openMenu;
}

function getMenuIsOpen(){
    return openMenu;
}

const videopreviews = document.getElementsByClassName("video-container");

function changeVideoPreview(number){
    for(let i = 0; i < videopreviews.length; i++){
        if(number == i){
            videopreviews[i].classList.remove('disabled');
            videopreviews[i].classList.add('active');
        }else{
            videopreviews[i].classList.remove('active');
            videopreviews[i].classList.add('disabled');
        }
    }
}

function waitUntilGifSwitch(){
    var animated_tv = document.getElementById("animated-gif");
    setTimeout(function() {
        animated_tv.src = "./assets/tv_and_gif/holza_tv_02_compressed.gif";
    }, 20000);
}

window.addEventListener("load", async function() {
    await downloadGifs();
    const animated_tv = document.getElementById("animated-gif");
    animated_tv.src = "./assets/tv_and_gif/holza_tv_01_compressed.gif";
    waitUntilGifSwitch();
});

async function downloadGifs() {
    const gifs = [
        "./assets/tv_and_gif/holza_tv_01_compressed.gif",
        "./assets/tv_and_gif/holza_tv_02_compressed.gif"
    ];
    const promises = gifs.map(gif => fetch(gif).then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.blob();
    }));
    await Promise.all(promises);
}