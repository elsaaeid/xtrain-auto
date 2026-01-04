jQuery(document).ready(function ($) {

    // --- Toast Helper ---
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

    // --- Helper to get WC AJAX URL ---
    function getWcAjaxUrl(endpoint) {
        if (typeof global_cart_params !== 'undefined' && global_cart_params.wc_ajax_url) {
            return global_cart_params.wc_ajax_url.replace('%%endpoint%%', endpoint);
        }
        // Fallback for when global_cart_params is missing
        return window.location.origin + '/?wc-ajax=' + endpoint;
    }

    // --- Intercept Add-to-Cart clicks for grid/related items ---
    // Ensures anchors with class .add-to-cart-btn perform AJAX add_to_cart
    $(document).on('click', 'a.add-to-cart-btn', function (e) {
        var $a = $(this);
        // allow non-JS fallback if ctrl/cmd/shift clicked
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        e.stopPropagation();

        var $wrapper = $a.closest('.add-to-cart-wrapper');
        var product_id = $a.data('product_id') || $a.attr('data-product_id');
        var qty = parseInt($a.data('quantity') || $a.attr('data-quantity') || 1, 10) || 1;

        // prefer visible qty input value when present
        if ($wrapper && $wrapper.length) {
            var $val = $wrapper.find('.grid-qty-val');
            if ($val.length) {
                var v = parseInt($val.val(), 10);
                if (!isNaN(v) && v > 0) qty = v;
            }
        }

        $a.addClass('loading');

        $.post(getWcAjaxUrl('add_to_cart'), { product_id: product_id, quantity: qty })
            .done(function (res) {
                if (res && res.fragments) {
                    $(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash, $a]);
                } else {
                    $(document.body).trigger('wc_fragment_refresh');
                }
            })
            .fail(function () {
                // fallback: navigate to href
                window.location = $a.attr('href');
            })
            .always(function () {
                $a.removeClass('loading');
            });
    });

    // --- Handle Quantity Plus (Add 1) ---
    $(document).on('click', '.grid-plus', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrapper = $(this).closest('.add-to-cart-wrapper');
        var product_id = $wrapper.data('product_id');
        var $val = $wrapper.find('.grid-qty-val');
        var current = parseInt($val.val()) || 0;

        $val.css('opacity', '0.5');

        $.ajax({
            type: 'POST',
            url: getWcAjaxUrl('add_to_cart'),
            data: { product_id: product_id, quantity: 1 },
            success: function (res) {
                $(document.body).trigger('wc_fragment_refresh');
                if (res.fragments) $(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash]);

                var newQty = current + 1;
                $val.val(newQty).css('opacity', '1');

                // If we went from 1 to 2, ensure minus button is standard
                var $minusBtn = $wrapper.find('.grid-minus');
                if (newQty > 1 && $minusBtn.hasClass('trash-mode')) {
                    $minusBtn.removeClass('trash-mode').css({ 'background': '#f9f9f9', 'color': '' });
                    $minusBtn.html('-');
                }
            },
            error: function () {
                $val.css('opacity', '1'); // Reset opacity on error
            }
        });
    });

    // --- Handle Quantity Minus (Subtract 1 or Remove) ---
    $(document).on('click', '.grid-minus', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrapper = $(this).closest('.add-to-cart-wrapper');
        var product_id = $wrapper.data('product_id');
        var $val = $wrapper.find('.grid-qty-val');
        var current = parseInt($val.val());

        // Try to find the item key from local state
        var item_key = null;
        if (typeof products_filter_grid_full !== 'undefined' && products_filter_grid_full.initial_cart && products_filter_grid_full.initial_cart[product_id]) {
            item_key = products_filter_grid_full.initial_cart[product_id].key;
        }

        if (current <= 1) {
            updateGlobalGridQty(product_id, 0, $wrapper, item_key);
        } else {
            updateGlobalGridQty(product_id, current - 1, $wrapper, item_key);
        }
    });

    function updateGlobalGridQty(product_id, new_qty, $wrapper, item_key) {
        var $val = $wrapper.find('.grid-qty-val');
        var $btn = $wrapper.find('.add-to-cart-btn');
        var $qtyCtrl = $wrapper.find('.grid-qty-control');

        $val.css('opacity', '0.5');

        var ajaxData = {
            product_id: product_id,
            qty: new_qty
        };
        if (item_key) ajaxData.cart_item_key = item_key;

        $.ajax({
            url: getWcAjaxUrl('update_item_qty'),
            type: 'POST',
            data: ajaxData,
            success: function (res) {
                if (res && res.fragments) {
                    $(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash, $btn]);
                } else {
                    $(document.body).trigger('wc_fragment_refresh');
                }

                // Update Local State if products_filter_grid_full is available
                if (typeof products_filter_grid_full !== 'undefined') {
                    if (!products_filter_grid_full.initial_cart) products_filter_grid_full.initial_cart = {};

                    if (new_qty === 0) {
                        delete products_filter_grid_full.initial_cart[product_id];
                    } else {
                        // Preserve key if it existed
                        var existingKey = (products_filter_grid_full.initial_cart[product_id] && products_filter_grid_full.initial_cart[product_id].key) ? products_filter_grid_full.initial_cart[product_id].key : '';
                        products_filter_grid_full.initial_cart[product_id] = { qty: new_qty, key: existingKey };
                    }
                }

                if (new_qty === 0) {
                    $qtyCtrl.hide();
                    $btn.css('display', 'block'); // Reset to Add button
                    $val.val('1');
                    // Reset minus button style
                    var $minusBtn = $qtyCtrl.find('.grid-minus');
                    $minusBtn.removeClass('trash-mode').css({ 'background': '#f9f9f9', 'color': '' }).html('-');

                    showToast('✔ تم حذف المنتج من السلة');

                } else {
                    $val.val(new_qty);
                    // Update Trash Icon logic
                    var $minusBtn = $qtyCtrl.find('.grid-minus');
                    if (new_qty === 1) {
                        $minusBtn.addClass('trash-mode').css({ 'background': '#fff0f0', 'color': '#ef4444' });
                        $minusBtn.html('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>');
                    } else {
                        $minusBtn.removeClass('trash-mode').css({ 'background': '#f9f9f9', 'color': '' });
                        $minusBtn.html('-');
                    }
                }
                $val.css('opacity', '1');
            },
            error: function () {
                $val.css('opacity', '1');
                // Fallback: if server returned 500 for update_item_qty (custom endpoint missing),
                // attempt to add the requested qty via standard add_to_cart endpoint when new_qty > 0,
                // or redirect to cart page when removing (new_qty === 0).
                var fallbackAjaxUrl = getWcAjaxUrl('add_to_cart');
                if (new_qty > 0) {
                    $.ajax({
                        url: fallbackAjaxUrl,
                        type: 'POST',
                        data: { product_id: product_id, quantity: new_qty },
                        success: function (res) {
                            $(document.body).trigger('wc_fragment_refresh');
                            if (res && res.fragments) $(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash]);
                            // Update UI locally
                            $val.val(new_qty);
                            $val.css('opacity', '1');
                            if (typeof showToast === 'function') showToast('✔ تمت الإضافة للسلة');
                        },
                        error: function () {
                            // As a last resort redirect to the cart page so user can manage quantities there
                            var cartUrl = (typeof global_cart_params !== 'undefined' && global_cart_params.cart_url) ? global_cart_params.cart_url : '/cart/';
                            window.location = cartUrl;
                        }
                    });
                } else {
                    var cartUrl = (typeof global_cart_params !== 'undefined' && global_cart_params.cart_url) ? global_cart_params.cart_url : '/cart/';
                    window.location = cartUrl;
                }
            }
        });
    }

    // --- Global Listener for added_to_cart (UI Swapping) ---
    $(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
        if (!$button || $button.length === 0) return;

        var $wrapper = $button.closest('.add-to-cart-wrapper');
        if ($wrapper.length > 0) {
            var $qtyCtrl = $wrapper.find('.grid-qty-control');
            var $qtyVal = $wrapper.find('.grid-qty-val');

            setTimeout(function () {
                $button.hide().removeClass('loading').css('opacity', '1');
                $wrapper.find('.added_to_cart').remove();

                var currentQty = parseInt($qtyVal.val());
                if (isNaN(currentQty) || currentQty < 1) {
                    currentQty = 1;
                    $qtyVal.val('1');
                }

                $qtyCtrl.css('display', 'flex');

                var $minusBtn = $qtyCtrl.find('.grid-minus');
                if (currentQty === 1) {
                    $minusBtn.addClass('trash-mode').css({ 'background': '#fff0f0', 'color': '#ef4444' });
                    $minusBtn.html('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>');
                } else {
                    $minusBtn.removeClass('trash-mode').css({ 'background': '#f9f9f9', 'color': '' });
                    $minusBtn.html('-');
                }
            }, 50);
        }
    });

    // --- Global Wishlist Handler ---
    $(document).on('click', '.toggle-wishlist-btn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var $btn = $(this);
        var product_id = $btn.data('product-id');
        var action = $btn.data('action');
        var $icon = $btn.find('i');

        // Optimistic UI Update
        if (action === 'add') {
            $icon.removeClass('fa-regular').addClass('fa-solid').css('color', '#ef4444');
            $btn.addClass('active');
            $btn.data('action', 'remove');
        } else {
            $icon.removeClass('fa-solid').addClass('fa-regular').css('color', '');
            $btn.removeClass('active');
            $btn.data('action', 'add');
        }

        var ajaxEndpoint = (typeof global_cart_params !== 'undefined') ? global_cart_params.ajax_url : window.location.origin + '/wp-admin/admin-ajax.php';

        $.ajax({
            type: 'POST',
            url: ajaxEndpoint,
            data: {
                action: (action === 'add') ? 'hello_custom_add_to_wishlist' : 'hello_custom_remove_from_wishlist',
                product_id: product_id      // Unified parameter name
            },
            success: function (response) {
                if (response.success) {
                    if (action === 'add') {
                        $(document.body).trigger('added_to_wishlist', [product_id, $btn]);
                        // Bridge to DOM events for non-jQuery listeners
                        try {
                            document.dispatchEvent(new CustomEvent('added_to_wishlist', { detail: { product_id: product_id } }));
                            // Open wishlist sidebar on success
                            document.dispatchEvent(new Event('hello_open_wishlist_sidebar'));
                        } catch (err) { }
                        showToast('✔ تمت الإضافة للمفضلة');
                    } else {
                        $(document.body).trigger('removed_from_wishlist', [product_id, $btn]);
                        try {
                            document.dispatchEvent(new CustomEvent('removed_from_wishlist', { detail: { product_id: product_id } }));
                        } catch (err) { }
                        showToast('✔ تمت الإزالة من المفضلة');
                    }
                } else {
                    // Revert UI if server returned success: false
                    if (action === 'add') {
                        $icon.removeClass('fa-solid').addClass('fa-regular').css('color', '');
                        $btn.removeClass('active');
                        $btn.data('action', 'add');
                    } else {
                        $icon.removeClass('fa-regular').addClass('fa-solid').css('color', '#ef4444');
                        $btn.addClass('active');
                        $btn.data('action', 'remove');
                    }
                    // Optional: showToast('❌ حدث خطأ');
                }
            },
            error: function () {
                // Revert UI on AJAX error
                if (action === 'add') {
                    $icon.removeClass('fa-solid').addClass('fa-regular').css('color', '');
                    $btn.removeClass('active');
                    $btn.data('action', 'add');
                } else {
                    $icon.removeClass('fa-regular').addClass('fa-solid').css('color', '#ef4444');
                    $btn.addClass('active');
                    $btn.data('action', 'remove');
                }
            }
        });
    });

    // (Redundant handler removed - relying on Unified Wishlist Button Handler below)

    // --- Unified Wishlist Button Handler ---
    // Now captures all wishlist buttons, overriding default YITH behavior if present
    $(document).on('click', '.product-wishlist-btn, .product-grid-wishlist, .product-wishlist, .add_to_wishlist', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        var $btn = $(this);
        var product_id = $btn.data('product-id');
        var isActive = $btn.hasClass('active');
        var action = isActive ? 'remove' : 'add';

        // Optimistic UI Update
        if (action === 'add') {
            $btn.addClass('active');
            $btn.find('svg').addClass('active');
        } else {
            $btn.removeClass('active');
            $btn.find('svg').removeClass('active');
        }

        var ajaxEndpoint = (typeof global_cart_params !== 'undefined') ? global_cart_params.ajax_url : window.location.origin + '/wp-admin/admin-ajax.php';

        $.ajax({
            type: 'POST',
            url: ajaxEndpoint,
            data: {
                action: (action === 'add') ? 'hello_custom_add_to_wishlist' : 'hello_remove_wishlist_item',
                product_id: product_id,
                nonce: (typeof hello_header_icons !== 'undefined') ? hello_header_icons.nonce : ''
            },
            success: function (response) {
                if (response.success) {
                    if (action === 'add') {
                        $(document.body).trigger('added_to_wishlist', [product_id, $btn]);
                        try {
                            document.dispatchEvent(new CustomEvent('added_to_wishlist', { detail: { product_id: product_id } }));
                            document.dispatchEvent(new Event('hello_open_wishlist_sidebar'));
                        } catch (err) { }
                        if (typeof showToast === 'function') {
                            showToast('✔ تمت الإضافة للمفضلة');
                        }
                    } else {
                        // Remove from wishlist - use same behavior as sidebar remove button
                        var $sidebarItem = $('.wishlist-sidebar-item[data-product-id="' + product_id + '"]');
                        if ($sidebarItem.length) {
                            $sidebarItem.fadeOut(300, function () {
                                $(this).remove();
                                // Check if sidebar is now empty
                                if ($('#wishlist-sidebar-items').children('.wishlist-sidebar-item').length === 0) {
                                    $('#wishlist-sidebar-items').html('<div class="wishlist-empty-msg">لا توجد منتجات في المفضلة.</div>');
                                }
                            });
                        }

                        // Trigger update of header count and sidebar refresh
                        $(document.body).trigger('removed_from_wishlist', [product_id, $btn]);

                        if (typeof showToast === 'function') {
                            showToast('✔ تمت الإزالة من المفضلة');
                        }
                    }
                } else {
                    // Revert UI if server returned success: false
                    if (action === 'add') {
                        $btn.removeClass('active');
                        $btn.find('svg').removeClass('active');
                    } else {
                        $btn.addClass('active');
                        $btn.find('svg').addClass('active');
                    }
                }
            },
            error: function () {
                // Revert UI on AJAX error
                if (action === 'add') {
                    $btn.removeClass('active');
                    $btn.find('svg').removeClass('active');
                } else {
                    $btn.addClass('active');
                    $btn.find('svg').addClass('active');
                }
            }
        });
    });

});

// Bridge YITH jQuery events to DOM and open wishlist sidebar
jQuery(document).ready(function ($) {

    // Sync wishlist button states on page load
    window.hello_sync_wishlist_button_states = function () {
        var ajaxUrl = (typeof global_cart_params !== 'undefined') ? global_cart_params.ajax_url : '/wp-admin/admin-ajax.php';

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'hello_get_wishlist_product_ids'
            },
            success: function (response) {
                if (response.success && response.data && response.data.length > 0) {
                    // Mark buttons as active for products in wishlist
                    response.data.forEach(function (productId) {
                        // SVG-based buttons
                        $('.product-wishlist-btn[data-product-id="' + productId + '"], .product-grid-wishlist[data-product-id="' + productId + '"], .product-wishlist[data-product-id="' + productId + '"]')
                            .addClass('active')
                            .find('svg').addClass('active');

                        // FontAwesome icon buttons
                        var $faBtn = $('.toggle-wishlist-btn[data-product-id="' + productId + '"]');
                        if ($faBtn.length) {
                            $faBtn.addClass('active').data('action', 'remove');
                            $faBtn.find('i').removeClass('fa-regular').addClass('fa-solid').css('color', '#ef4444');
                        }
                    });
                }
            }
        });
    }

    // Run sync on page load
    window.hello_sync_wishlist_button_states();

    $(document.body).on('added_to_wishlist', function (event, product_id, $btn) {
        try {
            document.dispatchEvent(new CustomEvent('added_to_wishlist', { detail: { product_id: product_id } }));
            document.dispatchEvent(new Event('hello_open_wishlist_sidebar'));
        } catch (err) { }
        // Ensure active visual state for button if available
        if ($btn && $btn.length) {
            $btn.addClass('active');
            $btn.find('svg').addClass('active');
        }
    });
    // Fallback for YITH-specific event name
    $(document).on('yith_wcwl_added_to_wishlist', function (event, data) {
        try {
            document.dispatchEvent(new Event('hello_open_wishlist_sidebar'));
        } catch (err) { }
    });
    $(document.body).on('removed_from_wishlist', function (event, product_id, $btn) {
        try {
            document.dispatchEvent(new CustomEvent('removed_from_wishlist', { detail: { product_id: product_id } }));
        } catch (err) { }
        if ($btn && $btn.length) {
            $btn.removeClass('active');
            $btn.find('svg').removeClass('active');
        }
    });

    // -----------------------------
    // Cart quantity controls (shared)
    // -----------------------------
    function initCartQtyControls() {
        var updateTimer;
        var $cartForm = $('.woocommerce-cart-form');
        var $updateBtn = $cartForm.find('button[name="update_cart"]');

        function submitCartDebounced() {
            clearTimeout(updateTimer);
            $('.cart-main-content').css('opacity', '0.6');
            updateTimer = setTimeout(function () {
                if ($updateBtn.length) {
                    $updateBtn.prop('disabled', false);
                    $updateBtn.trigger('click');
                } else {
                    $cartForm.trigger('submit');
                }
            }, 400);
        }

        function updateMinusIcon($wrapper) {
            var qty = parseInt($wrapper.find('.qty').val(), 10) || 0;
            var $btn = $wrapper.find('.custom-minus');
            var $trash = $btn.find('.trash-icon');
            var $minus = $btn.find('.minus-icon');
            var isTrash = qty <= 1;

            $btn.toggleClass('trash-mode', isTrash);
            $trash.toggle(isTrash);
            $minus.toggle(!isTrash);
            $btn.attr('aria-label', isTrash ? 'حذف المنتج' : 'تقليل الكمية');
        }

        $('.custom-qty-wrapper').each(function () { updateMinusIcon($(this)); });
        setTimeout(function () {
            $('.custom-qty-wrapper').each(function () { updateMinusIcon($(this)); });
        }, 50);

        $(document).off('click.cartQty').on('click.cartQty', '.custom-plus, .custom-minus', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $wrapper = $btn.closest('.custom-qty-wrapper');
            var $input = $wrapper.find('.qty');

            var min = parseFloat($input.attr('min'));
            if (isNaN(min)) min = 1;

            var rawMax = $input.attr('max');
            var max = null;
            if (typeof rawMax !== 'undefined' && rawMax !== false && rawMax !== '') {
                var parsedMax = parseFloat(rawMax);
                if (!isNaN(parsedMax) && parsedMax > 0) {
                    max = parsedMax;
                }
            }

            var rawStep = $input.attr('step');
            var step = parseFloat(rawStep);
            if (isNaN(step) || step <= 0) step = 1;

            var currentVal = parseFloat($input.val());
            if (isNaN(currentVal)) {
                currentVal = min;
            }

            if ($btn.hasClass('custom-plus')) {
                if (max !== null && currentVal >= max) {
                    return;
                }
                $input.val(currentVal + step).trigger('change');
            } else {
                if (currentVal <= 1) {
                    var $remove = $btn.closest('tr').find('.product-remove .remove');
                    if ($remove.length) {
                        $remove[0].click();
                        return;
                    }
                    $input.val(0).trigger('change');
                    submitCartDebounced();
                    return;
                }
                if (currentVal <= min) return;

                $input.val(currentVal - step).trigger('change');
            }

            updateMinusIcon($wrapper);
            submitCartDebounced();
        });

        $(document).off('change.cartQty input.cartQty').on('change.cartQty input.cartQty', '.custom-qty-wrapper .qty', function () {
            var $wrapper = $(this).closest('.custom-qty-wrapper');
            updateMinusIcon($wrapper);
            submitCartDebounced();
        });

        $(document.body).on('updated_cart_totals updated_wc_div', function () {
            $('.cart-main-content').css('opacity', '1');
            $('.custom-qty-wrapper').each(function () { updateMinusIcon($(this)); });
            $cartForm = $('.woocommerce-cart-form');
            $updateBtn = $cartForm.find('button[name="update_cart"]');

            // Check if cart is effectively empty (no rows)
            if ($('.cart-item-row').length === 0 && $('.woocommerce-cart-form__cart-item').length === 0) {
                window.location.reload();
                return;
            }

            // Refresh Custom Sidebar
            if ($('.cart-sidebar-totals').length) {
                $('.cart-sidebar-totals').css('opacity', '0.5');
                var ajaxUrl = (typeof global_cart_params !== 'undefined') ? global_cart_params.ajax_url : '/wp-admin/admin-ajax.php';
                $.post(ajaxUrl, { action: 'hello_get_cart_sidebar' }, function (res) {
                    if (res.success) {
                        $('.cart-sidebar-totals').html(res.data.html);
                    }
                    $('.cart-sidebar-totals').css('opacity', '1');
                });
            }
        });

        // Optimistic UI for Remove Button
        $(document).on('click', '.woocommerce-cart-form .product-remove .remove', function (e) {
            var $row = $(this).closest('tr');
            var $tbody = $row.closest('tbody');

            // Visual removal
            $row.fadeOut(300, function () {
                $(this).remove();
                // Check if that was the last item
                if ($tbody.find('tr.cart-item-row').length === 0) {
                    window.location.reload();
                }
            });
        });
    }

    // Expose for other templates if needed
    window.helloInitCartQtyControls = initCartQtyControls;

    // Auto-init on pages that have cart form
    if ($('.woocommerce-cart-form').length) {
        initCartQtyControls();
    }
});
