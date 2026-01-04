document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".fps-form");
    if (!form) return;

    // Use the form's defined action (home_url) as the base for redirection
    // const resultsContainer = ... (removed unused AJAX container code)

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Collect input values
        const type = form.querySelector('[name="type"]').value.trim();
        const location = form.querySelector('[name="location"]').value.trim();
        const yearInput = form.querySelector('[name="year"]');
        const year = yearInput.value.trim();
        const model = form.querySelector('[name="model"]').value.trim();

        // No required validation, but if year is filled, must be a number
        if (year && isNaN(Number(year))) {
            yearInput.style.borderColor = "#e74c3c";
            alert("يرجى إدخال سنة صحيحة");
            return;
        } else {
            yearInput.style.borderColor = "var(--color-border)";
        }

        // Build search query from all fields
        const searchTerm = [type, location, year, model].filter(Boolean).join(' ');

        // Construct target URL using the form's action
        // User requested explicit path: http://localhost/xtrain-auto/shop
        // We will use the relative path /xtrain-auto/shop/

        // Base URL for the shop page
        let targetUrl = '/xtrain-auto/shop/';
        const queryParams = [];

        if (searchTerm) {
            // Using 'q_search' to avoid triggering standard WordPress search template (search.php)
            // This ensures we stay on the Shop page which contains our custom filter shortcode
            queryParams.push('q_search=' + encodeURIComponent(searchTerm));
        }

        // If specific logic required to pass individual fields as separate params later, add here
        // For now, filtering depends on 's' as per products-filter-grid-full support

        if (queryParams.length > 0) {
            targetUrl += '?' + queryParams.join('&');
        }

        window.location.href = targetUrl;
    });
});
