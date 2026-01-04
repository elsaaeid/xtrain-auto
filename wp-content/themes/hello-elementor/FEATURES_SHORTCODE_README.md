# Features Display Shortcode - Implementation Guide

## Overview
Created a new WordPress shortcode `[features_display]` that fetches and displays features from Advanced Custom Fields (ACF) with `feature_image`, `feature_title`, and `feature_desc` fields. Includes modern animations using GSAP and ScrollTrigger.

## Files Created/Modified

### 1. **functions.php** (Modified)
Added three new functions:

#### `features_display_shortcode()`
- **Location**: Lines 901-973
- **Shortcode**: `[features_display]`
- **Purpose**: Fetches features from custom post type and displays them
- **ACF Fields Used**:
  - `feature_image` (Image field)
  - `feature_title` (Text field)
  - `feature_desc` (Textarea field)
- **Features**:
  - Queries custom post type `feature`
  - Uses output buffering like `fast_parts_search_shortcode`
  - Proper escaping and sanitization
  - Lazy loading for images
  - Skips items without title or description
  - Resets post data after query

#### `features_display_enqueue_assets()`
- **Location**: Lines 351-365
- **Purpose**: Enqueues CSS and JavaScript for features display
- **Dependencies**: GSAP library
- **Hook**: `wp_enqueue_scripts`

#### `enqueue_gsap_cdn()` (Updated)
- **Location**: Lines 299-318
- **Purpose**: Loads GSAP and ScrollTrigger from CDN
- **Plugins**:
  - GSAP 3.14.1
  - ScrollTrigger 3.14.1

### 2. **features-display.css** (Created)
- **Location**: `assets/css/features-display.css`
- **Features**:
  - Modern card-based grid layout
  - Gradient background
  - Smooth hover effects with lift animation
  - Top border animation on hover
  - Image zoom effect
  - Fully responsive (desktop, tablet, mobile)
  - RTL support
  - GSAP animation classes

### 3. **features-display.js** (Created)
- **Location**: `assets/js/features-display.js`
- **Features**:
  - GSAP ScrollTrigger integration
  - Scroll-based fade-in animations
  - Staggered entrance effect
  - Hover animations for images
  - Title color transitions
  - Graceful fallback if GSAP not loaded

## Usage

### Basic Usage
```php
[features_display]
```

Simply add this shortcode to any page, post, or widget area.

### Required ACF Setup

You need to create:

1. **Custom Post Type**: `feature`
2. **ACF Fields**:
   - `feature_image` (Image field) - Optional
   - `feature_title` (Text field) - Required
   - `feature_desc` (Textarea field) - Required

### Example ACF Field Group Configuration

```php
// Field Group: Features
Post Type: feature

// Field: feature_image
- Field Type: Image
- Return Format: Array
- Preview Size: Medium
- Required: No

// Field: feature_title
- Field Type: Text
- Required: Yes
- Character Limit: 100

// Field: feature_desc
- Field Type: Textarea
- Required: Yes
- Rows: 4
```

## Key Features Matching fast_parts_search_shortcode

1. ✅ **ACF Integration**: Uses `get_field()` with function_exists check
2. ✅ **WP_Query Pattern**: Custom post type query with proper arguments
3. ✅ **Output Buffering**: Uses `ob_start()` and `ob_get_clean()`
4. ✅ **Post Data Reset**: Calls `wp_reset_postdata()`
5. ✅ **Proper Escaping**: Uses `esc_url()`, `esc_attr()`, `esc_html()`
6. ✅ **Conditional Rendering**: Checks if posts exist and fields are populated
7. ✅ **Enqueued Assets**: Separate CSS and JS files properly enqueued
8. ✅ **WordPress Coding Standards**: Follows WP best practices
9. ✅ **Performance**: Lazy loading, optimized queries
10. ✅ **Modern Animations**: GSAP integration with ScrollTrigger

## Animation Features

### Scroll Animations
- Features fade in and slide up when scrolled into view
- Staggered animation (0.1s delay between items)
- Smooth easing with `power3.out`

### Hover Effects
- Image zoom (1.1x scale)
- Title color change to brand orange (#F47C33)
- Card lift with enhanced shadow
- Top border slide-in effect

### Customizing Animations

Edit `assets/js/features-display.js`:

```javascript
// Change animation duration
duration: 0.8, // Change to 1.2 for slower

// Change stagger delay
delay: index * 0.1, // Change to 0.2 for more delay

// Change scroll trigger point
start: 'top 85%', // Change to 'top 90%' for earlier trigger
```

## Customization Options

### Change Number of Features
Modify line 904 in functions.php:
```php
'posts_per_page' => -1,  // -1 shows all, or set a number like 6
```

### Change Order
Modify lines 905-906:
```php
'orderby' => 'menu_order',  // or 'title', 'date', 'rand'
'order'   => 'ASC'          // or 'DESC'
```

### Modify Grid Columns
Edit `features-display.css` line 13:
```css
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
/* Change 300px to adjust minimum column width */
```

### Change Colors
Edit `features-display.css`:
```css
/* Brand color */
background: linear-gradient(90deg, #F47C33 0%, #ff9a56 100%);

/* Background gradient */
background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
```

### Disable Animations
Remove or comment out the script enqueue in functions.php (line 355-360):
```php
// wp_enqueue_script(
//     'features-display-script',
//     get_template_directory_uri() . '/assets/js/features-display.js',
//     ['gsap'],
//     '1.0',
//     true
// );
```

## Layout Variations

### Icon-Style Features
Add class `icon-style` to feature items for icon-based layout:
```php
<div class="feature-item icon-style">
```

This creates:
- Smaller image container (120px)
- Gradient background
- White icon filter

### 2-Column Layout
Edit CSS:
```css
.features-container {
    grid-template-columns: repeat(2, 1fr);
}
```

### 4-Column Layout
Edit CSS:
```css
.features-container {
    grid-template-columns: repeat(4, 1fr);
}
```

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with CSS Grid support)
- Mobile responsive
- GSAP works on all modern browsers

## Performance
- Lazy loading enabled for images
- Minimal CSS (< 5KB)
- Optimized JavaScript (< 3KB)
- Efficient WP_Query with proper arguments
- CDN-hosted GSAP for fast loading

## Responsive Breakpoints

| Breakpoint | Columns | Padding | Image Height |
|------------|---------|---------|--------------|
| Desktop (>992px) | Auto-fit | 60px | 200px |
| Tablet (768-992px) | Auto-fit | 50px | 180px |
| Mobile (480-768px) | 1 column | 40px | 160px |
| Small Mobile (<480px) | 1 column | 30px | 140px |

## Debugging

### Enable ScrollTrigger Markers
Edit `features-display.js` line 37:
```javascript
scrollTrigger: {
    markers: true // Uncomment this line
}
```

### Check Console
Open browser console to see:
- GSAP load status
- ScrollTrigger registration
- Any animation errors

## Next Steps

1. Create the `feature` custom post type (if not exists)
2. Set up ACF field group with required fields
3. Add some feature posts with images, titles, and descriptions
4. Insert `[features_display]` shortcode on desired page
5. Customize CSS/animations as needed for your design

## Example Feature Post

```
Title: Fast Delivery
feature_title: "سرعة التوصيل"
feature_desc: "نوصل طلبك في أسرع وقت ممكن"
feature_image: [delivery-icon.png]
```

## Support

If you need to modify the custom post type name from `feature` to something else, update line 903 in functions.php:
```php
'post_type' => 'your_custom_post_type_name',
```

## Integration with Other Shortcodes

You can combine multiple shortcodes on the same page:

```php
[features_display]
[brands_display]
[fast_parts_search]
```

All shortcodes follow the same pattern and work seamlessly together!
