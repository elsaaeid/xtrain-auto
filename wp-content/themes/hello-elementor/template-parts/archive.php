<?php
/**
 * The template for displaying archive pages.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if ( is_front_page() || is_home() ) : ?>
<section class="hero-section">
    <div class="hero-bg">
        <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?q=80&w=2000&auto=format&fit=crop" alt="Background">
        <div class="overlay"></div>
    </div>
    
    <div class="hero-container">
        <!-- Content Side (Text) -->
        <div class="hero-content">
            <h4 class="hero-subtitle">إكس ترين أوتو</h4>
            <h1 class="hero-title">كفاءة عالية لقطع <br> غيار سيارتك</h1>
            <p class="hero-desc">
                هنالك العديد من الأنواع المتوفرة لنصوص لوريم إيبسوم، ولكن الغالبية <br>
                تم تعديلها بشكل ما عبر إدخال بعض النوادر
            </p>
            <a href="#" class="hero-btn">تسوق الآن</a>
        </div>

        <!-- Form Side -->
        <div class="hero-form-card">
            <h2 class="form-title">دعنا نجد قطع غيارك أسرع</h2>
            <p class="form-subtitle">بحث سريع عن قطعة غيار سيارتك</p>
            
            <form class="parts-search-form" action="/" method="get">
                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="step-badge">01</span>
                        <input type="text" name="part_type" placeholder="نوع القطعة">
                        <i class="fa-solid fa-gear input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="step-badge">02</span>
                        <input type="text" name="part_location" placeholder="مكان القطعة">
                        <i class="fa-solid fa-location-dot input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="step-badge">03</span>
                        <input type="text" name="production_year" placeholder="سنة الإنتاج">
                        <i class="fa-solid fa-calendar-days input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="step-badge">04</span>
                        <input type="text" name="engine" placeholder="المحرك">
                        <i class="fa-solid fa-car-battery input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="submit-btn">بحث سريع</button>
            </form>
        </div>
    </div>
</section>

<style>
.hero-section {
    position: relative;
    width: 100%;
    min-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #fff;
    direction: rtl;
    padding: 60px 20px;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-bg .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(240,240,240,0.95) 0%, rgba(240,240,240,0.7) 50%, rgba(240,240,240,0.1) 100%);
}

.hero-container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
    flex-direction: row-reverse;
}

.hero-content {
    flex: 1;
    min-width: 300px;
    text-align: right;
    color: #333;
}

.hero-subtitle {
    font-size: 14px;
    color: #f47b32;
    margin-bottom: 10px;
    font-weight: 700;
}

.hero-title {
    font-size: 48px;
    line-height: 1.2;
    font-weight: 800;
    color: #000;
    margin-bottom: 20px;
}

.hero-desc {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
    max-width: 500px;
}

.hero-btn {
    display: inline-block;
    padding: 12px 32px;
    background-color: #f47b32;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    border-radius: 6px;
    transition: background 0.3s;
}

.hero-btn:hover {
    background-color: #d66a28;
    color: #fff;
}

.hero-form-card {
    width: 380px;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    color: #333;
}

.form-title {
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 8px;
    text-align: center;
    color: #000;
}

.form-subtitle {
    font-size: 13px;
    color: #888;
    margin-bottom: 25px;
    text-align: center;
}

.parts-search-form .form-group {
    margin-bottom: 15px;
}

.parts-search-form .input-wrapper {
    position: relative;
    border: 1px solid #eee;
    border-radius: 8px;
    display: flex;
    align-items: center;
    background: #fff;
    padding: 0 10px;
}

.parts-search-form input {
    width: 100%;
    border: none;
    padding: 12px 10px;
    outline: none;
    font-size: 14px;
    text-align: right;
    background: transparent;
    color: #333;
}
.parts-search-form input::placeholder {
   color: #999;
}

.step-badge {
    background: #eff2f5;
    color: #777;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
    margin-left: 8px;
}

.input-icon {
    margin-right: 8px;
    color: #ccc;
    font-size: 14px;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    background-color: #f47b32;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.3s;
}

.submit-btn:hover {
    background-color: #d66a28;
}

@media (max-width: 900px) {
    .hero-container {
        flex-direction: column;
        text-align: center;
    }
    .hero-content {
        text-align: center;
        margin-bottom: 40px;
    }
    .hero-desc {
        margin-left: auto;
        margin-right: auto;
    }
    .hero-bg .overlay {
        background: rgba(255,255,255,0.85);
    }
}
</style>
<?php endif; ?>

<main id="content" class="site-main">

	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<div class="page-header">
			<?php
			the_archive_title( '<h1 class="entry-title">', '</h1>' );
			the_archive_description( '<p class="archive-description">', '</p>' );
			?>
		</div>
	<?php endif; ?>

	<div class="page-content">
		<?php
		while ( have_posts() ) {
			the_post();
			$post_link = get_permalink();
			?>
			<article class="post">
				<?php
				printf( '<h2 class="%s"><a href="%s">%s</a></h2>', 'entry-title', esc_url( $post_link ), wp_kses_post( get_the_title() ) );
				if ( has_post_thumbnail() ) {
					printf( '<a href="%s">%s</a>', esc_url( $post_link ), get_the_post_thumbnail( $post, 'large' ) );
				}
				the_excerpt();
				?>
			</article>
		<?php } ?>
	</div>

	<?php
	global $wp_query;
	if ( $wp_query->max_num_pages > 1 ) :
		$prev_arrow = is_rtl() ? '&rarr;' : '&larr;';
		$next_arrow = is_rtl() ? '&larr;' : '&rarr;';
		?>
		<nav class="pagination">
			<div class="nav-previous"><?php
				/* translators: %s: HTML entity for arrow character. */
				previous_posts_link( sprintf( esc_html__( '%s Previous', 'hello-elementor' ), sprintf( '<span class="meta-nav">%s</span>', $prev_arrow ) ) );
			?></div>
			<div class="nav-next"><?php
				/* translators: %s: HTML entity for arrow character. */
				next_posts_link( sprintf( esc_html__( 'Next %s', 'hello-elementor' ), sprintf( '<span class="meta-nav">%s</span>', $next_arrow ) ) );
			?></div>
		</nav>
	<?php endif; ?>

</main>
