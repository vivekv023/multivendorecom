// Main JavaScript - Shared functions

document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        loginModal.addEventListener('shown.bs.modal', function() {
            const redirectInput = loginModal.querySelector('input[name="redirect_to"]');
            if (redirectInput && !redirectInput.value) {
                redirectInput.value = '';
            }
        });
    }
});

function openLoginModal(redirectTo = '') {
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        const redirectInput = loginModal.querySelector('input[name="redirect_to"]');
        if (redirectInput) {
            redirectInput.value = redirectTo;
        }
        const modal = new bootstrap.Modal(loginModal);
        modal.show();
    }
}

function showToast(icon, title) {
    Swal.fire({
        icon: icon,
        title: title,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

async function updateCartCount() {
    try {
        const response = await fetch('/cart/count');
        const data = await response.json();
        const badge = document.querySelector('.cart-badge');
        if (data.cart_count > 0) {
            if (badge) {
                badge.textContent = data.cart_count;
            } else {
                let icon = document.querySelector('.cart-icon a');
                if (icon) {
                    icon.innerHTML = icon.innerHTML + '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">' + data.cart_count + '</span>';
                }
            }
        } else if (badge) {
            badge.remove();
        }
    } catch (e) {}
}
