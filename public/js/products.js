// Product Listing JavaScript

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const btn = this;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';
            
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await updateCartCount();
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to cart!',
                        text: 'Product has been added to your cart.',
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
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cart-plus me-1"></i>Add to Cart';
            }
        });
    });
});
