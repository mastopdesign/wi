<?php
if (!defined('ABSPATH')) exit;

function mas_wishlist_button_shortcode($atts) {
    // הגדרת פרמטרים ברירת מחדל עבור ה-Shortcode
    $atts = shortcode_atts(['id' => ''], $atts);
    $product_id = intval($atts['id']);
    if (!$product_id) return ''; // אם אין מזהה מוצר, אין להציג את הכפתור

    // בדיקה אם המשתמש מחובר
    $is_logged_in = is_user_logged_in();
    $user_id = $is_logged_in ? get_current_user_id() : 0;

    // בדיקת קיום המוצר בווישליסט
    $wishlist = $is_logged_in ? mas_get_wishlist($user_id) : [];
    $is_in_wishlist = in_array($product_id, $wishlist); // אם המוצר נמצא בווישליסט

    // הגדרת הנתונים לכפתור
    $icon = $is_in_wishlist ? 'heart_check' : 'heart_plus'; // אייקון שונה אם המוצר בווישליסט או לא
    $button_class = $is_in_wishlist ? 'mas-remove-wishlist' : 'mas-add-wishlist'; // קלאס שונה להוספה/הסרה
    $action = 'mas_toggle_wishlist'; // הפעולה שתתבצע

    // הפעלת תבנית HTML עבור כפתור הווישליסט
    ob_start(); ?>
    <button class="mas-wish-btn <?php echo esc_attr($button_class); ?>" 
            data-id="<?php echo esc_attr($product_id); ?>"
            data-action="<?php echo esc_attr($action); ?>"
            aria-label="Add to wishlist">
        <span class="material-symbols-outlined"><?php echo esc_html($icon); ?></span>
    </button>
    <?php 
    return ob_get_clean(); // מחזיר את התוצאה שנוצרה
}

add_shortcode('mas_wishlist_button', 'mas_wishlist_button_shortcode');