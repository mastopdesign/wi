<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table_name = $wpdb->prefix . 'wishlist';

// ✅ פונקציה ליצירת הטבלה במסד הנתונים (אם לא קיימת)
function mas_create_wishlist_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) NOT NULL,
            product_id BIGINT(20) NOT NULL,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_wishlist (user_id, product_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// ✅ יצירת הטבלה כאשר האתר נטען
add_action('init', 'mas_create_wishlist_table');

// ✅ פונקציות לניהול הנתונים בווישליסט
function mas_add_to_wishlist($user_id, $product_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    return $wpdb->insert($table_name, ['user_id' => $user_id, 'product_id' => $product_id]);
}

function mas_remove_from_wishlist($user_id, $product_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    return $wpdb->delete($table_name, ['user_id' => $user_id, 'product_id' => $product_id]);
}

function mas_get_wishlist($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    return $wpdb->get_col($wpdb->prepare("SELECT product_id FROM $table_name WHERE user_id = %d", $user_id));
}

// ✅ פונקציה לניקוי כל הווישליסט של משתמש
function mas_clear_wishlist($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    return $wpdb->delete($table_name, ['user_id' => $user_id]);
}

// ✅ פונקציה שמחזירה את מספר הפריטים בווישליסט ישירות
function mas_get_wishlist_count_direct() {
    if (!is_user_logged_in()) {
        return 0; // משתמש לא מחובר תמיד יקבל 0
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';

    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        get_current_user_id()
    ));
}

// ✅ פונקציה שמחזירה את מספר הפריטים בווישליסט מהמסד
function mas_get_wishlist_count_from_db($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishlist';
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ));
}
?>