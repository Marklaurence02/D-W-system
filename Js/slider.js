const dots = document.querySelectorAll(".dot");
const slides = document.querySelectorAll(".slide-content-box");
const slideTrack = document.querySelector(".slide-track");
const prevButton = document.getElementById("prev-btn");
const nextButton = document.getElementById("next-btn");
const tableNav = document.querySelector(".table_nav");
let currentIndex = 1;

function showSlide(index) {
    currentIndex = (index + slides.length) % slides.length;

    slideTrack.style.transform = `translateX(-${(currentIndex - 1) * 33.33}%)`;

    slides.forEach((slide, i) => {
        slide.classList.remove("active-slide");
        if (i === currentIndex) {
            slide.classList.add("active-slide");
        }
    });

    dots.forEach((dot, i) => {
        dot.classList.remove("active");
        if (i === currentIndex - 1) {
            dot.classList.add("active");
        }
    });

    updateArrowVisibility();

    if (currentIndex === 2) {
        tableNav.style.display = 'flex';
    } else {
        tableNav.style.display = 'none';
    }
}

function updateArrowVisibility() {
    prevButton.style.display = currentIndex === 1 ? 'none' : 'block';
    nextButton.style.display = currentIndex === 3 ? 'none' : 'block';
}

showSlide(currentIndex);

dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
        showSlide(index + 1);
    });
});

nextButton.addEventListener("click", () => {
    showSlide(currentIndex + 1);
});

prevButton.addEventListener("click", () => {
    showSlide(currentIndex - 1);
});
