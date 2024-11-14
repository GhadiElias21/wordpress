<?php

function osm_generate_order_status_dropdown($status, $post_id)
{
    $color = '';
    switch ($status) {
        case 'Pending':
            $color = 'background-color: orange;';
            break;
        case 'Canceled':
            $color = 'background-color: red;';
            break;
        case 'Completed':
            $color = 'background-color: green;';
            break;
        default:
            $color = 'background-color:orange;';
            break;
    }

    $html = '<select class="order-status" data-post-id="' . $post_id . '" style="' . $color . ' color: white; font-weight: bold;">';
    $html .= '<option value="Pending"' . selected($status, 'Pending', false) . '>Pending</option>';
    $html .= '<option value="Canceled"' . selected($status, 'Canceled', false) . '>Canceled</option>';
    $html .= '<option value="Completed"' . selected($status, 'Completed', false) . '>Completed</option>';
    $html .= '</select>';

    return $html;
}