jQuery(document).ready(function($) {
    let userEmail = userData.email || {};
    let exchangeRate = userData.exchangeRate || 1
    const currencySymbol =userData.selectedCurrency === 'BYN' ? ' byn' : '$'; 

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
            const orderHistory = $('#order-history');
            if (response.length === 0) {
                orderHistory.html(`<p class="no-orders">${userData.translations.no_orders}</p>`);
                return;
            }

            const orders = response.map(order => {
                const productItems = order.products.map(product => `
                    <li class="total-amount">${product.name} (${userData.translations.quantity} ${product.quantity}, ${userData.translations.price} ${(product.price * exchangeRate).toFixed(2)}${currencySymbol})</li>
                `).join('');

                const statusClass = getStatusClass(order.status.toLowerCase());

                return `
                    <li class="list-group-item">
                        <div class="details">
                            <div class="order-title">${userData.translations.order_id}</div>
                            <div class="order-value">${order.order_id}</div>
                        </div>
                        <div class="details">
                            <div class="order-title">${userData.translations.order_date}</div>
                            <div class="order-value">${order.order_date}</div>
                        </div>  
                        <div class="details">
                            <div class="order-title">${userData.translations.total_amount}</div>
                            <div class="order-value total-amount">
                                ${(order.total_amount * exchangeRate).toFixed(2) }${currencySymbol }
                            </div>
                        </div>  
                        <div class="details status ${statusClass}">
                            <div class="status-title">${userData.translations.status}</div>
                            <div class="status-value">${userData.translations[order.status.toLowerCase()]}</div>
                        </div>   
                        <div class="details">
                            <div class="order-title">${userData.translations.products}</div>
                            <ul class="product-list">${productItems}</ul>      
                        </div>
                    </li>
                `;
            }).join('');


        orderHistory.html(`<ul class="list-group">${orders}</ul>`);
    },
    error: function(error) {
        $('#order-history').html(`<p>${userData.translations.failed_to_fetch}</p>`);
    }
});

function getStatusClass(status) {
    switch (status) {
        case 'pending':
            return 'pending';
        case 'completed':
            return 'completed';
        case 'canceled':
            return 'canceled';
        default:
            return '';
    }}})
