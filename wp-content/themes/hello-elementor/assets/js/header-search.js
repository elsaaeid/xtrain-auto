document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.querySelector('.exact-search-wrapper');
    const toggle = document.querySelector('.exact-search-toggle');
    const input = document.getElementById('exact-search-input');
    const icon = document.querySelector('.exact-search-icon');
    const results = document.getElementById('exact-search-results');
    let timer;

    if (!input || !wrapper) return;

    // Toggle visibility on mobile
    if (toggle) {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = wrapper.classList.toggle('is-active');
            if (isActive) {
                setTimeout(() => input.focus(), 100);
            }
        });
    }

    // Inner icon click also reveals/focuses
    if (icon) {
        icon.addEventListener('click', () => {
            wrapper.classList.add('is-active');
            setTimeout(() => input.focus(), 100);
        });
    }

    // Search logic
    input.addEventListener('input', function () {
        clearTimeout(timer);
        const val = this.value.trim();
        if (val.length < 2) {
            results.innerHTML = '';
            results.classList.remove('has-results');
            return;
        }
        timer = setTimeout(function () {
            const ajaxUrl = typeof header_search !== 'undefined' ? header_search.ajax_url : '/wp-admin/admin-ajax.php';
            fetch(ajaxUrl + '?action=header_search&term=' + encodeURIComponent(val))
                .then(res => res.text())
                .then(html => {
                    if (val.length < 2) {
                        results.innerHTML = '';
                        results.classList.remove('has-results');
                    } else if (html.trim()) {
                        results.innerHTML = html;
                        results.classList.add('has-results');
                    } else {
                        results.innerHTML = '<div class="no-results">No results found</div>';
                        results.classList.add('has-results');
                    }
                });
        }, 300);
    });

    // Close on outside click or click on backdrop
    document.addEventListener('click', (e) => {
        // If search is active AND we click something that is NOT the search container or toggle
        if (wrapper.classList.contains('is-active') &&
            !e.target.closest('.exact-search-container') &&
            !e.target.closest('.exact-search-toggle')) {

            wrapper.classList.remove('is-active');
            results.innerHTML = '';
            results.classList.remove('has-results');
        }
    });
});
