jQuery(document).ready(function($) {
    let userEmail = userData.email;

    $.ajax({
        url: userData.restUrl,
        method: 'POST',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', userData.nonce);
        },
        data: {
            email: userEmail
        },
        success: function(response) {
            let orderHistory = $('#order-history');
            if (response.length === 0) {
                orderHistory.html('<p class="no-orders">No orders found.</p>');
                return;
            }

            let orders = `
                <ul class="list-group">
            `;

            $.each(response, function(index, order) {
                let productItems = order.products.map(product => `
                    <li>${product.name} (Quantity: ${product.quantity} , Price of 1 unit: $${product.price})</li>
                `).join('');

                let statusClass;
                switch (order.status.toLowerCase()) {
                    case 'pending':
                        statusClass = 'pending';
                        break;
                    case 'completed':
                        statusClass = 'completed';
                        break;
                    case 'canceled':
                        statusClass = 'canceled';
                        break;
                    default:
                        statusClass = '';
                }

                orders += `
                    <li class="list-group-item">
                        <div class="details">
                            <div class="order-title">Order ID:</div>
                            <div class="order-value">${order.order_id}</div>
                        </div>
                        <div class="details">
                            <div class="order-title">Order Date:</div>
                            <div class="order-value">${order.order_date}</div>
                        </div>  
                        <div class="details">
                            <div class="order-title">Total Amount:</div>
                            <div class="order-value">$${order.total_amount}</div>
                        </div>  
                        <div class="details status ${statusClass}">
                            <div class="status-title">Status:</div>
                            <div class="status-value">${ order.status }</div>
                        </div>   
                        <div class="details">
                            <div class="order-title">Products:</div>
                            <ul class="product-list">${productItems}</ul>      
                        </div>
                    </li>
                `;
            });

            orders += '</ul>';
            orderHistory.html(orders);
        },
        error: function(error) {
            $('#order-history').html('<p>Failed to fetch orders.</p>');
        }
    });
});
