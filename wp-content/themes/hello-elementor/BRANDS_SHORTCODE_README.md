# Brands Display Shortcode - Implementation Guide

## Overview
Created a new WordPress shortcode `[brands_display]` that fetches and displays brand images from Advanced Custom Fields (ACF), following the same pattern as the `fast_parts_search_shortcode` function.

## Files Created/Modified

### 1. **functions.php** (Modified)
Added two new functions:

#### `brands_display_shortcode()`
- **Location**: Lines 804-866
- **Shortcode**: `[brands_display]`
- **Purpose**: Fetches brands from custom post type and displays brand images
- **Features**:
  - Queries custom post type `brand`
  - Retrieves `brand_image` ACF field
  - Optional `brand_url` field for clickable brand logos
  - Uses output buffering like `fast_parts_search_shortcode`
  - Includes proper escaping and sanitization
  - Lazy loading for images
  - Resets post data after query

#### `brands_display_enqueue_assets()`
- **Location**: Lines 319-328
- **Purpose**: Enqueues the brands display CSS stylesheet
- **Hook**: `wp_enqueue_scripts`

### 2. **brands-display.css** (Created)
- **Location**: `assets/css/brands-display.css`
- **Features**:
  - Modern grid layout with `auto-fit` columns
  - Responsive design (desktop, tablet, mobile)
  - Hover effects (lift animation, color transition)
  - Grayscale to color effect on hover
  - Clean, minimal design with subtle shadows
  - Fully responsive breakpoints

## Usage

### Basic Usage
```php
[brands_display]
```

Simply add this shortcode to any page, post, or widget area.

### Required ACF Setup

You need to create:

1. **Custom Post Type**: `brand`
2. **ACF Fields**:
   - `brand_image` (Image field) - **Required**
   - `brand_url` (URL field) - Optional (for clickable logos)

### Example ACF Field Group Configuration

```php
// Field: brand_image
- Field Type: Image
- Return Format: Array
- Preview Size: Medium

// Field: brand_url (optional)
- Field Type: URL
- Return Format: URL
```

## Key Features Matching fast_parts_search_shortcode

1. ✅ **ACF Integration**: Uses `get_field()` with function_exists check
2. ✅ **WP_Query Pattern**: Custom post type query with proper arguments
3. ✅ **Output Buffering**: Uses `ob_start()` and `ob_get_clean()`
4. ✅ **Post Data Reset**: Calls `wp_reset_postdata()`
5. ✅ **Proper Escaping**: Uses `esc_url()`, `esc_attr()`, `esc_html()`
6. ✅ **Conditional Rendering**: Checks if posts exist before rendering
7. ✅ **Enqueued Assets**: Separate CSS file properly enqueued
8. ✅ **WordPress Coding Standards**: Follows WP best practices

## Customization Options

### Change Number of Brands
Modify line 816 in functions.php:
```php
'posts_per_page' => -1,  // -1 shows all, or set a number like 12
```

### Change Order
Modify lines 817-818:
```php
'orderby' => 'menu_order',  // or 'title', 'date', 'rand'
'order'   => 'ASC'          // or 'DESC'
```

### Modify Grid Columns
Edit `brands-display.css` line 13:
```css
grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
/* Change 150px to adjust minimum column width */
```

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with CSS Grid support)
- Mobile responsive

## Performance
- Lazy loading enabled for images
- Minimal CSS (< 2KB)
- No JavaScript required
- Efficient WP_Query with proper arguments

## Next Steps

1. Create the `brand` custom post type (if not exists)
2. Set up ACF field group with `brand_image` field
3. Add some brand posts with images
4. Insert `[brands_display]` shortcode on desired page
5. Customize CSS as needed for your design

## Support

If you need to modify the custom post type name from `brand` to something else, update line 815 in functions.php:
```php
'post_type' => 'your_custom_post_type_name',
```
