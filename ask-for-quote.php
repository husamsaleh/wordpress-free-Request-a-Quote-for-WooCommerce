<?php
/**
 * Plugin Name: Ask for Quote for Out of Stock Products
 * Description: Displays an "Ask for Quote" button on out-of-stock products, allowing customers to submit inquiries even for variable products.
 * Version: 1.0
 * Author: CENTRAGO.ORG
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function afq_enqueue_scripts() {
    if ( is_product() ) { // Check if it is a single product page
        wp_enqueue_script('jquery');
        wp_enqueue_script('afq-js', plugins_url('/js/afq_script.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('afq-js', 'afqAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_style('afq-css', plugins_url('./css/afq_style.css', __FILE__));
    }
}

add_action('wp_enqueue_scripts', 'afq_enqueue_scripts');
function afq_display_quote_button() {
    global $product;
    // Check if the product is out of stock, has a price of 0, or has no price set
    if (!$product->is_in_stock() || $product->get_price() === '' || $product->get_price() === null || $product->get_price() == 0) {
        echo '<div class="afq-quote-button-container">';
        if ($product->is_type('variable')) {
            // Fetch all variations, including those out of stock, priced at 0, or not priced
            $args = array(
                'post_type'   => 'product_variation',
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'asc',
                'post_parent' => $product->get_id() // get parent product ID
            );
            $variations = get_posts($args);

            // Prepare variations data
            $available_variations = array();
            foreach ($variations as $variation) {
                $variation_obj = new WC_Product_Variation($variation->ID);
                if (!$variation_obj->is_in_stock() || $variation_obj->get_price() === '' || $variation_obj->get_price() === null || $variation_obj->get_price() == 0) {
                    $available_variations[] = array(
                        'variation_id' => $variation_obj->get_id(),
                        'attributes'   => $variation_obj->get_attributes(),
                    );
                }
            }

            include 'templates/variation_selector.php';
            include 'templates/quote_button.php';
        } else {
            include 'templates/quote_button.php';
        }
        echo '</div>';
    }
}

add_action('woocommerce_single_product_summary', 'afq_display_quote_button', 31);

function afq_load_modal_form() {
    if ( is_product() ) { // Check if it is a single product page
        ?>
        <div class="afq-modal" style="display: none;">
            <div class="afq-modal-content">
                <span class="afq-close">&times;</span>
                <?php echo do_shortcode('[contact-form-7 id="7917b23" title="Quote Form"]'); ?>
                <input type="hidden" id="afq-product-id" name="afq-product-id" value="">
                <input type="hidden" id="afq-variation-id" name="afq-variation-id" value="">
                <input type="hidden" id="afq-product-url" name="afq-product-url" value="">
                <input type="hidden" id="afq-product-name" name="afq-product-name" value="">
                <input type="hidden" id="afq-variation-name" name="afq-variation-name" value="">
                <p>Product: <span id="display-product-name"></span></p>
                <p>Variation: <span id="display-variation-name"></span></p>
            </div>
        </div>
        <?php
    }
}

add_action('wp_footer', 'afq_load_modal_form');

function afq_handle_quote_request() {
    check_ajax_referer('afq_nonce', 'security');

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $customer_name = sanitize_text_field($_POST['customer_name']);
    $customer_email = sanitize_email($_POST['customer_email']);
    $inquiry = sanitize_textarea_field($_POST['inquiry']);

    // Here you could send an email, store the inquiry in the database, etc.
    $response = "Thank you, $customer_name. We will get back to you soon about product ID $product_id, variation ID $variation_id.";

    wp_send_json_success($response);
}

add_action('wp_ajax_afq_request_quote', 'afq_handle_quote_request');
add_action('wp_ajax_nopriv_afq_request_quote', 'afq_handle_quote_request');

