const scrollToFirst = document.getElementById("termine");
const scrollToSecond = document.getElementsByClassName("fit-content")[1];
const scrollToThird = document.getElementsByClassName("music-videos")[0];
const scrollToFourth = document.getElementsByClassName("flex-who")[0];
const scrollToFifth = document.getElementsByClassName("contact")[0];

var scrollTo = [];
scrollTo.push(scrollToFirst);
scrollTo.push(scrollToSecond);
scrollTo.push(scrollToThird);
scrollTo.push(scrollToFourth);
scrollTo.push(scrollToFifth);

function scrollToBottom(number) {
    if (getMenuIsOpen()) {
        slideMenu();
    }

    const item = scrollTo[number];

    if (item instanceof HTMLElement) {
        const duration = 900 + (500 * number); // Adjust the duration as needed
        const timingFunction = 'cubic-bezier(0.42, 0, 0.58, 1)'; // Ease timing function
        const offset = -(window.innerHeight/25) ; // Scroll 4vh above the target

        const startTime = performance.now();
        const startPosition = window.scrollY;
        const targetPosition = item.getBoundingClientRect().top + window.scrollY + offset;

        function scrollAnimation(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeInOutCubic(progress); // Apply the ease timing function
            const newPosition = startPosition + (targetPosition - startPosition) * easedProgress;
            window.scroll(0, newPosition);

            if (progress < 1) {
                requestAnimationFrame(scrollAnimation);
            }
        }

        item.style.scrollBehavior = 'auto'; // Remove smooth scrolling style

        requestAnimationFrame(scrollAnimation);
    }
}

function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
}

function bodyScrollToTop(){
    document.body.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
}