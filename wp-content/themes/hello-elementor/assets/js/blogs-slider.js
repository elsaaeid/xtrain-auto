/**
 * Blogs Slider JavaScript
 * Handles slider navigation for the blogs section
 */

document.addEventListener('DOMContentLoaded', function() {
    initBlogsSlider();
});

function initBlogsSlider() {
    const wrapper = document.querySelector('.blogs-slider-wrapper');
    if (!wrapper) return;

    const track = wrapper.querySelector('.blogs-slider-track');
    const slides = wrapper.querySelectorAll('.blog-slide');
    const prevBtn = wrapper.querySelector('.slider-prev');
    const nextBtn = wrapper.querySelector('.slider-next');
    
    if (!track || slides.length === 0) return;

    let currentIndex = 0;
    
    // Calculate slides per view based on viewport
    function getSlidesPerView() {
        const viewportWidth = window.innerWidth;
        if (viewportWidth <= 480) return 1;
        if (viewportWidth <= 768) return 1.2;
        if (viewportWidth <= 992) return 2;
        if (viewportWidth <= 1200) return 3;
        return 4;
    }

    // Calculate slide width including gap
    function getSlideWidth() {
        const slide = slides[0];
        const slideStyle = getComputedStyle(slide);
        const gap = parseInt(getComputedStyle(track).gap) || 24;
        return slide.offsetWidth + gap;
    }

    // Get maximum scroll index
    function getMaxIndex() {
        const slidesPerView = getSlidesPerView();
        return Math.max(0, slides.length - Math.floor(slidesPerView));
    }

    // Update slider position
    function updateSlider() {
        const slideWidth = getSlideWidth();
        const offset = currentIndex * slideWidth;
        
        // RTL: positive transform = scroll left (towards earlier slides in DOM)
        track.style.transform = `translateX(${offset}px)`;
        
        // Update button states
        if (prevBtn) {
            prevBtn.disabled = currentIndex >= getMaxIndex();
        }
        if (nextBtn) {
            nextBtn.disabled = currentIndex <= 0;
        }
    }

    // Navigation handlers (RTL: directions are swapped)
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentIndex < getMaxIndex()) {
                currentIndex++;
                updateSlider();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Ensure current index is valid after resize
            const maxIndex = getMaxIndex();
            if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }
            updateSlider();
        }, 100);
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    const swipeThreshold = 50;

    track.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchmove', function(e) {
        touchEndX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchend', function() {
        const swipeDistance = touchStartX - touchEndX;
        
        // RTL: swipe left = go to next (decrease index), swipe right = go to prev (increase index)
        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0 && currentIndex < getMaxIndex()) {
                // Swipe left in RTL = show next cards
                currentIndex++;
                updateSlider();
            } else if (swipeDistance < 0 && currentIndex > 0) {
                // Swipe right in RTL = show previous cards
                currentIndex--;
                updateSlider();
            }
        }
    });

    // Initial setup
    updateSlider();
}
