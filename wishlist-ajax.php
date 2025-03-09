<?php
if (!defined('ABSPATH')) exit; // ✅ אבטחה – מונע גישה ישירה

// ✅ פונקציה להוספה / הסרה מהווישליסט עם AJAX
function mas_toggle_wishlist() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'User not logged in']);
        wp_die();
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $table_name = $wpdb->prefix . 'wishlist';

    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
        wp_die();
    }

    // ✅ בדיקה אם המוצר כבר בווישליסט
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND product_id = %d",
        $user_id,
        $product_id
    ));

    if ($exists) {
        $wpdb->delete($table_name, ['user_id' => $user_id, 'product_id' => $product_id]);
        $action = 'removed';
    } else {
        $wpdb->insert($table_name, ['user_id' => $user_id, 'product_id' => $product_id]);
        $action = 'added';
    }

    // ✅ מחשב מחדש את מספר הפריטים בווישליסט
    $count = mas_get_wishlist_count_from_db($user_id);

    wp_send_json_success(['action' => $action, 'count' => $count]);
}
add_action('wp_ajax_mas_toggle_wishlist', 'mas_toggle_wishlist');
add_action('wp_ajax_nopriv_mas_toggle_wishlist', 'mas_toggle_wishlist'); // למשתמשים לא מחוברים

// ✅ פונקציה לניקוי כל הווישליסט
function mas_clear_wishlist_ajax() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'User not logged in']);
        wp_die();
    }

    mas_clear_wishlist(get_current_user_id());
    wp_send_json_success(['message' => 'Wishlist cleared', 'count' => 0]);
}
add_action('wp_ajax_mas_clear_wishlist', 'mas_clear_wishlist_ajax');
add_action('wp_ajax_nopriv_mas_clear_wishlist', 'mas_clear_wishlist_ajax'); // למשתמשים לא מחוברים

// ✅ פונקציה שמחזירה את מספר הפריטים בווישליסט
function mas_get_wishlist_count() {
    if (!is_user_logged_in()) {
        wp_send_json_success(['count' => 0]); // משתמש לא מחובר תמיד יקבל 0
        wp_die();
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'wishlist';

    $count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    wp_send_json_success(['count' => $count]);
}
add_action('wp_ajax_mas_get_wishlist_count', 'mas_get_wishlist_count');
add_action('wp_ajax_nopriv_mas_get_wishlist_count', 'mas_get_wishlist_count'); // למשתמשים לא מחוברים
?>