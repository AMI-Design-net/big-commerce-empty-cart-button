document.addEventListener("DOMContentLoaded", function() {

    // === Empty cart function ===
    function emptyCart() {
        // Confirmation prompt
        if (!confirm("Are you sure you want to empty your cart?")) {
            return;
        }
        // First, get the live cart from Storefront API
        fetch("/api/storefront/cart", {
            method: "GET",
            credentials: "include"
        })
        .then(res => res.json())
        .then(carts => {
            if (!carts || carts.length === 0) {
                alert("No active cart found.");
                return;
            }

            var cartId = carts[0].id; // always take the first cart
            console.log("Cart ID:", cartId);

            // Send to your PHP endpoint
            return fetch("https://www.yoursite.com/empty-cart.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(cartId)
            });
        })
        .then(response => response ? response.json() : null)
        .then(data => {
            if (!data) return; // already handled missing cart

            if (data.success) {
                location.reload(); // reload cart page
            } else {
                alert("Failed to empty cart: " + (data.error || ""));
                console.error("Empty cart error:", data);
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            alert("Error contacting server.");
        });
    }

    // === Add button to cart page ===
    var cartActions = document.querySelector(".cart-actions") || document.querySelector("#cart-form");
    if (cartActions) {
        var emptyBtn = document.createElement("button");
        emptyBtn.textContent = "Empty Cart";
        emptyBtn.type = "button";
        emptyBtn.className = "button button--secondary";
        emptyBtn.style.marginLeft = "10px";
        emptyBtn.style.marginRight = "10px";
        emptyBtn.addEventListener("click", emptyCart);
        cartActions.appendChild(emptyBtn);
    }
});