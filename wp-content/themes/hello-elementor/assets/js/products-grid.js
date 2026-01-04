/**
 * Products Grid - Category Filtering & Pagination
 */
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.querySelector('.products-grid-wrapper');
    if (!wrapper) return;

    const tabBtns = wrapper.querySelectorAll('.tab-btn');
    const productItems = wrapper.querySelectorAll('.product-grid-item');
    const paginationDots = wrapper.querySelectorAll('.grid-pagination-dot');

    const productsPerPage = 6;
    let currentPage = 0;
    let filteredProducts = Array.from(productItems);

    // Category Tab Filtering
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const category = this.dataset.category;

            // Update active tab
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter products
            filteredProducts = [];
            productItems.forEach(item => {
                const itemCategories = item.dataset.category || '';

                if (category === 'all' || itemCategories.includes(category)) {
                    item.classList.remove('hidden');
                    filteredProducts.push(item);
                } else {
                    item.classList.add('hidden');
                }
            });

            // Reset pagination
            currentPage = 0;
            updatePagination();
            showPage(currentPage);
        });
    });

    // Pagination
    function showPage(page) {
        const start = page * productsPerPage;
        const end = start + productsPerPage;

        filteredProducts.forEach((item, index) => {
            if (index >= start && index < end) {
                item.style.display = '';
                // Re-trigger animation
                item.style.animation = 'none';
                item.offsetHeight; // Trigger reflow
                item.style.animation = null;
            } else {
                item.style.display = 'none';
            }
        });

        // Update active dot
        paginationDots.forEach((dot, index) => {
            dot.classList.toggle('active', index === page);
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        const paginationContainer = wrapper.querySelector('.products-grid-pagination');

        if (paginationContainer) {
            paginationContainer.innerHTML = '';

            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('span');
                dot.className = 'grid-pagination-dot' + (i === 0 ? ' active' : '');
                dot.dataset.page = i;
                dot.addEventListener('click', function () {
                    currentPage = parseInt(this.dataset.page);
                    showPage(currentPage);
                });
                paginationContainer.appendChild(dot);
            }
        }
    }

    // Initial pagination click handlers
    paginationDots.forEach(dot => {
        dot.addEventListener('click', function () {
            currentPage = parseInt(this.dataset.page);
            showPage(currentPage);
        });
    });

    // Add to Cart AJAX: intentionally no card-level loading to allow other actions
    // Delegated global handlers handle add-to-cart UI; avoid setting `.loading` on `.product-grid-card` here.

    // Wishlist button interaction (placeholder)
    const wishlistBtns = wrapper.querySelectorAll('.product-grid-wishlist');
    wishlistBtns.forEach(btn => {
        btn.setAttribute('aria-pressed', 'false');
        btn.addEventListener('click', function (e) {
            const isAnchor = this.tagName && this.tagName.toLowerCase() === 'a';
            if (!isAnchor) {
                e.preventDefault();
            }
            const isActive = this.classList.toggle('active');
            this.setAttribute('aria-pressed', isActive ? 'true' : 'false');

            // Animate heart
            const svg = this.querySelector('svg');
            if (svg) {
                svg.style.transform = 'scale(1.3)';
                svg.classList.toggle('active', isActive);
                setTimeout(() => {
                    svg.style.transform = 'scale(1)';
                }, 200);
            }

            // Dispatch change event so header/counts can update
            try {
                const ev = new CustomEvent('wishlist:changed', { detail: { productId: this.getAttribute('data-product-id'), active: isActive } });
                document.dispatchEvent(ev);
            } catch (err) {
                const ev = document.createEvent('CustomEvent');
                ev.initCustomEvent('wishlist:changed', true, true, { productId: this.getAttribute('data-product-id'), active: isActive });
                document.dispatchEvent(ev);
            }
        });
    });
});
