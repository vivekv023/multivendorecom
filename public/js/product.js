// Product Detail Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    const quantityInput = document.getElementById('quantity');
    const maxStock = parseInt(quantityInput?.dataset?.maxStock || 10);
    
    if (decreaseBtn && increaseBtn && quantityInput) {
        decreaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });

        increaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value < maxStock) {
                quantityInput.value = value + 1;
            }
        });
    }
    
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const imgSrc = this.querySelector('img').src;
            const mainImage = document.getElementById('mainImage');
            if (mainImage) {
                mainImage.src = imgSrc;
            }
        });
    });
});

async function addToCart(productId, quantity, addUrl, csrfToken) {
    const addToCartBtn = document.getElementById('addToCartBtn');
    addToCartBtn.disabled = true;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    try {
        const response = await fetch(addUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: parseInt(quantity)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await updateCartCount();
            Swal.fire({
                icon: 'success',
                title: 'Added to cart!',
                text: quantity + ' item(s) added to your cart.',
                showConfirmButton: false,
                timer: 1500,
                background: '#fff',
                iconColor: '#10b981'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                background: '#fff',
                iconColor: '#ef4444'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong',
            background: '#fff',
            iconColor: '#ef4444'
        });
    } finally {
        addToCartBtn.disabled = false;
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
    }
}

function handleBuyNow(productId, quantity, addUrl, csrfToken) {
    addToCart(productId, quantity, addUrl, csrfToken).then(() => {
        window.location.href = '/cart';
    });
}
