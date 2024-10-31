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
                    // Check if the product already exists in the local cart
                    const existingProduct = this.cart.find(item => item.id === product.id);
                    if (existingProduct) {
                        existingProduct.quantity += 1; // Increment quantity for existing product
                    } else {
                        product.quantity = 1; // Initialize quantity for new product
                        this.cart.push(product); // Add new product to cart
                    }
                    this.updateCart(); // Update the cart UI
                }
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
                    }
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
                }
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

    updateCartCount() {
        const count = this.getItemCount();
        const cartCount = document.getElementById('cart-count');
        cartCount.textContent = count;
        cartCount.classList.toggle('badge-danger', count === 0);
        cartCount.classList.toggle('badge-success', count > 0);
    }

    updateCartTotal() {
        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cart-total');
        cartTotal.innerText = `$${total}`;
    }

    // Render cart items in the modal
    renderCartItems() {
        const cartItems = document.getElementById('cart-items');
        cartItems.innerHTML = ''; // Clear previous items
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

// Initialize the shopping cart
document.addEventListener('DOMContentLoaded', function () {

    const cart = new ShoppingCart();

    // Event listener for all "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productImage = this.getAttribute('data-image');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            const productLink = this.getAttribute('data-link')
            const product = {
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                image: productImage,
                link:productLink

            };
            cart.addItem(product);
        });
    });

    // Event delegation for quantity change and remove buttons
    document.getElementById('cart-items').addEventListener('click', function(event) {
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
});