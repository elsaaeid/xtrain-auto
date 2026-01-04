// Custom Currency Dropdown: Close on outside click
// This script closes the dropdown if you click outside of it

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.custom-currency-dropdown input[type="checkbox"]').forEach(function(checkbox) {
    var dropdown = checkbox.closest('.custom-currency-dropdown');
    document.addEventListener('click', function(e) {
      if (!dropdown.contains(e.target)) {
        checkbox.checked = false;
      }
    });
    // Optional: close dropdown when a currency is selected
    dropdown.querySelectorAll('.currency-list a').forEach(function(link) {
      link.addEventListener('click', function() {
        checkbox.checked = false;
      });
    });
  });
});
