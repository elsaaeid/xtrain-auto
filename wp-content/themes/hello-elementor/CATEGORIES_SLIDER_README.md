# Categories Slider Shortcode - Implementation Guide

## Overview
Created a new WordPress shortcode `[categories_slider]` that displays categories from Advanced Custom Fields (ACF) with Arabic fields (اسم_الصنف, صور_الصنف, عدد_الاصناف) in a responsive slider with navigation controls.

## Files Created/Modified

### 1. **functions.php** (Modified)
Added the `categories_slider_shortcode()` function:

- **Location**: Lines 992-1085
- **Shortcode**: `[categories_slider]`
- **Purpose**: Fetches categories and displays them in a slider
- **ACF Fields Used**:
  - `اسم_الصنف` (Category Name) - Text field
  - `صور_الصنف` (Category Image) - Image field
  - `عدد_الاصناف` (Items Count) - Text/Number field
- **Custom Post Type**: `category_menu`

### 2. **categories-slider.css** (Created)
- **Location**: `assets/css/categories-slider.css`
- **Features**:
  - RTL-first design
  - Responsive grid (4 → 3 → 2 → 1 columns)
  - Card-based layout matching uploaded image
  - Hover effects with lift animation
  - Navigation button styling
  - CSS animations for entrance
  - All colors use CSS variables

### 3. **categories-slider.js** (Created)
- **Location**: `assets/js/categories-slider.js`
- **Features**:
  - Touch/swipe support for mobile
  - Mouse drag for desktop
  - Keyboard navigation (arrow keys)
  - RTL/LTR support
  - Responsive breakpoints
  - GSAP integration for smooth animations
  - Auto-play option (commented out)
  - Smooth transitions

### 4. **categories_slider_enqueue_assets()** (Added)
- **Location**: Lines 372-389
- **Purpose**: Enqueues CSS and JavaScript
- **Dependencies**: GSAP library

## Design Match

The slider matches your uploaded image with:
- ✅ Clean white cards with borders
- ✅ Centered product images
- ✅ Category name below image
- ✅ Item count display
- ✅ Navigation arrows (prev/next)
- ✅ "تصفح الأقسام الفرعية" link
- ✅ RTL layout
- ✅ Responsive design

## Usage

### Basic Usage
```php
[categories_slider]
```

### Required ACF Setup

1. **Custom Post Type**: `category_menu` (already exists in your theme)
2. **ACF Fields**:
   - `اسم_الصنف` (Text field) - Category name
   - `صور_الصنف` (Image field) - Category image
   - `عدد_الاصناف` (Text/Number field) - Number of items

### Example ACF Configuration

```php
// Field Group: Category Menu Fields
Post Type: category_menu

// Field: اسم_الصنف
- Field Type: Text
- Required: Yes

// Field: صور_الصنف
- Field Type: Image
- Return Format: Array
- Required: Yes

// Field: عدد_الاصناف
- Field Type: Text or Number
- Required: No
- Default: 27
```

## Slider Features

### Navigation
- **Previous/Next Buttons**: Click to navigate
- **Touch/Swipe**: Swipe on mobile devices
- **Mouse Drag**: Drag on desktop
- **Keyboard**: Use arrow keys (← →)

### Responsive Breakpoints
| Screen Size | Slides Visible | Gap |
|-------------|----------------|-----|
| > 1200px | 4 slides | 20px |
| 992-1200px | 3 slides | 20px |
| 768-992px | 2 slides | 20px |
| < 768px | 1 slide | 20px |

### RTL Support
- Fully supports RTL (Right-to-Left) layout
- Navigation buttons swap positions
- Swipe direction adapts to RTL
- Automatic detection of document direction

## Customization Options

### Change Slides Per View
Edit `categories-slider.js` line 27-37:
```javascript
function calculateSlidesToShow() {
    const width = window.innerWidth;
    if (width <= 768) {
        slidesToShow = 1; // Change this
    } else if (width <= 992) {
        slidesToShow = 2; // Change this
    } else if (width <= 1200) {
        slidesToShow = 3; // Change this
    } else {
        slidesToShow = 4; // Change this
    }
}
```

### Enable Auto-Play
Uncomment lines 214-232 in `categories-slider.js`:
```javascript
let autoplayInterval;
function startAutoplay() {
    autoplayInterval = setInterval(() => {
        if (currentIndex >= maxIndex) {
            currentIndex = 0;
        } else {
            currentIndex++;
        }
        updateSlider();
    }, 3000); // Change interval (milliseconds)
}
```

### Change Slider Speed
Edit `categories-slider.css` line 52:
```css
.categories-slider-track {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    /* Change 0.5s to your preferred speed */
}
```

### Modify Card Appearance
Edit `categories-slider.css`:
```css
.category-card {
    padding: 30px 20px; /* Change padding */
    border-radius: 12px; /* Change border radius */
}

.category-image {
    height: 150px; /* Change image height */
}
```

### Change Header Text
Edit `functions.php` lines 1020-1021:
```php
<h2 class="categories-slider-title">الأقسام</h2>
<a href="#" class="categories-view-all">تصفح الأقسام الفرعية</a>
```

## JavaScript API

### Manual Navigation
```javascript
// Get slider instance
const prevBtn = document.querySelector('.slider-prev');
const nextBtn = document.querySelector('.slider-next');

// Trigger navigation
prevBtn.click(); // Go to previous
nextBtn.click(); // Go to next
```

### Custom Events (Add to JS file)
```javascript
// Dispatch custom event on slide change
function updateSlider() {
    // ... existing code ...
    
    // Dispatch event
    const event = new CustomEvent('sliderChange', {
        detail: { currentIndex: currentIndex }
    });
    sliderWrapper.dispatchEvent(event);
}

// Listen for changes
sliderWrapper.addEventListener('sliderChange', function(e) {
    console.log('Slider changed to index:', e.detail.currentIndex);
});
```

## Performance

- ✅ Lazy loading for images
- ✅ Debounced resize handler
- ✅ CSS transforms for smooth animations
- ✅ Passive event listeners for touch
- ✅ Efficient DOM queries
- ✅ GSAP for optimized animations

## Browser Compatibility

- ✅ Modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)
- ✅ Touch devices
- ✅ RTL languages
- ⚠️ IE11 (limited support, no CSS Grid)

## Accessibility

- ✅ ARIA labels on navigation buttons
- ✅ Keyboard navigation support
- ✅ Focus management
- ✅ Alt text for images
- ✅ Semantic HTML

## GSAP Animations

### Entrance Animation
Categories fade in and slide up when scrolled into view:
```javascript
gsap.from('.category-slide', {
    scrollTrigger: {
        trigger: '.categories-slider-wrapper',
        start: 'top 80%',
    },
    opacity: 0,
    y: 30,
    duration: 0.6,
    stagger: 0.1
});
```

### Slide Transition
Smooth opacity transition when navigating:
```javascript
gsap.fromTo(track, 
    { opacity: 0.8 },
    { opacity: 1, duration: 0.3 }
);
```

## Troubleshooting

### Slider Not Working
1. Check if GSAP is loaded: `console.log(typeof gsap)`
2. Check browser console for errors
3. Verify ACF fields are populated
4. Ensure `category_menu` posts exist

### Images Not Showing
1. Verify `صور_الصنف` field has images
2. Check image URLs in browser inspector
3. Ensure images are uploaded to media library

### Navigation Buttons Not Appearing
1. Check if there are enough slides to navigate
2. Verify CSS is loaded
3. Check z-index conflicts

### RTL Issues
1. Verify `dir="rtl"` on `<html>` or `<body>`
2. Check CSS RTL overrides
3. Test navigation direction

## Integration Examples

### With Elementor
1. Add Shortcode widget
2. Insert: `[categories_slider]`
3. Adjust widget spacing as needed

### With Gutenberg
1. Add Shortcode block
2. Insert: `[categories_slider]`
3. Preview and publish

### In PHP Template
```php
<?php echo do_shortcode('[categories_slider]'); ?>
```

### Multiple Sliders on Same Page
Each slider instance works independently:
```php
[categories_slider]
<!-- Other content -->
[categories_slider]
```

## Advanced Customization

### Filter Categories
Add to `functions.php` before the query:
```php
$args = array(
    'post_type'      => 'category_menu',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => array(
        array(
            'key'     => 'featured',
            'value'   => '1',
            'compare' => '='
        )
    )
);
```

### Custom Link for "View All"
Edit line 1021 in `functions.php`:
```php
<a href="<?php echo esc_url(home_url('/categories')); ?>" class="categories-view-all">
    تصفح الأقسام الفرعية
</a>
```

### Add Category Description
Modify the shortcode to include description:
```php
$category_desc = get_field('category_description', $post_id);
if ( $category_desc ) : ?>
    <p class="category-desc"><?php echo esc_html($category_desc); ?></p>
<?php endif;
```

## Next Steps

1. ✅ Create/verify `category_menu` custom post type
2. ✅ Set up ACF fields (اسم_الصنف, صور_الصنف, عدد_الاصناف)
3. ✅ Add category posts with images
4. ✅ Insert `[categories_slider]` shortcode on page
5. ✅ Test on different devices
6. ✅ Customize colors/spacing as needed

## Support

For detailed setup of other shortcodes, see:
- `BRANDS_SHORTCODE_README.md`
- `FEATURES_SHORTCODE_README.md`
- `SHORTCODES_REFERENCE.md`

---

**Last Updated**: 2025-12-21  
**Version**: 1.0  
**Dependencies**: ACF, GSAP 3.14.1, ScrollTrigger
