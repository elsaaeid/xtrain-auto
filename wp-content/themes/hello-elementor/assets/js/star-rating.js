/**
 * Star Rating Handler for WooCommerce Product Reviews
 * Updates rating value when stars are clicked
 * All stars up to the clicked star become active
 */

(function() {
    'use strict';

    // Initialize star rating on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeStarRating();
    });

    function initializeStarRating() {
        // Find all star rating containers
        const ratingContainers = document.querySelectorAll('.comment-form-rating');

        ratingContainers.forEach(container => {
            const radioInputs = container.querySelectorAll('input[type="radio"]');
            const labels = container.querySelectorAll('label');

            if (radioInputs.length === 0 || labels.length === 0) {
                return; // No radio inputs or labels found
            }

            // Add click and hover handlers to each label
            labels.forEach((label, index) => {
                const radio = radioInputs[index];

                if (!radio) return;

                // Click handler - set rating and highlight all stars up to this one
                label.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const ratingValue = radio.value || (index + 1);
                    
                    // Update the radio input (do not bubble change event globally)
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: false }));
                    
                    // Highlight all stars up to and including the clicked star
                    highlightStarsUpTo(container, index);
                    
                    // Update display (scoped class name)
                    updateRatingDisplay(container, ratingValue);

                    // Fire a namespaced custom event (bubbles) so global listeners can react
                    container.dispatchEvent(new CustomEvent('hello:ratingChanged', {
                        detail: { rating: ratingValue },
                        bubbles: true
                    }));
                });

                // Hover handler - preview all stars up to hovered star
                label.addEventListener('mouseenter', function() {
                    previewStarsUpTo(container, index);
                });

                label.addEventListener('mouseleave', function() {
                    // Restore to checked state
                    const checkedRadio = container.querySelector('input[type="radio"]:checked');
                    if (checkedRadio) {
                        const checkedIndex = Array.from(radioInputs).indexOf(checkedRadio);
                        highlightStarsUpTo(container, checkedIndex);
                    } else {
                        resetStarDisplay(container);
                    }
                });
            });

            // Set initial state if a radio is already checked
            const checkedRadio = container.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                const checkedIndex = Array.from(radioInputs).indexOf(checkedRadio);
                highlightStarsUpTo(container, checkedIndex);
                updateRatingDisplay(container, checkedRadio.value || (checkedIndex + 1));
            }
        });
    }

    /**
     * Highlight all stars up to and including the given index
     */
    function highlightStarsUpTo(container, targetIndex) {
        const labels = container.querySelectorAll('label');

        labels.forEach((label, index) => {
            if (index <= targetIndex) {
                label.style.color = 'var(--color-accent)';
            } else {
                label.style.color = 'var(--color-light-grey)';
            }
        });
    }

    /**
     * Preview stars on hover - highlight all up to hovered star
     */
    function previewStarsUpTo(container, targetIndex) {
        const labels = container.querySelectorAll('label');

        labels.forEach((label, index) => {
            if (index <= targetIndex) {
                label.style.color = 'var(--color-accent)';
            } else {
                label.style.color = 'var(--color-light-grey)';
            }
        });
    }

    /**
     * Reset star display to empty
     */
    function resetStarDisplay(container) {
        const labels = container.querySelectorAll('label');

        labels.forEach(label => {
            label.style.color = 'var(--color-light-grey)';
        });

        // Clear any rating display (scoped class)
        const ratingDisplay = container.querySelector('.comment-rating-display');
        if (ratingDisplay) {
            ratingDisplay.textContent = '';
        }
    }

    /**
     * Update rating display text/number
     */
    function updateRatingDisplay(container, rating) {
        let ratingDisplay = container.querySelector('.comment-rating-display');

        if (!ratingDisplay) {
            ratingDisplay = document.createElement('span');
            ratingDisplay.className = 'comment-rating-display';
            ratingDisplay.style.marginLeft = '10px';
            ratingDisplay.style.fontSize = '14px';
            ratingDisplay.style.color = 'var(--color-accent)';
            ratingDisplay.style.fontWeight = '600';
            container.appendChild(ratingDisplay);
        }

        ratingDisplay.textContent = rating + '/5';
    }

    // Export for external use
    window.starRatingHandler = {
        initialize: initializeStarRating
    };

})();

// Global listener: update product rating preview when a comment-form rating changes
document.addEventListener('hello:ratingChanged', function(e) {
    try {
        var rating = parseFloat(e.detail && e.detail.rating) || 0;
        var stars = document.querySelectorAll('.product-grid-rating .stars .star');
        stars.forEach(function(s, idx) {
            if (idx + 1 <= rating) {
                s.classList.add('filled');
            } else {
                s.classList.remove('filled');
            }
        });

        var valueEl = document.querySelector('.product-grid-rating .rating-value');
        if (valueEl) {
            valueEl.textContent = rating.toFixed(2);
        }
    } catch (err) {
        // fail silently
        console.error('hello:ratingChanged handler error', err);
    }
}, false);
