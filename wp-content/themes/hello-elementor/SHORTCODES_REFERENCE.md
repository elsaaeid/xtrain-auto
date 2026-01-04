# All Custom Shortcodes - Quick Reference

## 📋 Overview
This document provides a quick reference for all custom shortcodes created for the xtrain-auto WordPress theme.

---

## 🔍 1. Fast Parts Search
**Shortcode**: `[fast_parts_search]`

### Purpose
Search form for finding car parts with 4 input fields.

### ACF Fields Required
- `fps_title` (Text)
- `fps_subtitle` (Text)
- `fps_field1` (Text - placeholder)
- `fps_field2` (Text - placeholder)
- `fps_field3` (Text - placeholder)
- `fps_field4` (Text - placeholder)
- `fps_button` (Text)

### Custom Post Type
`fast_search_part`

### Files
- CSS: `assets/css/fast-parts-search.css`
- JS: `assets/js/fast-parts-search.js`

---

## 🏢 2. Brands Display
**Shortcode**: `[brands_display]`

### Purpose
Display brand logos in a responsive grid layout.

### ACF Fields Required
- `brand_image` (Image) - **Required**
- `brand_url` (URL) - Optional

### Custom Post Type
`brand`

### Files
- CSS: `assets/css/brands-display.css`
- Function: `brands_display_shortcode()`

### Features
- Responsive grid layout
- Grayscale to color hover effect
- Lazy loading images
- Optional clickable logos

---

## ⭐ 3. Features Display
**Shortcode**: `[features_display]`

### Purpose
Display features/services with image, title, and description.

### ACF Fields Required
- `feature_image` (Image) - Optional
- `feature_title` (Text) - **Required**
- `feature_desc` (Textarea) - **Required**

### Custom Post Type
`feature`

### Files
- CSS: `assets/css/features-display.css`
- JS: `assets/js/features-display.js`

### Features
- Modern card-based layout
- GSAP scroll animations
- Hover effects
- Gradient backgrounds
- Fully responsive

---

## 🔍 4. Header Search
**Shortcode**: `[header_search]`

### Purpose
WooCommerce product search with live results.

### Features
- AJAX search
- Live product results
- Mobile toggle
- Product thumbnails and prices

### Files
- CSS: `assets/css/header-search.css`
- JS: `assets/js/header-search.js`

---

## 🧭 5. Navigation with Categories
**Shortcode**: `[nav_with_categories]`

### Purpose
Primary menu with categories dropdown.

### ACF Fields Required
- `اسم_الصنف` (Text)
- `صور_الصنف` (Image)

### Custom Post Type
`category_menu`

### Files
- CSS: `assets/css/header-nav.css`
- JS: `assets/js/header-nav.js`

---

## 🎨 Global Assets

### GSAP Animation Library
**Version**: 3.14.1
**CDN**: jsDelivr
**Plugins**:
- GSAP Core
- ScrollTrigger

**Function**: `enqueue_gsap_cdn()`

### Usage in Custom Scripts
```javascript
// Simple animation
gsap.to('.element', {duration: 1, x: 100});

// Scroll animation
gsap.to('.element', {
    scrollTrigger: {
        trigger: '.element',
        start: 'top 80%'
    },
    opacity: 1,
    y: 0
});
```

---

## 📁 File Structure

```
wp-content/themes/hello-elementor/
├── functions.php (All shortcode functions)
├── assets/
│   ├── css/
│   │   ├── fast-parts-search.css
│   │   ├── brands-display.css
│   │   ├── features-display.css
│   │   ├── header-search.css
│   │   ├── header-nav.css
│   │   └── header-icons.css
│   └── js/
│       ├── fast-parts-search.js
│       ├── features-display.js
│       ├── header-search.js
│       ├── header-nav.js
│       └── header-icons.js
├── BRANDS_SHORTCODE_README.md
└── FEATURES_SHORTCODE_README.md
```

---

## 🚀 Quick Setup Guide

### 1. Create Custom Post Types
```php
// Add to functions.php or use a plugin like CPT UI
register_post_type('brand', [...]);
register_post_type('feature', [...]);
register_post_type('fast_search_part', [...]);
register_post_type('category_menu', [...]);
```

### 2. Create ACF Field Groups
- Go to Custom Fields > Add New
- Create field groups for each post type
- Add required fields as listed above

### 3. Add Content
- Create posts in each custom post type
- Fill in ACF fields
- Upload images

### 4. Insert Shortcodes
Add shortcodes to pages using:
- Elementor: Shortcode widget
- Gutenberg: Shortcode block
- Classic Editor: Direct insertion

---

## 🎯 Common Patterns

All shortcodes follow the same pattern:

1. ✅ Check if ACF is available
2. ✅ Query custom post type with WP_Query
3. ✅ Check if posts exist
4. ✅ Use output buffering
5. ✅ Loop through posts
6. ✅ Get ACF fields
7. ✅ Render HTML with proper escaping
8. ✅ Reset post data
9. ✅ Return buffered output

### Example Pattern
```php
function my_shortcode() {
    if (!function_exists('get_field')) return '';
    
    $q = new WP_Query([...]);
    if (!$q->have_posts()) return '';
    
    ob_start();
    while ($q->have_posts()) : $q->the_post();
        $field = get_field('field_name');
        // Render HTML
    endwhile;
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('my_shortcode', 'my_shortcode');
```

---

## 🔧 Customization

### Change Number of Items
```php
'posts_per_page' => -1, // All items
'posts_per_page' => 6,  // Limit to 6
```

### Change Order
```php
'orderby' => 'menu_order', // Manual order
'orderby' => 'date',       // By date
'orderby' => 'title',      // Alphabetical
'orderby' => 'rand',       // Random
```

### Change Grid Columns
```css
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
/* Change 300px to adjust column width */
```

---

## 📱 Responsive Design

All shortcodes are fully responsive with breakpoints:
- **Desktop**: > 992px
- **Tablet**: 768px - 992px
- **Mobile**: 480px - 768px
- **Small Mobile**: < 480px

---

## 🌐 RTL Support

All shortcodes support RTL (Right-to-Left) languages:
```css
[dir="rtl"] .element {
    /* RTL-specific styles */
}
```

---

## 🎬 Animation Support

### Features with GSAP
- ✅ Features Display (scroll animations)
- ✅ Available globally for custom animations

### Adding Animations to Other Shortcodes
```javascript
// Add GSAP dependency when enqueueing
wp_enqueue_script('my-script', '...', ['gsap'], '1.0', true);
```

---

## 📊 Performance

All shortcodes are optimized for performance:
- ✅ Lazy loading images
- ✅ Efficient database queries
- ✅ Minified assets (when needed)
- ✅ CDN for external libraries
- ✅ Conditional loading

---

## 🐛 Debugging

### Check if Shortcode is Registered
```php
global $shortcode_tags;
var_dump($shortcode_tags);
```

### Test Shortcode Output
```php
echo do_shortcode('[features_display]');
```

### Check ACF Fields
```php
$field = get_field('field_name');
var_dump($field);
```

---

## 📞 Support

For detailed documentation on each shortcode:
- Brands: See `BRANDS_SHORTCODE_README.md`
- Features: See `FEATURES_SHORTCODE_README.md`

For WordPress/ACF help:
- [ACF Documentation](https://www.advancedcustomfields.com/resources/)
- [WordPress Shortcode API](https://developer.wordpress.org/plugins/shortcodes/)
- [GSAP Documentation](https://greensock.com/docs/)

---

## ✅ Checklist

Before using shortcodes:
- [ ] ACF plugin installed and activated
- [ ] Custom post types registered
- [ ] ACF field groups created
- [ ] Sample content added
- [ ] Shortcode tested on page
- [ ] Responsive design checked
- [ ] Browser compatibility verified

---

**Last Updated**: 2025-12-21
**Theme**: Hello Elementor
**WordPress Version**: 5.0+
**ACF Version**: 5.0+
