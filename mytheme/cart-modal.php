<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Shopping Cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="cart-items" class="list-group mb-3"></ul>
                <p class="mt-3"><strong>Total:</strong> <span id="cart-total" class="font-weight-bold text-success"></span></p>
                <div id="empty-cart-message" class="alert alert-warning d-none" role="alert">
                    Your cart is currently empty. Add items to your cart to see them here!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="place-order-button">Place an Order</button>
            </div>
        </div>
    </div>
</div>
