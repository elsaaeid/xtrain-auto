// GSAP animation for SVG feature images
// Animates all SVGs with the class 'gsap-feature-svg' in the features display

document.addEventListener('DOMContentLoaded', function() {
  if (typeof gsap !== 'undefined') {
    // Check if elements exist before attempting animation
    var elements = document.querySelectorAll('.gsap-feature-svg');
    if (elements && elements.length > 0) {
      gsap.from('.gsap-feature-svg', {
        scale: 0.7,
        opacity: 0,
        y: 40,
        duration: 1.2,
        ease: 'power2.out',
        stagger: 0.15
      });
    }
  }
});
