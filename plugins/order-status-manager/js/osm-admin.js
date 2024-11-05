jQuery(document).ready(function($) {
    $('.order-status').change(function() {
        let postId = $(this).data('post-id');
        let newStatus = $(this).val();
        let $select = $(this);
        $.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'update_order_status',
                post_id: postId,
                status: newStatus,
                nonce: ajax_obj.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);

                    switch (newStatus) {
                        case 'Pending':
                            $select.css('background-color', 'orange');
                            break;
                        case 'Canceled':
                            $select.css('background-color', 'red');
                            break;
                        case 'Completed':
                            $select.css('background-color', 'green');
                            break;
                        default:
                            $select.css('background-color', 'orange');
                            break;
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('An error occurred while updating the order status. Please try again.');
            }
        });
    });
});