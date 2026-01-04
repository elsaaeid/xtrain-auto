// JS for products_filter_grid_full AJAX filter
jQuery(document).ready(function ($) {

    // --- Dual Range Slider Logic ---
    const rangeMin = $('.price-range-input.min');
    const rangeMax = $('.price-range-input.max');
    const inputMin = $('#price-min');
    const inputMax = $('#price-max');
    const displayMin = $('#price-display-min');
    const displayMax = $('#price-display-max');
    const rangeTrack = $('.price-slider-range');
    const sliderMaxValue = 1000; // Match max attribute in HTML
    const minGap = 50; // Minimum gap between handles

    function updateSlider() {
        let minVal = parseInt(rangeMin.val());
        let maxVal = parseInt(rangeMax.val());

        // Prevent crossing
        if (maxVal - minVal < minGap) {
            if ($(this).hasClass('min')) {
                rangeMin.val(maxVal - minGap);
                minVal = maxVal - minGap;
            } else {
                rangeMax.val(minVal + minGap);
                maxVal = minVal + minGap;
            }
        }

        // Update visuals
        const percentMin = (minVal / sliderMaxValue) * 100;
        const percentMax = (maxVal / sliderMaxValue) * 100;

        rangeTrack.css({
            'right': percentMin + '%',
            'width': (percentMax - percentMin) + '%'
        });

        // Update inputs and text
        inputMin.val(minVal);
        inputMax.val(maxVal);
        displayMin.text(minVal);
        displayMax.text(maxVal);
    }

    function syncInputs() {
        let minVal = parseInt(inputMin.val());
        let maxVal = parseInt(inputMax.val());

        // Validate
        if (minVal < 0) minVal = 0;
        if (maxVal > sliderMaxValue) maxVal = sliderMaxValue;
        if (maxVal - minVal < minGap) {
            // Basic validation, prefer not to force change heavily on typing
        }

        rangeMin.val(minVal);
        rangeMax.val(maxVal);
        updateSlider(); // Refresh visuals
    }

    // Event Listeners
    rangeMin.on('input', updateSlider);
    rangeMax.on('input', updateSlider);

    // Sync on change of number inputs
    inputMin.on('change', syncInputs);
    inputMax.on('change', syncInputs);

    // Initial call
    updateSlider();


    // --- AJAX Fetch Logic ---
    function fetchProducts(page = 1) {
        var data = $('#products-filter-form').serialize();
        // Append paged arg
        data += '&paged=' + page;

        // Append search term from URL if exists (s or q_search)
        const urlParams = new URLSearchParams(window.location.search);
        const search = urlParams.get('s') || urlParams.get('q_search');
        if (search) {
            // pass as 's' to the backend as our PHP logic maps q_search to s if needed, 
            // but for consistency let's pass it as whatever the backend expects. 
            // Actually our backend checks $_GET['s'] OR $_GET['q_search'], but serialize adds form data.
            // Let's pass it as 'q_search' to be safe if 's' is missing.
            data += '&q_search=' + encodeURIComponent(search);
        }

        // Generate Skeleton
        let skeletonHTML = '';
        for (let i = 0; i < 6; i++) {
            skeletonHTML += '<div class="col-12 col-md-6 col-lg-4 mb-4"><div class="skeleton-card"><div class="skeleton-box skeleton-image"></div><div class="skeleton-box skeleton-title"></div><div class="skeleton-box skeleton-price"></div><div class="skeleton-box skeleton-btn"></div></div></div>';
        }
        $('#products-filter-results').html(skeletonHTML);

        // Append cart state
        if (products_filter_grid_full.initial_cart) {
            data += '&current_cart_state=' + encodeURIComponent(JSON.stringify(products_filter_grid_full.initial_cart));
        }

        $.ajax({
            url: products_filter_grid_full.ajax_url,
            type: 'POST',
            data: 'action=products_filter_grid_full&' + data,
            dataType: 'html',
            cache: false,
            success: function (response) {
                $('#products-filter-results').html(response);
                syncCartButtons();
                if (typeof window.hello_sync_wishlist_button_states === 'function') {
                    window.hello_sync_wishlist_button_states();
                }
            }
        });
    }

    // Function to sync buttons with known cart state (Client-Side Hydration)
    function syncCartButtons() {
        if (!products_filter_grid_full.initial_cart) return;

        // Loop through all product cards in the grid
        $('.add-to-cart-wrapper').each(function () {
            var $wrapper = $(this);
            var product_id = $wrapper.data('product_id');
            var qtyInCart = products_filter_grid_full.initial_cart[product_id]; // Check localized data
            if (qtyInCart && typeof qtyInCart === 'object' && qtyInCart.qty) {
                qtyInCart = qtyInCart.qty; // Handle {qty, key} structure
            }

            if (qtyInCart && qtyInCart > 0) {
                // Item IS in cart, force UI update
                var $btn = $wrapper.find('.add-to-cart-btn');
                var $qtyCtrl = $wrapper.find('.grid-qty-control');
                var $qtyVal = $wrapper.find('.grid-qty-val');
                var $minusBtn = $wrapper.find('.grid-minus');

                // Hide Add Button
                $btn.hide();
                // Show Qty Controls
                $qtyCtrl.css('display', 'flex');
                $qtyVal.val(qtyInCart);

                // Set Trash vs Minus
                if (qtyInCart == 1) {
                    $minusBtn.addClass('trash-mode').css({ 'background': '#fff0f0', 'color': '#ef4444' });
                    $minusBtn.html('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>');
                } else {
                    $minusBtn.removeClass('trash-mode').css({ 'background': '#f9f9f9', 'color': '' });
                    $minusBtn.html('-');
                }
            }
        });
    }

    // Fetch initial
    fetchProducts();

    // Debounce for slider to prevent too many requests
    let timeout;
    $('#products-filter-form').on('input', '.price-range-input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(fetchProducts, 500);
    });

    // Immediate fetch for other inputs
    $('#products-filter-form').on('change', 'input[type="checkbox"], input[type="number"]', function () {
        fetchProducts();
    });

    // Form submit
    $('#products-filter-form').on('submit', function (e) {
        e.preventDefault();
        fetchProducts();
    });

    // Reset Filters
    $('#reset-filters-btn').on('click', function (e) {
        e.preventDefault();

        // Reset Form
        $('#products-filter-form')[0].reset();

        // Reset Visual Sliders
        inputMin.val(0);
        inputMax.val(sliderMaxValue);
        rangeMin.val(0);
        rangeMax.val(sliderMaxValue);
        updateSlider();

        // Clear any search params from URL (e.g. q_search) without reloading page
        if (window.history.pushState) {
            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({ path: newUrl }, '', newUrl);
        }

        // Fetch products with clean state
        fetchProducts(1);
    });

    // Pagination clicks (if pagination is added dynamically)
    $(document).on('click', '.pagination-btn', function () {
        let page = $(this).data('page');
        fetchProducts(page);
        // Scroll to top of results
        $('html, body').animate({
            scrollTop: $('#products-filter-results').offset().top - 100
        }, 300);
    });

    // Toast Helper
    function showToast(message) {
        let $toast = $('.custom-toast');
        if ($toast.length === 0) {
            $toast = $('<div class="custom-toast"></div>').appendTo('body');
        }
        $toast.html(message).addClass('show');
        setTimeout(() => {
            $toast.removeClass('show');
        }, 3000);
    }

    // --- Cart Actions are now handled globally by global-product-actions.js ---
    // This integration ensures consistent behavior across all shortcodes.


});
