// JS for products_filter_grid AJAX filter
jQuery(document).ready(function($) {
    function updatePriceLabels() {
        $('#price-min-value').text($('#price-min').val());
        $('#price-max-value').text($('#price-max').val());
    }
    updatePriceLabels();
    $('#price-min, #price-max').on('input change', updatePriceLabels);

    function fetchProducts() {
        var data = $('#products-filter-form').serialize();
        $('#products-filter-results').html('<div class="loading">جاري التحميل...</div>');
        $.get(products_filter_grid.ajax_url, 'action=products_filter_grid&' + data, function(response) {
            $('#products-filter-results').html(response);
        });
    }

    // Initial load
    fetchProducts();

    $('#products-filter-form').on('submit', function(e) {
        e.preventDefault();
        fetchProducts();
    });

    // Optionally, auto-submit on filter change
    $('#products-filter-form').on('change', 'input, select', function() {
        fetchProducts();
    });
});
