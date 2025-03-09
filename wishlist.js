document.addEventListener("DOMContentLoaded", function () {
    // 🔄 פונקציה לעדכון מספר הפריטים בווישליסט
    function updateWishlistCount() {
        fetch(wp_ajax.ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ action: "mas_get_wishlist_count" }),
        })
        .then(res => res.json())
        .then((data) => {
            // עדכון מספר הפריטים בווישליסט
            document.querySelectorAll("#mas-wish-count").forEach((el) => {
                el.textContent = data.count;  
                el.style.visibility = 'visible'; // להבטיח שה-Span יהיה תמיד גלוי
                el.style.opacity = '1'; // מבטיח שהקאונט תמיד יהיה נראה
            });
        })
        .catch((error) => console.error("❌ Wishlist count update error:", error));
    }

    // 🎯 הוספה / הסרה מהווישליסט
    document.querySelectorAll(".mas-wish-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const productId = this.dataset.id;

            fetch(wp_ajax.ajaxurl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "mas_toggle_wishlist",
                    product_id: productId,
                }),
            })
            .then(res => res.json())
            .then((data) => {
                if (data.success) {
                    this.innerHTML = `<span class="material-symbols-outlined">
                        ${data.data.action === "added" ? "heart_check" : "heart_plus"}
                    </span>`;

                    // ✅ עדכון הקאונט ישירות עם התגובה מהמשרת
                    document.querySelectorAll("#mas-wish-count").forEach((el) => {
                        el.textContent = data.data.count; // עדכון הקאונט
                        el.style.visibility = 'visible'; // לוודא שהקאונט תמיד גלוי
                        el.style.opacity = '1'; // לא יוסתר
                    });
                }
            })
            .catch((error) => console.error("❌ Wishlist toggle error:", error));
        });
    });

    // 🗑️ ניקוי כל הווישליסט
    const clearWishlistBtn = document.getElementById("mas-wish-clear");
    if (clearWishlistBtn) {
        clearWishlistBtn.addEventListener("click", function () {
            fetch(wp_ajax.ajaxurl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "mas_clear_wishlist" }),
            })
            .then(res => res.json())
            .then((data) => {
                if (data.success) {
                    updateWishlistCount(); // עדכון הקאונט אחרי ניקוי הרשימה
                    document.getElementById("mas-wish-container").innerHTML = "<p>הרשימה שלך ריקה.</p>";
                }
            })
            .catch((error) => console.error("❌ Wishlist clear error:", error));
        });
    }

    // 🔄 טעינת Wishlist עבור משתמשים לא מחוברים (LocalStorage)
    function loadGuestWishlist() {
        if (!document.getElementById("mas-wish-container-guest")) return;
        let wishlist = JSON.parse(localStorage.getItem("guest_wishlist")) || [];
        let wishlistContainer = document.getElementById("mas-wish-container-guest");
        
        if (wishlist.length === 0) {
            wishlistContainer.innerHTML = "<p>הרשימה שלך ריקה.</p>";
            return;
        }

        wishlistContainer.innerHTML = "";
        wishlist.forEach((product) => {
            let item = document.createElement("div");
            item.classList.add("mas-wish-item");
            item.innerHTML = `<a href="${product.url}">${product.name}</a>
                              <button class="mas-wish-remove" data-id="${product.id}">❌</button>`;
            wishlistContainer.appendChild(item);
        });

        document.querySelectorAll(".mas-wish-remove").forEach((button) => {
            button.addEventListener("click", function () {
                let productId = this.dataset.id;
                wishlist = wishlist.filter(item => item.id !== productId);
                localStorage.setItem("guest_wishlist", JSON.stringify(wishlist));
                loadGuestWishlist();
            });
        });
    }

    // 🛠️ טעינת Wishlist עבור משתמשים לא מחוברים
    loadGuestWishlist();

    // ✅ עדכון הקאונט בעת טעינת הדף
    updateWishlistCount();
});