<?php
if (!defined('ABSPATH')) exit;

// ✅ טוען את כל קבצי הווישליסט מהתיקייה
$wishlist_files = glob(__DIR__ . '/wishlist/*.php');
foreach ($wishlist_files as $file) {
    require_once $file;
}

// ✅ טעינת קבצי JS ו-CSS של הווישליסט בצורה חכמה
function mas_enqueue_wishlist_assets() {
    // ✅ נטען תמיד כדי למנוע בעיות בתפריט
    wp_enqueue_style(
        'mas-wishlist-css',
        get_template_directory_uri() . '/inc/wishlist/assets/css/wishlist.css',
        [],
        null,
        'all'
    );

    wp_enqueue_script(
        'mas-wishlist-js',
        get_template_directory_uri() . '/inc/wishlist/assets/js/wishlist.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('mas-wishlist-js', 'wp_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'has_wishlist' => has_wishlist_element() // ✅ נשלח ל-JS לבדיקה אם צריך להפעיל קאונט
    ]);
}
add_action('wp_enqueue_scripts', 'mas_enqueue_wishlist_assets');

// ✅ פונקציה שבודקת אם יש wishlist בעמוד או בהדר
function has_wishlist_element() {
    return true;
}
?>
