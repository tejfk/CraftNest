// Toggle search input visibility
function toggleSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.style.display = (searchInput.style.display === 'none' || searchInput.style.display === '') ? 'block' : 'none';
    }
}

// Toggle navigation on small screens
document.querySelector('.hamburger')?.addEventListener('click', () => {
    document.querySelector('.nav-links')?.classList.toggle('active');
});

// Slideshow logic
let slideIndex = 0;
let slideInterval;

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;

    if (totalSlides === 0) return;

    slideIndex = (index + totalSlides) % totalSlides;

    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === slideIndex);
    });

    const slidesWrapper = document.querySelector('.slides');
    if (slidesWrapper) {
        slidesWrapper.style.transform = `translateX(-${slideIndex * 100}%)`;
    }
}

function moveSlides() {
    showSlide(slideIndex + 1);
}

function moveSlide(direction) {
    clearInterval(slideInterval);
    showSlide(slideIndex + direction);
    startSlideshow();
}

function startSlideshow() {
    slideInterval = setInterval(moveSlides, 5000);
}

// Optional: Enable keyboard navigation only when focused inside slideshow
const slideshowContainer = document.querySelector('.slideshow-container');
if (slideshowContainer) {
    slideshowContainer.setAttribute('tabindex', '0'); // Make it focusable
    slideshowContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
    slideshowContainer.addEventListener('mouseleave', startSlideshow);
    slideshowContainer.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') moveSlide(-1);
        if (e.key === 'ArrowRight') moveSlide(1);
    });

    showSlide(slideIndex);
    startSlideshow();
}
