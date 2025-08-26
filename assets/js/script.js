// Wait until DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("script.js loaded ");

    // ===== Example: Toggle Mobile Navigation =====
    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");

    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", function () {
            navLinks.classList.toggle("active");
        });
    }

    // ===== Example: Add to Cart Buttons =====
    const addToCartButtons = document.querySelectorAll(".add-to-cart");
    const cartCount = document.querySelector("#cart-count");

    addToCartButtons.forEach(button => {
        button.addEventListener("click", function () {
            let count = parseInt(cartCount.innerText) || 0;
            cartCount.innerText = count + 1;

            alert("Item added to cart!");
        });
    });

    // ===== Example: Confirm Logout =====
    const logoutLink = document.querySelector("#logout-link");
    if (logoutLink) {
        logoutLink.addEventListener("click", function (e) {
            if (!confirm("Are you sure you want to logout?")) {
                e.preventDefault();
            }
        });
    }

    // ===== Example: Order Confirmation =====
    const placeOrderButton = document.querySelector("#place-order");
    if (placeOrderButton) {
        placeOrderButton.addEventListener("click", function () {
            alert("Your order has been placed successfully! ");
        });
    }
});
