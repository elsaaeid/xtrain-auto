/**
 * Categories Slider - JavaScript
 * Handles slider navigation and touch/swipe functionality
 */

document.addEventListener('DOMContentLoaded', function () {

    const sliderWrapper = document.querySelector('.categories-slider-wrapper');
    if (!sliderWrapper) return;

    const slider = sliderWrapper.querySelector('.categories-slider');
    const track = sliderWrapper.querySelector('.categories-slider-track');
    const slides = sliderWrapper.querySelectorAll('.category-slide');
    const prevBtn = sliderWrapper.querySelector('.slider-prev');
    const nextBtn = sliderWrapper.querySelector('.slider-next');

    if (!slider || !track || slides.length === 0) return;

    let currentIndex = 0;
    let slidesToShow = 4;
    let slideWidth = 0;
    let maxIndex = 0;
    let isRTL = document.dir === 'rtl' || document.documentElement.dir === 'rtl';

    // Calculate slides to show based on screen width
    function calculateSlidesToShow() {
        const width = window.innerWidth;
        if (width <= 768) {
            slidesToShow = 1;
        } else if (width <= 992) {
            slidesToShow = 2;
        } else if (width <= 1200) {
            slidesToShow = 3;
        } else {
            slidesToShow = 4;
        }

        maxIndex = Math.max(0, slides.length - slidesToShow);
        updateSlideWidth();
        updateSlider();
    }

    // Update slide width
    function updateSlideWidth() {
        const containerWidth = slider.offsetWidth;
        const gap = 20;
        slideWidth = (containerWidth / slidesToShow);

        slides.forEach(slide => {
            slide.style.flex = `0 0 ${slideWidth - gap}px`;
        });
    }

    // Update slider position
    function updateSlider() {
        const gap = 20;
        const offset = currentIndex * (slideWidth);

        if (isRTL) {
            track.style.transform = `translateX(${offset}px)`;
        } else {
            track.style.transform = `translateX(-${offset}px)`;
        }

        updateButtons();
    }

    // Update button states
    function updateButtons() {
        if (prevBtn && nextBtn) {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= maxIndex;
        }
    }

    // Navigate to previous slide
    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();

            // GSAP animation (if available)
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(track,
                    { opacity: 0.8 },
                    { opacity: 1, duration: 0.3 }
                );
            }
        }
    }

    // Navigate to next slide
    function nextSlide() {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateSlider();

            // GSAP animation (if available)
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(track,
                    { opacity: 0.8 },
                    { opacity: 1, duration: 0.3 }
                );
            }
        }
    }

    // Event listeners for navigation buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
    }

    // Touch/Swipe functionality
    let touchStartX = 0;
    let touchEndX = 0;
    let isDragging = false;

    track.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
        isDragging = true;
    }, { passive: true });

    track.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        touchEndX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchend', function () {
        if (!isDragging) return;
        isDragging = false;

        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (isRTL) {
                // RTL: swipe right = next, swipe left = prev
                if (diff > 0) {
                    prevSlide();
                } else {
                    nextSlide();
                }
            } else {
                // LTR: swipe left = next, swipe right = prev
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
    });

    // Mouse drag functionality (desktop)
    let mouseStartX = 0;
    let mouseEndX = 0;
    let isMouseDragging = false;

    track.addEventListener('mousedown', function (e) {
        mouseStartX = e.clientX;
        isMouseDragging = true;
        track.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function (e) {
        if (!isMouseDragging) return;
        mouseEndX = e.clientX;
    });

    document.addEventListener('mouseup', function () {
        if (!isMouseDragging) return;
        isMouseDragging = false;
        track.style.cursor = 'grab';

        const swipeThreshold = 50;
        const diff = mouseStartX - mouseEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (isRTL) {
                if (diff > 0) {
                    prevSlide();
                } else {
                    nextSlide();
                }
            } else {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (!sliderWrapper.contains(document.activeElement)) return;

        if (e.key === 'ArrowLeft') {
            if (isRTL) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else if (e.key === 'ArrowRight') {
            if (isRTL) {
                prevSlide();
            } else {
                nextSlide();
            }
        }
    });

    // Auto-play (optional - uncomment to enable)
    /*
    let autoplayInterval;
    function startAutoplay() {
        autoplayInterval = setInterval(() => {
            if (currentIndex >= maxIndex) {
                currentIndex = 0;
            } else {
                currentIndex++;
            }
            updateSlider();
        }, 3000);
    }

    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }

    startAutoplay();

    sliderWrapper.addEventListener('mouseenter', stopAutoplay);
    sliderWrapper.addEventListener('mouseleave', startAutoplay);
    */

    // Initialize
    calculateSlidesToShow();

    // Recalculate on window resize
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            calculateSlidesToShow();
        }, 250);
    });

    // GSAP entrance animation (if available)
    if (typeof gsap !== 'undefined') {
        gsap.from('.category-slide', {
            scrollTrigger: {
                trigger: '.categories-slider-wrapper',
                start: 'top 80%',
            },
            opacity: 0,
            y: 30,
            duration: 0.6,
            stagger: 0.1,
            ease: 'power2.out'
        });
    }
});
