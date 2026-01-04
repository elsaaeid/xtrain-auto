<?php

/**
 * Shortcode: WooCommerce Products Filter Grid (All-in-One)
 * Usage: [products_filter_grid_full]
 */
function products_filter_grid_full_shortcode() {
    if (!class_exists('WooCommerce')) return '';

    // Get categories
    $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
    // Get color attribute (adjust slug if needed)
    $color_terms = get_terms(['taxonomy' => 'pa_color', 'hide_empty' => false]);
    ob_start();
    ?>
        <style>
        /* --- Mobile Sidebar (Accordion/Block Style) --- */
        @media (max-width: 991px) {
                .products-filter-sidebar {
                    display: none !important;
                    position: fixed !important;
                    top: 0px;
                    left: 0;
                    right: 0;
                    width: 50% !important;
                    height: 100% !important;
                    background: var(--color-white);
                    z-index: 100;
                    box-shadow: var(--shadow-lg) !important;
                    padding: 24px !important;
                    margin-bottom: 0;
                    border: 1px solid var(--color-border-hover);
                    overflow: visible !important;
                    transform: none !important;
                }

            .products-filter-sidebar.active {
                display: block !important;
                animation: fadeIn 0.3s ease-in-out;
                display: block;
            }

            /* Show Close Button on Mobile */
            .close-sidebar-btn {
                display: block !important;
                z-index: 101;
            }
            
            /* Hide Backdrop on Mobile */
            .filter-backdrop, .filter-backdrop.active {
                display: none !important;
            }

            .toggle-filters-btn {
                display: inline-flex;
                width: 70%;
                justify-content: center;
                margin-bottom: 10px;
            }
        
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        }

        /* --- Desktop Sidebar (Static Block) --- */
        @media (min-width: 992px) {
            .products-filter-sidebar {
                position: static !important;
                transform: none !important;
                height: auto !important;
                box-shadow: none !important;
                background: var(--color-transparent) !important;
                display: block !important;
                z-index: 1;
                overflow: visible !important;
            }

            /* Hide Mobile Elements on Desktop */
            .toggle-filters-btn,
            .close-sidebar-btn,
            .filter-backdrop {
                display: none !important;
            }
        }

        /* Shared Styles */
        .close-sidebar-btn {
            position: absolute;
            top: 20px;
            left: 20px; 
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--color-text-light);
            padding: 5px;
            line-height: 1;
        }
        .close-sidebar-btn:hover {
            color: var(--color-danger);
        }
        html[dir="ltr"] .close-sidebar-btn {
            left: auto;
            right: 20px;
        }

        /* Backdrop */
        .filter-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color-shadow);
            z-index: 99998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .filter-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Toggle Button Style */
        .toggle-filters-btn {
            align-items: center;
            gap: 8px;
            background: var(--color-white);
            border: 1px solid var(--color-border);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: var(--color-btn-text);
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        .toggle-filters-btn:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }
        .toggle-filters-btn svg {
            width: 20px;
            height: 20px;
        }
        </style>
        <script>
        function toggleFilterSidebar() {
            var sidebar = document.querySelector('.products-filter-sidebar');
            var backdrop = document.querySelector('.filter-backdrop');
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
        }
        function closeFilterSidebar() {
            document.querySelector('.products-filter-sidebar').classList.remove('active');
            document.querySelector('.filter-backdrop').classList.remove('active');
        }
        </script>
        
        <div class="filter-backdrop" onclick="closeFilterSidebar()"></div>

        <div class="container products-filter-grid-wrapper">
            <!-- Filter Toggle Button (Mobile Only) -->
            <button class="toggle-filters-btn" type="button" onclick="toggleFilterSidebar()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>فلتر البحث</span>
            </button>
            
        <div class="row">
                <!-- Sidebar Column: Mobile (Fixed), Desktop (3/12) -->
                <aside class="col-lg-3 products-filter-sidebar mb-4">
                    <button type="button" class="close-sidebar-btn" onclick="closeFilterSidebar()">×</button>
            <form id="products-filter-form">
                <div class="filter-title">فلتر البحث</div>
                
                <!-- Categories -->
                <div class="filter-section">
                    <label class="section-label">القسم</label>
                    <div class="filter-options-list" style="max-height: 200px; overflow-y: auto; overflow-x: hidden;">
                        <?php foreach ($categories as $cat): 
                            $cat_name = $cat->name;
                            // Shorten long category names
                            if (mb_strlen($cat_name) > 25) {
                                $cat_display = mb_substr($cat_name, 0, 25) . '...';
                            } else {
                                $cat_display = $cat_name;
                            }
                        ?>
                            <label class="filter-checkbox-item" title="<?php echo esc_attr($cat_name); ?>">
                                <span class="checkbox-text"><?php echo esc_html($cat_display); ?></span>
                                <input type="checkbox" name="category[]" value="<?php echo esc_attr($cat->slug); ?>">
                                <span class="custom-checkmark"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr class="filter-divider">

                <!-- Price -->
                <div class="filter-section">
                    <label class="section-label">السعر</label>
                    
                    <!-- Visual Slider Track -->
                    <div class="price-slider-container">
                        <div class="price-slider-track"></div>
                        <div class="price-slider-range"></div>
                        <input type="range" class="price-range-input min" min="0" max="1000" value="0" step="10">
                        <input type="range" class="price-range-input max" min="0" max="1000" value="860" step="10">
                    </div>

                    <!-- Input Boxes -->
                    <div class="price-inputs">
                        <input type="number" name="price_min" id="price-min" value="0" min="0">
                        <span class="price-dash">-</span>
                        <input type="number" name="price_max" id="price-max" value="440" min="0">
                    </div>

                    <div class="price-action-row" style="justify-content: space-between; align-items: center;">
                        <button type="button" id="reset-filters-btn" style="background: none; border: none; color: #ef4444; font-size: 14px; cursor: pointer; text-decoration: underline; padding: 0;">إعادة تعيين</button>
                        <div style="display: flex; align-items: center; gap: 10px;">
                             <span class="price-label-text" style="margin:0;">السعر: <span id="price-display-min">0</span> — <span id="price-display-max">860</span></span>
                             <button class="filter-btn-small" type="submit">تصفية</button>
                        </div>
                    </div>
                </div>

                <hr class="filter-divider">

                <!-- Color -->
                <div class="filter-section">
                    <label class="section-label">لون المنتج</label>
                    <div class="filter-options-list color-list">
                        <?php
                        if (!empty($color_terms) && !is_wp_error($color_terms)) {
                            foreach ($color_terms as $term) {
                                $color = $term->slug;
                                // Simple mapping or use slug as color if valid CSS
                                $style_color = (preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color) || preg_match('/^[a-z]+$/i', $color)) ? $color : '#ccc';
                                ?>
                                <label class="filter-item-color">
                                    <span class="color-count">(<?php echo $term->count; ?>)</span>
                                    <span class="color-name"><?php echo esc_html($term->name); ?></span>
                                    <span class="color-circle" style="background-color: <?php echo esc_attr($style_color); ?>;"></span>
                                    <input type="checkbox" name="color[]" value="<?php echo esc_attr($term->slug); ?>" style="display:none;">
                                </label>
                                <?php
                            }
                        } else {
                            echo '<div style="color:#999;font-size:13px;">لا توجد ألوان</div>';
                        }
                        ?>
                    </div>
                </div>

                <hr class="filter-divider">

                <!-- Status -->
                <div class="filter-section">
                    <label class="section-label">حالة المنتج</label>
                    <div class="filter-options-list">
                        <label class="filter-checkbox-item">
                            <span class="checkbox-text">متاح</span>
                            <input type="checkbox" name="stock[]" value="instock">
                            <span class="custom-checkmark"></span>
                        </label>
                        <label class="filter-checkbox-item">
                            <span class="checkbox-text">تخفيض</span>
                            <input type="checkbox" name="stock[]" value="onsale">
                            <span class="custom-checkmark"></span>
                        </label>
                    </div>
                </div>

            </form>
        </aside>
        <div class="col-12 col-lg-9">
            <div class="full-products-grid row" id="products-filter-results">
                <!-- AJAX products will be loaded here -->
            </div>
        </div>
        </div><!-- .row -->
    </div><!-- .container -->
    <?php
    return ob_get_clean();
}
add_shortcode('products_filter_grid_full', 'products_filter_grid_full_shortcode');
