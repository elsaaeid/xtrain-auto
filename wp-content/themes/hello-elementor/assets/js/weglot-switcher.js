// Custom Weglot Switcher Arrow Rotation
// This script toggles the arrow on .wgcurrent:after when the dropdown is open

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.country-selector.weglot-dropdown').forEach(function (dropdown) {
    var current = dropdown.querySelector('.wgcurrent');
    var menu = dropdown.querySelector('ul');
    if (!current) return;

    // Toggle arrow on dropdown click
    dropdown.addEventListener('click', function (e) {
      if (e.target === dropdown || e.target.closest('.wgcurrent')) {
        e.preventDefault();
        var input = dropdown.querySelector('input[type="checkbox"], input[type="radio"]');
        if (input) {
          input.checked = !input.checked;
          if (input.checked) {
            current.classList.add('active-arrow');
          } else {
            current.classList.remove('active-arrow');
          }
        } else {
          current.classList.toggle('active-arrow');
        }
      }
    });

    // Remove arrow rotation when clicking outside
    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target)) {
        current.classList.remove('active-arrow');
        var input = dropdown.querySelector('input[type="checkbox"], input[type="radio"]');
        if (input) input.checked = false;
      }
    });

    // Remove arrow rotation and close dropdown when clicking inside the menu
    if (menu) {
      menu.addEventListener('click', function (e) {
        // Only close if clicking a menu item (li or a)
        if (e.target.tagName === 'LI' || e.target.tagName === 'A' || e.target.closest('li')) {
          current.classList.remove('active-arrow');
          // Try to close the dropdown if using input:checked for open state
          var input = dropdown.querySelector('input[type="checkbox"], input[type="radio"]');
          if (input && input.checked) {
            input.checked = false;
          }
          // If menu is shown by class, try to hide it
          if (menu.style.display === 'block' || menu.classList.contains('open')) {
            menu.style.display = 'none';
            menu.classList.remove('open');
          }
        }
      });
    }
  });
});
