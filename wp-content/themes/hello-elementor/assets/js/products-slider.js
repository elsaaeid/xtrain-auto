/**
 * WooCommerce Products Slider - JavaScript
 * Handles slider navigation, wishlist, and add to cart functionality
 */

document.addEventListener('DOMContentLoaded', function () {

    const sliderWrapper = document.querySelector('.products-slider-wrapper');
    if (!sliderWrapper) return;

    const slider = sliderWrapper.querySelector('.products-slider');
    const track = sliderWrapper.querySelector('.products-slider-track');
    const slides = sliderWrapper.querySelectorAll('.product-slide');
    const prevBtn = sliderWrapper.querySelector('.products-slider-prev');
    const nextBtn = sliderWrapper.querySelector('.products-slider-next');

    if (!slider || !track || slides.length === 0) return;

    let currentIndex = 0;
    let slidesToShow = 5;
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
        } else if (width <= 1400) {
            slidesToShow = 4;
        } else {
            slidesToShow = 5;
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
        updatePaginationDots();
    }

    // Update pagination dots
    function updatePaginationDots() {
        const dots = sliderWrapper.querySelectorAll('.pagination-dot');
        const currentPage = Math.floor(currentIndex / slidesToShow);

        dots.forEach((dot, index) => {
            if (index === currentPage) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Pagination dots click handler
    const paginationDots = sliderWrapper.querySelectorAll('.pagination-dot');
    paginationDots.forEach((dot, index) => {
        dot.addEventListener('click', function () {
            currentIndex = index * slidesToShow;
            if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }
            updateSlider();

            if (typeof gsap !== 'undefined') {
                gsap.fromTo(track,
                    { opacity: 0.8 },
                    { opacity: 1, duration: 0.3 }
                );
            }
        });
    });

    // Navigate to previous slide
    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();

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

    // Wishlist functionality is handled by `global-product-actions.js`.
    // Removed local handlers to avoid duplicate listeners; rely on delegated global handlers.

    // Add-to-cart UI and interactions are handled by `global-product-actions.js` and the shared handlers.
    // Removed slider-local add-to-cart logic to avoid duplication and to use the canonical project handlers.

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
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.from('.product-slide', {
            scrollTrigger: {
                trigger: '.products-slider-wrapper',
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

// WooCommerce AJAX Add to Cart parameters
if (typeof wc_add_to_cart_params === 'undefined') {
    var wc_add_to_cart_params = {
        ajax_url: '/wp-admin/admin-ajax.php',
        wc_ajax_url: '/?wc-ajax=%%endpoint%%'
    };
}
