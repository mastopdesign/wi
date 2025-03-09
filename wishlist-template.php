<?php
/* Template Name: Wishlist */
if (!defined('ABSPATH')) exit;

get_header();

// בדיקה אם המשתמש מחובר
$is_logged_in = is_user_logged_in();
$wishlist_items = [];

if ($is_logged_in) {
    $wishlist_items = mas_get_wishlist(get_current_user_id());
}
?>

<h1 class="mas-wishlist-title">רשימת המשאלות שלי</h1>

<?php if ($is_logged_in): ?>
    <?php if (!empty($wishlist_items)): ?>
        <button id="mas-clear-wishlist" class="mas-wishlist-clear">🗑️ נקה רשימה</button>
        <div id="mas-wishlist-container">
            <?php foreach ($wishlist_items as $product_id):
                $product = wc_get_product($product_id);
                if (!$product) continue;

                $image = $product->get_image(); 
                $product_price = $product->get_price();
                $product_regular_price = $product->get_regular_price();

                if ($product->is_type('variation')) {
                    $parent_product = wc_get_product($product->get_parent_id());
                    $product_regular_price = $product_regular_price ?: $parent_product->get_regular_price();
                }

                if (empty($product_regular_price) || $product_regular_price <= $product_price) {
                    $product_regular_price = round($product_price * 1.3);
                }

                $description = wp_trim_words($product->get_short_description(), 15, '...');
            ?>
                <div class="mas-wishlist-item" data-id="<?php echo esc_attr($product_id); ?>">
                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="mas-wishlist-link">
                        <div class="mas-wishlist-image-container">
                            <?php echo $image; ?>
                        </div>
                        <div class="mas-wishlist-details">
                            <div class="mas-wishlist-name-item"><?php echo $product->get_name(); ?></div>
                            <div class="mas-wishlist-description"><?php echo esc_html($description); ?></div>
                            <div class="mas-wishlist-price-container">
                                <?php if ($product_regular_price > $product_price): ?>
                                    <span class="mas-wishlist-regular-price"><del><?php echo wc_price($product_regular_price); ?></del></span>
                                <?php endif; ?>
                                <span class="mas-wishlist-sale-price"><?php echo wc_price($product_price); ?></span>
                            </div>
                        </div>
                    </a>
                    <button class="mas-wishlist-remove" data-id="<?php echo esc_attr($product_id); ?>">❌</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="mas-wishlist-empty">הרשימה שלך ריקה.</p>
    <?php endif; ?>
<?php else: ?>
    <p class="mas-wishlist-guest-message">עליך להיות מחובר כדי לשמור מוצרים בווישליסט.</p>
    <div id="mas-wishlist-container-guest">
        <p class="mas-wishlist-loading">טוען רשימה...</p>
    </div>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function removeFromWishlist(productId) {
        fetch(wp_ajax.ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ action: "mas_remove_from_wishlist", product_id: productId }),
        })
        .then(res => res.json())
        .then((data) => {
            if (data.success) {
                document.querySelector(`.mas-wishlist-item[data-id='${productId}']`).remove();
                if (document.querySelectorAll(".mas-wishlist-item").length === 0) {
                    document.getElementById("mas-wishlist-container").innerHTML = "<p class='mas-wishlist-empty'>הרשימה שלך ריקה.</p>";
                }
            }
        })
        .catch((error) => console.error("❌ Wishlist remove error:", error));
    }

    document.querySelectorAll(".mas-wishlist-remove").forEach((button) => {
        button.addEventListener("click", function () {
            let productId = this.dataset.id;
            removeFromWishlist(productId);
        });
    });

    const clearBtn = document.getElementById("mas-clear-wishlist");
    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            fetch(wp_ajax.ajaxurl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "mas_clear_wishlist" }),
            })
            .then(res => res.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById("mas-wishlist-container").innerHTML = "<p class='mas-wishlist-empty'>הרשימה שלך ריקה.</p>";
                }
            })
            .catch((error) => console.error("❌ Wishlist clear error:", error));
        });
    }
});
</script>

<?php get_footer(); ?>








<style>
  .mas-wishlist-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 200px;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    background: #f9f9f9;
    border-radius: 10px;
    margin-bottom: 10px;
}

.mas-wishlist-link {
    display: flex;
    align-items: center;
    width: 100%;
    text-decoration: none;
    color: #333;
}

.mas-wishlist-image-container {
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    overflow: hidden;
    margin-right: 15px;
    background: white;
}

.mas-wishlist-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mas-wishlist-details {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.mas-wishlist-details h3 {
    font-size: 18px;
    margin: 5px 0;
}

.mas-wishlist-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.mas-wishlist-price-container {
    display: flex;
    align-items: center;
}

.mas-wishlist-regular-price {
    text-decoration: line-through;
    color: #888;
    margin-right: 8px;
}

.mas-wishlist-sale-price {
    color: red;
    font-weight: bold;
}

.mas-wishlist-remove-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.mas-wishlist-remove {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: red;
    padding: 10px;
}  
</style>
