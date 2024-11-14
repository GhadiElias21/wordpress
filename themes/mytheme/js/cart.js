class ShoppingCart {
    constructor() {
        this.cart = this.loadCart() || [];
        this.updateCartCount();
        this.updateCartTotal();
        this.renderCartItems();
    }

    loadCart() {
        const cartData = this.getCookie('shoppingCart');
        return cartData ? JSON.parse(cartData) : [];
    }
    saveCart() {
        this.setCookie('shoppingCart', JSON.stringify(this.cart), 7);
    }
    setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${(value || '')}${expires}; path=/`;
    }

    getCookie(name) {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    addItem(product) {
        const productSlug = product.slug || product.link.split('/').filter(Boolean).pop();
        product.slug = productSlug;


        jQuery.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'add_to_cart',
                product: product
            },
            success: (response) => {
                if (response.success) {
                    const existingProduct = this.cart.find(item => item.slug === product.slug);
                    if (existingProduct) {
                        existingProduct.quantity += 1;
                    } else {
                        product.quantity = 1;
                        this.cart.push(product);
                    }
                    this.updateCart();
                } else {
                    const errorMessage = JSON.stringify(response.data, null, 2);
                    alert('Failed to add item to cart: ' + errorMessage);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error adding item to cart:', status, error);
                alert('An error occurred while adding the item to the cart. Please try again later.');
            }
        });
    }
    changeQuantity(productId, quantity) {
        if (quantity < 1) {
            this.removeItem(productId);
        } else {
            jQuery.ajax({
                url: ajax_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_cart',
                    productId: productId,
                    quantity: quantity
                },
                success: (response) => {
                    if (response.success) {
                        const product = this.cart.find(item => item.id === productId);
                        if (product) {
                            product.quantity = quantity;
                        }
                        this.updateCart();
                    } else {
                        alert('Failed to change the quantity: ' + response.data);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error while changing the item quantity:', status, error);
                    alert('An error occurred while updating the item quantity. Please try again later.');
                }
            });
        }
    }

    removeItem(productId) {
        jQuery.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'remove_from_cart',
                productId: productId
            },
            success: (response) => {
                if (response.success) {
                    this.cart = this.cart.filter(item => item.id !== productId);
                    this.updateCart();
                } else {
                    alert('Failed to remove the item: ' + response.data);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error while removing an item:', status, error);
                alert('An error occurred while removing an item. Please try again later.');
            }
        });
    }

    calculateTotal() {
        let exchangeRate = ajax_obj.exchangeRate || 1
        return this.cart.reduce((total, item) => total + (item.price * item.quantity * exchangeRate), 0).toFixed(2);
    }

    getItemCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    }

    updateCart() {
        this.saveCart();
        this.updateCartCount();
        this.updateCartTotal();
        this.renderCartItems();
    }

    deleteCookie(name) {
        this.setCookie(name, '', -1);
    }

    clearCart() {
        this.cart = [];
        this.deleteCookie('shoppingCart');
        this.updateCart();
        jQuery('#cartModal').modal('hide');
    }

    updateCartCount() {
        const count = this.getItemCount();
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.classList.toggle('badge-danger', count === 0);
            cartCount.classList.toggle('badge-success', count > 0);
        }
    }

    updateCartTotal() {
        const currencySymbol =ajax_obj.selectedCurrency === 'BYN' ? ' byn' : '$'; 

        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cart-total');
        if (cartTotal) {
            cartTotal.innerText = `${total}${currencySymbol}`;
        } else {
        }
    }

    renderCartItems() {
        let exchangeRate = ajax_obj.exchangeRate || 1
         const currencySymbol =ajax_obj.selectedCurrency === 'BYN' ? ' byn' : '$'; 
        const cartItems = document.getElementById('cart-items');
        if (!cartItems) {
            return;
        }

        cartItems.innerHTML = '';
        if (this.cart.length === 0) {
            cartItems.innerHTML = `<li class="list-group-item">${ajax_obj.translations.empty_cart}</li>`;
            return;
        }
        this.cart.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `
                <div class="cart-item d-flex align-items-center p-3 border rounded shadow-sm mb-2 bg-light w-100">
                    <a href="${item.link}"><img src="${item.image}" alt="${item.name}" class="img-fluid rounded" style="width: 70px; height: 70px; margin-right: 15px; border-radius: 5px;"/></a> 
                    <span class="flex-grow-1">${item.name} x${item.quantity} - ${(item.price * item.quantity *exchangeRate).toFixed(2)}${currencySymbol}</span>
                    <div class="btn-group ml-auto" role="group" aria-label="Change quantity of ${item.name}">
                        <button class="btn btn-sm btn-outline-secondary change-quantity" data-id="${item.id}" data-action="decrease" aria-label="Decrease quantity of ${item.name}">-</button>
                        <button class="btn btn-sm btn-outline-secondary change-quantity" data-id="${item.id}" data-action="increase" aria-label="Increase quantity of ${item.name}">+</button>
                        <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}" aria-label="Remove ${item.name} from cart">&times;</button>
                    </div>
                </div>
            `;
            cartItems.appendChild(li);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const cart = new ShoppingCart();


        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productImage = this.getAttribute('data-image');
                const productPrice = parseFloat(this.getAttribute('data-price'));
                const productLink = this.getAttribute('data-link');
                const product = {
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    image: productImage,
                    link: productLink
                };
                cart.addItem(product);
            });
        });

        const cartItemsElement = document.getElementById('cart-items');
        if (cartItemsElement) {
            cartItemsElement.addEventListener('click', function (event) {
                const target = event.target;

                if (target.classList.contains('change-quantity')) {
                    const productId = target.getAttribute('data-id');
                    const action = target.getAttribute('data-action');

                    if (action === 'increase') {
                        cart.changeQuantity(productId, cart.cart.find(item => item.id === productId).quantity + 1);
                    } else if (action === 'decrease') {
                        cart.changeQuantity(productId, cart.cart.find(item => item.id === productId).quantity - 1);
                    }
                }

                if (target.classList.contains('remove-item')) {
                    const productId = target.getAttribute('data-id');
                    cart.removeItem(productId);
                }
            });
        } else {
            console.log("Element with ID 'cart-items' not found.");
        }


        const placeOrderButton = document.getElementById('place-order-button');
        if (placeOrderButton) {
            placeOrderButton.addEventListener('click', function () {
                if (cart.cart.length === 0) {

                    alert(ajax_obj.translations.empty_cart)
                    jQuery('#cartModal').modal('hide');

                    return;
                }

                if (!ajax_obj.current_user.display_name) {

                    jQuery('#cartModal').modal('hide');

                    jQuery('#auth-modal').fadeIn(100)


                    return

                }
                const currentUser = ajax_obj.current_user;

                const confirmationDetails = document.getElementById('confirmation-details');
                confirmationDetails.innerHTML = `
                <strong>Name:</strong> ${currentUser.display_name}<br>
                <strong>Email:</strong> ${currentUser.user_email}<br>
                <strong>Products:</strong><br>
                ${cart.cart.map(item => `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`).join('<br>')}
            `;

                document.getElementById('products').value = JSON.stringify(cart.cart);

                jQuery('#userDetailsModal').modal('show');
            });
        } else {
            console.log("Element with ID 'place-order-button' not found.");
        }

        const confirmOrderButton = document.getElementById('confirm-order-button');
        if (confirmOrderButton) {
            confirmOrderButton.addEventListener('click', function () {

                if (!ajax_obj.current_user.display_name) {
                    alert('you should be logged in before placing an order');
                    return
                }

                let products = JSON.parse(document.getElementById('products').value);
                let totalAmount = products.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);

                const currentUser = ajax_obj.current_user;

                jQuery.ajax({
                    url: ajax_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'create_order',
                        full_name: currentUser.display_name,
                        email: currentUser.user_email,
                        products: JSON.stringify(products),
                        total_amount: totalAmount,
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log('AJAX request successful', response);
                            cart.clearCart();
                            alert('Order placed successfully!');
                            jQuery('#userDetailsModal').modal('hide');
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error while placing the order:', status, error);
                        alert('An error occurred while placing the order. Please try again later.');
                    }
                });
            });

        } else {
            console.log("Element with ID 'confirm-order-button' not found.");
        }
    }
)
;
