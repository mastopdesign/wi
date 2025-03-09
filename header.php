<?php
if (!defined('ABSPATH')) exit;
if (is_admin()) return;

// ✅ טעינת קבצי CSS ו-JS
function mas_enqueue_header() {
    wp_enqueue_style('header-style', get_template_directory_uri() . '/assets/css/header.css', [], '1.0.0', 'all');
    wp_enqueue_style('login-style', get_template_directory_uri() . '/assets/css/login.css', [], '1.0.0', 'all');
    wp_enqueue_script('header-script', get_template_directory_uri() . '/assets/js/header.js', ['jquery'], '1.0.0', true);

    // חישוב מספר הפריטים בווישליסט
    $wishlist_count = 0;
    if (is_user_logged_in() && function_exists('mas_get_wishlist_count_from_db')) {
        $wishlist_count = mas_get_wishlist_count_from_db(get_current_user_id());
    } elseif (isset($_COOKIE['guest_wishlist'])) {
        $wishlist_count = count(json_decode(stripslashes($_COOKIE['guest_wishlist']), true));
    }

    // שליחת הנתונים ל-JS
    wp_localize_script('header-script', 'wp_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'wishlist_count' => $wishlist_count,
        'is_logged_in' => is_user_logged_in(),
    ]);
}
add_action('wp_enqueue_scripts', 'mas_enqueue_header', 1);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;500;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php 
require_once get_template_directory() . '/inc/auth/facebook-login.php';
require_once get_template_directory() . '/inc/auth/google-login.php';
require_once get_template_directory() . '/inc/header/includes/function.php';
?>
<header class="MAS_header">
    <div class="header-top">
        <div class="header--top mobile">
            <div class="header-slider mobile">
                <button id="next-slide" class="slider-btn right-btn" role="button" aria-label="Next Slide">›</button>
                <div class="slider-container">
                    <div id="slider-texts" class="slider-texts">
                        <div class="slider-item">SALE 2025 New Year</div>
                        <div class="slider-item">משלוחים חינם מעל 300₪</div>
                        <div class="slider-item">מבצעי חורף חמים</div>
                        <div class="slider-item">SALE 2025 New Year</div>
                        <div class="slider-item">משלוחים חינם מעל 300₪</div>
                        <div class="slider-item">מבצעי חורף חמים</div>
                    </div>
                </div>
                <button id="prev-slide" class="slider-btn left-btn" role="button" aria-label="Previous Slide">‹</button>
            </div>
            <div class="header-left-top no_mobile">
                <div class="order-status">
                    <span class="material-symbols-outlined">order_approve</span>
                    <a href="<?php echo wc_get_page_permalink('myaccount'); ?>">
                        סטטוס ההזמנות שלי 
                    </a>
                </div>
                <div class="contact-link no_mobile">
                    <span class="material-symbols-outlined">phone_in_talk</span>
                    <a href="<?php echo site_url('/contact'); ?>"> יצירת קשר </a>
                </div>
            </div>
        </div>
    </div>




<div class="header-middle">
    <div class="header-middle-container">

        <div class="header-logo">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/mas-b-logo.png" alt="Logo" class="logo">
            </a>
        </div>

        <div class="header-search">
            <form action="<?php echo home_url('/'); ?>" method="get">
                <input type="text" name="s" placeholder="מה אתם מחפשים?" />
                <button type="submit">חיפוש</button>
            </form>
        </div>

        <div class="header-icons">
            
           
<div class="header-icon">
    <div class="mini-cart">
        <a href="#" class="cart-icon" id="open-cart">
            <span class="material-symbols-outlined">shopping_cart</span>
            <span class="cart-count" id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        </a>
    </div>
</div>
<?php get_template_part('inc/mini-cart/mini-cart'); ?> 


            <?php get_template_part('inc/login-popup'); ?>
            <div class="login-popup">
                <a href="#" id="login-btn">
                    <span class="material-symbols-outlined">account_circle</span>
                </a>
            </div>



                <!-- ❤️ Wishlist -->

    <div class="mas-wish-wrapper">
        <div class="mas-wish">
            <a href="<?php echo esc_url(home_url('/wishlist/')); ?>" id="mas-wish-btn" class="mas-wish-link">
                <span class="material-symbols-outlined mas-wishlist">favorite</span>
<span id="mas-wish-count" class="mas-wish-count">
    <?php echo function_exists('mas_get_wishlist_count_direct') ? mas_get_wishlist_count_direct() : 0; ?>
</span>
            </a>
        </div>
    </div>
            </div>
        </div>
    </div>







<div class="header-bottom">
    <div class="mas-header-bottom-right">

        <div class="header-nav">
            
            <?php get_template_part('/inc/mega-menu/mega-menu'); ?>

            <button class="menu-right">חדשים                     <span></span>                                                       </button>
            <button class="menu-right">קולקציות                  <span class="material-symbols-outlined">star</span>                 </button>
            <button class="menu-right">SALE                      <span class="material-symbols-outlined">shoppingmode</span>         </button>
            <button class="menu-right">עשה בעצמך                 <span class="material-symbols-outlined">handyman</span>             </button> 
            <button class="menu-right">השראות וייעוץ             <span class="material-symbols-outlined">emoji_objects</span>        </button>
        </div>

    
        <div class="header--nav">
            <button class="menu-meet">בלוג                      <span class="material-symbols-outlined">clarify</span>              </button>
            <button class="menu-meet">תיאום פגישה               <span class="material-symbols-outlined">calendar_month</span>       </button>
        </div>

    </div>
        </div>







<div class="header-middle-mobile">
    <div class="header-middle-container-mobile">

        <div class="header-mobile right-mobile">
            
<div id="custom-mobile-nav-overlay" class="custom-mobile-nav-overlay"></div> 

<div id="custom-mobile-nav" class="custom-mobile-nav-container">
            <?php get_template_part('/inc/mega-menu/mega-menu-mobile'); ?>
        </div>
<button id="custom-mobile-nav-toggle" class="custom-mobile-nav-icon">☰</button>
        
        
        <div class="header-search-mobile">
           <span class="material-symbols-outlined">search</span>
            </form>
        </div>

  </div>

        <div class="header-mobile logo-mobile">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/mas-b-logo.png" alt="Logo" class="logo-mobile">
            </a>
        </div>
        
        <div class="header-mobile left-mobile">
            
<!-- ✅ Wishlist בהדר -->


            <?php get_template_part('inc/mini-cart/mini-cart'); ?>
            <div class="header-icon">
                <div class="mini-cart">
                    <a href="#" class="cart-icon" id="open-cart">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    </a>
                </div>
            </div>



        </div>
    </div>
</div>



</header>



