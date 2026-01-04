<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	?>

	<section class="related products">
		<style>
		.product-progress{margin:12px 0 6px}
		.product-progress .progress-track{background:#eef2f6;border-radius:6px;overflow:hidden;height:8px;position:relative;display:flex}
		.product-progress .progress-sold{background:#d1d5db;height:100%;transition:width .6s ease}
		.product-progress .progress-available{background:#f97316;height:100%;transition:width .6s ease}
		.product-progress .progress-track.unknown{background:#f3f4f6;display:block}
		.product-progress .progress-track.unknown .progress-indicator{width:6px;height:100%;background:#f97316;border-radius:3px;margin-left:6px}
		.product-progress .progress-meta{display:flex;justify-content:space-between;font-size:13px;color:#6b7280;margin-top:6px}
		</style>

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php
		// Remove default sale flash hook to show our custom percentage badge
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		?>
		<ul class="products">

			<?php
			/**
			 * Temporarily remove the default loop add-to-cart button and the automatic
			 * product-link closer for related products so we can manually close the
			 * link before rendering our custom add-to-cart markup (this avoids nested anchors).
			 */
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			foreach ( $related_products as $related_product ) :

				// Setup product data
				$post_object = get_post( $related_product->get_id() );
				setup_postdata( $GLOBALS['post'] = $post_object );
				$product_id = $related_product->get_id();
				$product_obj = wc_get_product( $product_id );
				$product_link = $product_obj ? $product_obj->get_permalink() : get_permalink( $product_id );
				$product_title = $product_obj ? $product_obj->get_name() : get_the_title( $product_id );
				$product_price = $product_obj ? $product_obj->get_price_html() : '';
				$product_rating = $product_obj ? floatval( $product_obj->get_average_rating() ) : 0;
				$is_on_sale = $product_obj ? $product_obj->is_on_sale() : false;
				$is_in_stock = $product_obj ? $product_obj->is_in_stock() : false;
				$product_stock = ( $product_obj && method_exists( $product_obj, 'get_stock_quantity' ) ) ? $product_obj->get_stock_quantity() : null;
				$product_image = '';
				$image_id = $related_product->get_image_id();
				if ( $image_id ) {
					$product_image = wp_get_attachment_image( $image_id, 'medium' );
				}

				// Wishlist URL (YITH integration)
				$add_to_wishlist_url = '';
				if ( defined( 'YITH_WCWL' ) || function_exists( 'YITH_WCWL' ) ) {
					$base_url = ( is_ssl() ? 'https://' : 'http://' ) . ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '' ) . ( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
					$add_to_wishlist_url = wp_nonce_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ), 'add_to_wishlist' );
				}

				// qty in cart check (canonical)
				$qty_in_cart = 0;
				if ( class_exists( 'WooCommerce' ) && WC()->cart ) {
					$cart_id = WC()->cart->generate_cart_id( $product_id );
					$in_cart_key = WC()->cart->find_product_in_cart( $cart_id );
					if ( $in_cart_key && isset( WC()->cart->cart_contents[ $in_cart_key ] ) ) {
						$qty_in_cart = WC()->cart->cart_contents[ $in_cart_key ]['quantity'];
					}
				}
				?>

				<div class="related-product-card">
					<!-- Wishlist Button -->
					<?php if ( $add_to_wishlist_url ) : ?>
						<div class="yith-wcwl-add-button">
							<a href="<?php echo esc_url( $add_to_wishlist_url ); ?>" class="add_to_wishlist product-wishlist product-wishlist-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-product-type="<?php echo esc_attr( $product_obj ? $product_obj->get_type() : '' ); ?>" data-original-product-id="<?php echo esc_attr( $product_obj ? $product_obj->get_parent_id() : '' ); ?>" data-title="<?php echo esc_attr( 'Add to wishlist' ); ?>" rel="nofollow">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</a>
						</div>
					<?php else : ?>
						<button class="product-wishlist product-wishlist-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>" type="button">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</button>
					<?php endif; ?>

					<!-- Sale Badge -->
					<?php if ( $is_on_sale ) : ?>
						<span class="slider-product-badge">-<?php echo esc_html( $product_obj->get_sale_price() ? round( ( ( $product_obj->get_regular_price() - $product_obj->get_sale_price() ) / $product_obj->get_regular_price() ) * 100 ) : '0' ); ?>%</span>
					<?php endif; ?>

					<!-- Product Image -->
					<a href="<?php echo esc_url( $product_link ); ?>" class="product-image-link">
						<div class="product-image">
							<?php if ( $product_image ) : ?>
								<?php echo $product_image; ?>
							<?php else : ?>
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr( $product_title ); ?>">
							<?php endif; ?>
						</div>
					</a>

					<!-- Product Info -->
					<div class="product-info">
						<!-- Rating -->
						<?php if ( $product_rating > 0 ) : ?>
							<div class="product-rating">
								<div class="stars">
									<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
										<span class="star <?php echo $i <= $product_rating ? 'filled' : ''; ?>">★</span>
									<?php endfor; ?>
								</div>
								<span class="rating-value"><?php echo esc_html( number_format( $product_rating, 2 ) ); ?></span>
							</div>
						<?php endif; ?>

						<!-- Title -->
						<h3 class="product-title"><a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_title ); ?></a></h3>

						<!-- Price -->
						<div class="product-price"><?php echo $product_price; ?></div>
						<?php
						$product_sold = (int) ( $product_obj ? $product_obj->get_total_sales() : 0 );
						$manages_stock = ( $product_obj && method_exists( $product_obj, 'managing_stock' ) ) ? $product_obj->managing_stock() : ( $product_obj ? (bool) $product_obj->get_manage_stock() : false );
						$available_count = ( $manages_stock && $product_stock !== null ) ? intval( $product_stock ) : null;
						$remaining_display = $available_count !== null ? $available_count : '∞';
						$show_bar = ( $available_count !== null );
						if ( $show_bar ) {
							$available_percent = max( 0, min( 100, intval( $available_count ) ) );
							$sold_percent = 0;
						}
						?>

						<div class="product-progress" aria-hidden="false">
							<?php if ( $available_count !== null ) : ?>
								<div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( isset( $available_percent ) ? $available_percent : 0 ); ?>" data-sold="<?php echo esc_attr( $product_sold ); ?>" data-available="<?php echo esc_attr( $available_count ); ?>">
									<div class="progress-sold" style="width:<?php echo esc_attr( isset( $sold_percent ) ? $sold_percent : 0 ); ?>%"></div>
									<div class="progress-available" style="width:<?php echo esc_attr( isset( $available_percent ) ? $available_percent : 0 ); ?>%"></div>
								</div>
							<?php else : ?>
								<div class="progress-track unknown" role="progressbar" data-sold="<?php echo esc_attr( $product_sold ); ?>" data-available="∞">
									<div class="progress-indicator" aria-hidden="true"></div>
								</div>
							<?php endif; ?>

							<div class="progress-meta">
								<span class="available">متوفر: <?php echo esc_html( $remaining_display ); ?></span>
								<span class="sold">مباع: <?php echo esc_html( $product_sold ); ?></span>
							</div>
						</div>

						<!-- Add to Cart Button -->
						<div class="product-actions add-to-cart-wrapper" data-product_id="<?php echo esc_attr( $product_id ); ?>">
							<?php
							if ( $is_in_stock ) :
								// Quantity Control
								?>
								<div class="grid-qty-control" style="display: <?php echo ( $qty_in_cart > 0 ) ? 'flex' : 'none'; ?>; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 100%; justify-content: space-between;">
									<?php if ( $qty_in_cart == 1 ) : ?>
										<button class="grid-qty-btn grid-minus trash-mode" type="button" style="background:#fff0f0; border:none; padding:8px 15px; cursor:pointer; flex: 1; color: #ef4444;">
											<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
										</button>
									<?php else : ?>
										<button class="grid-qty-btn grid-minus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">-</button>
									<?php endif; ?>
									<input type="number" class="grid-qty-val" value="<?php echo ( $qty_in_cart > 0 ) ? intval( $qty_in_cart ) : 1; ?>" min="1" style="width:50px; text-align:center; border:none; -moz-appearance:textfield;" readonly>
									<button class="grid-qty-btn grid-plus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">+</button>
								</div>

								<!-- Add to Cart Button (Visible if NOT in cart) -->
								<a href="<?php echo esc_url( $product_obj ? $product_obj->add_to_cart_url() : '#'); ?>" class="add-to-cart-btn product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-btn-init" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-quantity="1" rel="nofollow" style="display: <?php echo ( $qty_in_cart > 0 ) ? 'none' : 'block'; ?>;">أضف للسلة</a>
							<?php else : ?>
								<button class="add-to-cart-btn disabled" disabled>غير متوفر</button>
							<?php endif; ?>
						</div>
					</div>
				</div>

			<?php

			endforeach; ?>

			<?php
			// Restore the add_to_cart and product_link_close hooks for other contexts
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			?>

		</ul>

	</section>
	<?php
endif;

wp_reset_postdata();
