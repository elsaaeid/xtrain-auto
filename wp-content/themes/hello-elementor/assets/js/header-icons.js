document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".icon-btn").forEach(icon => {
        icon.addEventListener("click", () => {
            // reserved for analytics / tracking
        });
    });

    // YITH Wishlist integration: update header wishlist count when items are added/removed
    function findWishlistIcon() {
        const icons = Array.from(document.querySelectorAll('.icon-btn'));
        for (const el of icons) {
            // 1) If href contains 'wish' or 'wishlist', assume it's the wishlist
            try {
                const href = el.getAttribute && el.getAttribute('href') ? el.getAttribute('href').toLowerCase() : '';
                if (href.indexOf('wish') !== -1 || href.indexOf('wishlist') !== -1) return el;
            } catch (err) { }

            // 2) Look for FontAwesome heart icon (<i class="fa-... fa-heart">)
            const iEl = el.querySelector('i');
            if (iEl && iEl.className && /fa-?heart|heart/i.test(iEl.className)) return el;

            // 3) Look for SVG heart path (old heuristic)
            const svg = el.querySelector('svg');
            if (svg) {
                const html = svg.innerHTML || svg.outerHTML || '';
                if (html.indexOf('M20.84') !== -1 || /heart/i.test(html)) return el;
            }

            // 4) Also check title/aria-label for 'wish' or localized words
            const title = (el.getAttribute('title') || el.getAttribute('aria-label') || '').toLowerCase();
            if (title.indexOf('wish') !== -1 || title.indexOf('قائمة') !== -1 || title.indexOf('قلب') !== -1) return el;
        }
        return null;
    }

    function ensureCountBadge(iconEl) {
        if (!iconEl) return null;
        let badge = iconEl.querySelector('.icon-count');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'icon-count';
            badge.textContent = '0';
            // Hidden by default; will be shown when count > 0
            badge.style.display = 'none';
            iconEl.appendChild(badge);
        }
        // Normalize visibility based on current numeric content
        const val = parseInt(badge.textContent || '0', 10) || 0;
        badge.style.display = val > 0 ? '' : 'none';
        return badge;
    }

    const wishlistIcon = findWishlistIcon();
    const wishlistBadge = ensureCountBadge(wishlistIcon);

    // --- Cart Badge Logic ---
    function findCartIcon() {
        const icons = Array.from(document.querySelectorAll('.icon-btn'));
        for (const el of icons) {
            try {
                const href = el.getAttribute && el.getAttribute('href') ? el.getAttribute('href').toLowerCase() : '';
                if (href.indexOf('cart') !== -1 || href.indexOf('basket') !== -1 || href.indexOf('checkout') !== -1) return el;
            } catch (err) { }

            const iEl = el.querySelector('i');
            if (iEl && iEl.className && /fa-?shopping-(cart|bag|basket)|cart|basket/i.test(iEl.className)) return el;

            const title = (el.getAttribute('title') || el.getAttribute('aria-label') || '').toLowerCase();
            if (title.indexOf('cart') !== -1 || title.indexOf('سلة') !== -1 || title.indexOf('السلة') !== -1) return el;
        }
        return null;
    }
    const cartIcon = findCartIcon();
    const cartBadge = ensureCountBadge(cartIcon);

    // Override default click functionality for Wishlist
    if (wishlistIcon) {
        wishlistIcon.addEventListener('click', function (e) {
            // Priority 1: Use the link from the element itself (e.g. from ACF "Icon Link" field)
            const href = this.getAttribute('href');

            // If ACF link is set (valid URL), force navigation and BLOCK sidebar
            if (href && href.length > 2 && href.trim() !== '#' && href.indexOf('javascript:') === -1) {
                e.preventDefault();
                e.stopImmediatePropagation();
                window.location.href = href;
                return;
            }
            else {
                e.preventDefault();
                // STOP propagation to ensure no other sidebars/modals conflict
                e.stopPropagation();

                // Trigger our new Custom Wishlist Sidebar
                document.dispatchEvent(new Event('hello_open_wishlist_sidebar'));
            }
        }, true);
    }

    // Update badge helper
    function updateBadge(delta) {
        if (!wishlistBadge) return;
        const current = parseInt(wishlistBadge.textContent || '0', 10) || 0;
        let next = current + (delta || 0);
        if (next < 0) next = 0;
        wishlistBadge.textContent = String(next);
        wishlistBadge.style.display = next > 0 ? '' : 'none';
    }

    // Expose global fetch helper
    window.hello_refresh_wishlist_count = function () {
        if (typeof hello_header_icons === 'undefined' || !hello_header_icons.ajax_url) return;
        try {
            const data = new URLSearchParams();
            data.append('action', 'hello_get_wishlist_count');
            data.append('_', Math.random());

            fetch(hello_header_icons.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: data.toString()
            }).then(res => res.json()).then(json => {
                if (json && json.success && json.data && typeof json.data.count !== 'undefined') {
                    if (wishlistBadge) {
                        const c = parseInt(json.data.count, 10) || 0;
                        wishlistBadge.textContent = String(c);
                        wishlistBadge.style.display = c > 0 ? '' : 'none';
                    }
                }
            }).catch(() => { });
        } catch (err) { }
    };

    window.hello_refresh_cart_count = function () {
        if (typeof hello_header_icons === 'undefined' || !hello_header_icons.ajax_url) return;
        try {
            const data = new URLSearchParams();
            data.append('action', 'hello_get_cart_count');
            data.append('_', Math.random());

            fetch(hello_header_icons.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: data.toString()
            }).then(res => res.json()).then(json => {
                if (json && json.success && typeof json.data.count !== 'undefined') {
                    if (cartBadge) {
                        const c = parseInt(json.data.count, 10) || 0;
                        cartBadge.textContent = String(c);
                        cartBadge.style.display = c > 0 ? '' : 'none';
                    }
                }
            }).catch(() => { });
        } catch (err) { }
    };

    // Initial fetch
    window.hello_refresh_wishlist_count();
    window.hello_refresh_cart_count();

    // Robust fallback: ensure any heart/wishlist links get a badge
    (function ensureWishlistBadgeFallback() {
        // Select by FontAwesome heart <i> inside links/buttons or href containing 'wish'
        const candidates = Array.from(document.querySelectorAll('a[href], button'));
        for (const el of candidates) {
            try {
                const href = el.getAttribute && el.getAttribute('href') ? el.getAttribute('href').toLowerCase() : '';
                if (href.indexOf('wish') !== -1 || href.indexOf('wishlist') !== -1) {
                    const b = ensureCountBadge(el);
                    if (b) b.style.display = 'none';
                    return;
                }
            } catch (err) { }

            const iEl = el.querySelector && el.querySelector('i');
            if (iEl && iEl.className && /fa-?heart|heart/i.test(iEl.className)) {
                const b = ensureCountBadge(el);
                if (b) b.style.display = 'none';
                return;
            }
        }
    })();

    document.addEventListener('added_to_wishlist', function (e) {
        window.hello_refresh_wishlist_count();
    });

    document.addEventListener('removed_from_wishlist', function (e) {
        window.hello_refresh_wishlist_count();
    });

    // Cart Events
    jQuery(document.body).on('added_to_cart removed_from_cart wc_fragments_refreshed wc_fragment_refresh updated_cart_totals updated_wc_div', function () {
        window.hello_refresh_cart_count();
    });

    // Also listen for generic YITH fragment response which may carry HTML fragments
    document.addEventListener('yith_wcwl_fragment_response', function (e) {
        // If plugin updates fragments, try to extract a numeric count from fragment HTML
        if (!e || !e.detail) return;
        try {
            const fragments = e.detail.fragments || e.detail;
            // Search fragment HTML for a count number
            for (const key in fragments) {
                if (!fragments.hasOwnProperty(key)) continue;
                const html = fragments[key] || '';
                const m = html.match(/(\d+)/);
                if (m) {
                    if (wishlistBadge) wishlistBadge.textContent = m[1];
                    return;
                }
            }
        } catch (err) { }
    });


    // Single Product Wishlist Button Handler
    document.addEventListener('DOMContentLoaded', () => {
        const singleWishBtn = document.querySelector('.single-wishlist-btn');
        if (singleWishBtn) {
            singleWishBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const btn = this;
                const icon = btn.querySelector('i');
                const productId = btn.getAttribute('data-product-id');

                if (!productId) return;

                // Optimistic UI update
                btn.classList.toggle('active');
                if (icon.classList.contains('far')) {
                    icon.classList.replace('far', 'fas');
                } else {
                    icon.classList.replace('fas', 'far');
                }

                // AJAX Request
                const data = new URLSearchParams();
                data.append('action', 'hello_toggle_wishlist');
                data.append('product_id', productId);

                fetch(hello_header_icons.ajax_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: data.toString()
                })
                    .then(res => res.json())
                    .then(json => {
                        if (json.success) {
                            // Trigger global event for header badge update
                            const eventName = json.data.action === 'added' ? 'added_to_wishlist' : 'removed_from_wishlist';
                            const event = new CustomEvent(eventName, { detail: { count: json.data.count } });
                            document.dispatchEvent(event);
                        } else {
                            // Revert on failure
                            btn.classList.toggle('active');
                            if (icon.classList.contains('fas')) {
                                icon.classList.replace('fas', 'far');
                            } else {
                                icon.classList.replace('far', 'fas');
                            }
                            console.error(json.data);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        // Revert
                        btn.classList.toggle('active');
                    });
            });
        }
    });
});
