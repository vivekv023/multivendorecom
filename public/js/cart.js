// Cart Page JavaScript

async function updateQuantity(itemId, change) {
    const input = document.querySelector(`input[data-item-id="${itemId}"]`);
    let newQty = parseInt(input.value) + change;
    
    if (newQty < 1) newQty = 1;
    if (newQty > parseInt(input.max)) newQty = parseInt(input.max);

    try {
        const response = await fetch(`/cart/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ quantity: newQty })
        });

        const data = await response.json();

        if (data.success) {
            input.value = newQty;
            await updateCartCount();
            location.reload();
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
    }
}

async function updateQuantityFromInput(input) {
    let newQty = parseInt(input.value);
    
    if (newQty < 1) newQty = 1;
    if (newQty > parseInt(input.max)) newQty = parseInt(input.max);
    input.value = newQty;

    const itemId = input.dataset.itemId;
    await updateQuantity(itemId, 0);
}

async function removeItem(itemId) {
    const result = await Swal.fire({
        title: 'Remove Item',
        text: 'Are you sure you want to remove this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        confirmButtonColor: '#ef4444'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                await updateCartCount();
                location.reload();
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
        }
    }
}

async function removeGuestItem(productId) {
    const result = await Swal.fire({
        title: 'Remove Item',
        text: 'Are you sure you want to remove this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        confirmButtonColor: '#ef4444'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/cart/product/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                await updateCartCount();
                location.reload();
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
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-bs-target="#loginModal"]').forEach(button => {
        button.addEventListener('click', function() {
            openLoginModal('checkout');
        });
    });
});
