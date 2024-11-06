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
            jQuery.ajax({
                url: ajax_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product: product
                },
                success: (response) => {
                    if (response.success) {
                        const existingProduct = this.cart.find(item => item.id === product.id);
                        if (existingProduct) {
                            existingProduct.quantity += 1;
                        } else {
                            product.quantity = 1;
                            this.cart.push(product);
                        }
                        this.updateCart();
                    } else {
                        alert('Failed to add item to cart: ' + response.data);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error adding item to cart:', status, error);
                    alert('An error occurred while adding the item to the cart. Please try again later.');
                }
            });
        }

        changeQuantity(productId, quantity) {
            // Ensure quantity is not less than zero
            if (quantity < 1) {
                // Remove the item from the cart if quantity is 0 or less
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
                        console.error('Error while changing the item quanitity:', status, error);
                        alert('An error occurred while adding the item to the cart. Please try again later.');
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
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);
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
            this.setCookie(name, '', -1); // Set the cookie with an expiration date in the past
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
        } else {
            console.log("Element with ID 'cart-count' not found.");

        }
    }

    updateCartTotal() {
        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cart-total');
        if (cartTotal) {
            cartTotal.innerText = `$${total}`;
        } else {
            console.log("Element with ID 'cart-total' not found.");
        }
    }

    renderCartItems() {
        const cartItems = document.getElementById('cart-items');
        if (!cartItems) {
            console.log("Element with ID 'cart-items' not found.");
            return;
        }

        cartItems.innerHTML = '';
        if (this.cart.length === 0) {
            cartItems.innerHTML = '<li class="list-group-item">Your cart is empty.</li>';
            return;
        }
            this.cart.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `
    <div class="cart-item d-flex align-items-center p-3 border rounded shadow-sm mb-2 bg-light w-100">
   <a href=${item.link}><img  src="${item.image}" alt="${item.name}" class="img-fluid rounded" style="width: 70px; height: 70px; margin-right: 15px; border-radius: 5px;"/></a> 
        <span class="flex-grow-1">${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}</span>
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

    // Event listener for all "Add to Cart" buttons
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

    // Event delegation for quantity change and remove buttons
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
                alert('Your cart is empty. Please add items to your cart before placing an order.');
                return;
            }

            const productData = cart.cart.map(item => {
                return `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
            }).join('\n'); // Join with new line for better readability

            document.getElementById('products').value = JSON.stringify(cart.cart);
            jQuery('#userDetailsModal').modal('show');
        });
    } else {
        console.log("Element with ID 'place-order-button' not found.");
    }

    const orderForm = document.getElementById('order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function (e) {
            e.preventDefault();

            let fullName = document.getElementById('full_name').value.trim();
            let email = document.getElementById('email').value;

            if (fullName === '') {
                alert('Full Name cannot be empty.');
                return;
            }

            let products = JSON.parse(document.getElementById('products').value);
            let totalAmount = products.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);

            jQuery.ajax({
                url: ajax_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'create_order',
                    full_name: fullName,
                    email: email,
                    products: JSON.stringify(products),
                    total_amount: totalAmount,
                },
                success: function (response) {
                    if (response.success) {
                        console.log('AJAX request successful', response);
                        cart.clearCart();
                        alert('Order placed successfully!');
                        jQuery('#userDetailsModal').modal('hide');
                        orderForm.reset();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function (xhr, status, error) {
                    alert(error);
                }
            });
        });
    } else {

        console.log("Element with ID 'order-form' not found.");

    }
});
//     document.addEventListener('DOMContentLoaded', function () {
//         const cart = new ShoppingCart();
//
//         // Event listener for all "Add to Cart" buttons
//         document.querySelectorAll('.add-to-cart').forEach(button => {
//             button.addEventListener('click', function () {
//                 const productId = this.getAttribute('data-id');
//                 const productName = this.getAttribute('data-name');
//                 const productImage = this.getAttribute('data-image');
//                 const productPrice = parseFloat(this.getAttribute('data-price'));
//                 const productLink = this.getAttribute('data-link')
//                 const product = {
//                     id: productId,
//                     name: productName,
//                     price: productPrice,
//                     quantity: 1,
//                     image: productImage,
//                     link: productLink
//
//                 };
//                 cart.addItem(product);
//
//             });
//         });
//
//         // Event delegation for quantity change and remove buttons
//         document.getElementById('cart-items').addEventListener('click', function (event) {
//             const target = event.target;
//
//             if (target.classList.contains('change-quantity')) {
//                 const productId = target.getAttribute('data-id');
//                 const action = target.getAttribute('data-action');
//
//                 if (action === 'increase') {
//                     cart.changeQuantity(productId, cart.cart.find(item => item.id === productId).quantity + 1);
//                 } else if (action === 'decrease') {
//                     cart.changeQuantity(productId, cart.cart.find(item => item.id === productId).quantity - 1);
//                 }
//             }
//
//             if (target.classList.contains('remove-item')) {
//                 const productId = target.getAttribute('data-id');
//                 cart.removeItem(productId);
//             }
//         });
//
//         document.getElementById('place-order-button').addEventListener('click', function () {
//
//             if (cart.cart.length === 0) {
//
//                 alert('Your cart is empty. Please add items to your cart before placing an order.');
// // Check if cartCount exists
//                 return;
//
//             }
//
//             const productData = cart.cart.map(item => {
//
//                 return `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
//
//             }).join('\n'); // Join with new line for better readability
//
//             document.getElementById('products').value = JSON.stringify(cart.cart);
//             jQuery('#userDetailsModal').modal('show');
//         });
//
//         document.getElementById('order-form').addEventListener('submit', function (e) {
//             e.preventDefault();
//
//             let fullName = document.getElementById('full_name').value.trim();
//             let email = document.getElementById('email').value;
//
//             if (fullName === '') {
//                 alert('Full Name cannot be empty.');
//                 return;
//             }
//
//             let products = JSON.parse(document.getElementById('products').value);
//             let totalAmount = products.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);
//
//
//             jQuery.ajax({
//                 url: ajax_obj.ajax_url,
//                 type: 'POST',
//                 data: {
//                     action: 'create_order',
//                     full_name: fullName,
//                     email: email,
//                     products: JSON.stringify(products),
//                     total_amount: totalAmount,
//
//                 },
//                 success: function (response) {
//                     if (response.success) {
//                         console.log('AJAX request successful', response);
//                         cart.clearCart();
//
//                         alert('Order placed successfully!');
//                         jQuery('#userDetailsModal').modal('hide');
//                         document.getElementById('order-form').reset();
//                     } else {
//                         alert('Error: ' + response.data)
//                     }
//                 },
//                 error: function (xhr, status, error) {
//                     alert(error)
//                 }
//             });
//         });
//     });
