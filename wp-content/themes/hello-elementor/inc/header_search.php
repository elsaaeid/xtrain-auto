<?php
// ===============================
// EXACT WooCommerce Search Component
// ===============================

function header_search_shortcode() {
    ob_start(); ?>
	<div class="exact-search-wrapper">
        <button type="button" class="exact-search-toggle" aria-label="Toggle Search">
            <svg class="toggle-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>

		<div class="exact-search-container">
            <div class="exact-search-field">
                <input
                    type="text"
                    id="exact-search-input"
                    placeholder="ابحث عن منتج أو قطع غيار"
                    autocomplete="off"
                >

                <svg class="exact-search-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

			<div id="exact-search-results"></div>

		</div>
	</div>
    <?php
    return ob_get_clean();
}
add_shortcode('header_search', 'header_search_shortcode');
