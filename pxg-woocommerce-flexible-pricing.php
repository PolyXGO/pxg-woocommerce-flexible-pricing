<?php
/*
Plugin Name: PXG WooCommerce Flexible Pricing
Plugin URI: https://polyxgo.vn
Description: A plugin for customizing the display of product prices in WooCommerce.
Version: 1.0.0
Author: PXG Team
Author URI: https://polyxgo.vn
License: GPLv2 or later
Text Domain: pxg-woocommerce-flexible-pricing
*/

if (!defined('ABSPATH')) {
    exit;
}

class PXG_WooCommerce_Flexible_Pricing
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));

        add_action('wp_enqueue_scripts', array($this, 'pxg_load_frontend_styles'));

        add_action('woocommerce_product_options_pricing', array($this, 'add_custom_price_input_field'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_price_input_field'));
        add_filter('woocommerce_get_price_html', array($this, 'display_custom_price_instead_of_sale_price'), 10, 2);

        // Remove add to cart + read more (product)
        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'remove_read_more_button_in_product_loop'), 10, 2);
        add_filter('woocommerce_is_purchasable', array($this, 'disable_add_to_cart_if_custom_price_exists'), 10, 2);
        // END Remove add to cart + read more (product)

        add_action('woocommerce_after_shop_loop_item', array($this, 'display_first_contact_info_custom'), 99);

        add_action('woocommerce_single_product_summary', array($this, 'display_contact_info_instead_of_add_to_cart'), 25);

        add_filter('woocommerce_is_sold_individually', array($this, 'hide_quantity_field_on_product_page'), 10, 2);
    }

    public function display_first_contact_info_custom()
    {
        global $product;
        $custom_price = get_post_meta($product->get_id(), '_custom_price_field', true);

        if (!empty($custom_price)) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

            $options = get_option('pxg_flexible_pricing_settings');
            $option_default = null;
            $default_type = get_option('pxg_flexible_pricing_info_default');

            if (!empty($options) && is_array($options)) {
                foreach ($options as $option) {
                    if (isset($option['status']) && $option['status'] === 'true' && isset($option['type']) && $option['type'] === $default_type) {
                        $option_default = $option;
                        break;
                    }
                }
            }

            if (empty($option_default)) {
                $option_default = !empty($options) ? reset($options) : null;
            }

            if ($option_default) {
                $html = '<div class="pxg-flexible-pricing-info">';
                $html .= '<div class="pxg-flexible-pricing-loop">';

                switch ($option_default['type']) {
                    case 'phone':
                        $html .= '<a href="tel:' . esc_html($option_default['content']) . '">' . esc_html($option_default['title']) . '</a>';
                        break;
                    case 'email':
                        $html .= '<a href="mailto:' . esc_html($option_default['content']) . '">' . esc_html($option_default['title']) . '</a>';
                        break;
                    case 'url':
                        $html .= '<a href="' . esc_url($option_default['content']) . '" target="_blank">' . esc_html($option_default['title']) . '</a>';
                        break;
                    default:
                        $html .= esc_html($option_default['content']);
                        break;
                }

                $html .= '</div>';
                $html .= '</div>';

                echo $html;
            }
        }
    }

    public function pxg_load_frontend_styles()
    {
        wp_enqueue_style(
            'pxg-flexible-pricing-style',
            plugin_dir_url(__FILE__) . 'dist/assets/css/style.min.css',
            array(),
            '1.0.0'
        );
    }

    public function display_contact_info_instead_of_add_to_cart()
    {
        global $product;
        $custom_price = get_post_meta($product->get_id(), '_custom_price_field', true);

        if (!empty($custom_price)) {
            $options = get_option('pxg_flexible_pricing_settings');
            if (!empty($options) && is_array($options)) {
                $active_options = array_filter($options, function ($option) {
                    return isset($option['status']) && $option['status'] === 'true';
                });
                if (!empty($active_options)) {
                    echo '<div class="pxg-flexible-pricing-info">';
                    foreach ($active_options as $option) {
                        $style_attr = '';
                        if (!empty($option['background_color'])) {
                            $style_attr .= 'background-color: ' . esc_attr($option['background_color']) . '; ';
                        }

                        if (!empty($option['border_color'])) {
                            $style_attr .= 'border: 1px solid ' . esc_attr($option['border_color']) . '; ';
                        }

                        if (!empty($option['text_color'])) {
                            $style_attr .= 'color: ' . esc_attr($option['text_color']) . '; ';
                        }

                        $text_attr = '';
                        if (!empty($option['text_color'])) {
                            $text_attr .= 'color: ' . esc_attr($option['text_color']) . '; ';
                        }
                        switch ($option['type']) {
                            case 'phone':
                                echo '<div class="pxg-flexible-pricing" ' . (!empty($style_attr) ? 'style="' . $style_attr . '"' : '') . '><a href="tel:' . esc_attr($option['content']) . '" target="_blank" ' . (!empty($text_attr) ? 'style="' . $text_attr . '"' : '') . '>' . esc_html($option['title']) . '</a></div>';
                                break;
                            case 'email':
                                echo '<div class="pxg-flexible-pricing" ' . (!empty($style_attr) ? 'style="' . $style_attr . '"' : '') . '><a href="mailto:' . esc_attr($option['content']) . '" target="_blank" ' . (!empty($text_attr) ? 'style="' . $text_attr . '"' : '') . '>' . esc_html($option['title']) . '</a></div>';
                                break;
                            case 'url':
                                echo '<div class="pxg-flexible-pricing" ' . (!empty($style_attr) ? 'style="' . $style_attr . '"' : '') . '><a href="' . esc_url($option['content']) . '" target="_blank" rel="nofollow" ' . (!empty($text_attr) ? 'style="' . $text_attr . '"' : '') . '>' . esc_html($option['title']) . '</a></div>';
                                break;
                            default:
                                echo '<div class="pxg-flexible-pricing" ' . (!empty($style_attr) ? 'style="' . $style_attr . '"' : '') . '>' . esc_html($option['content']) . '</div>';
                                break;
                        }
                    }
                    echo '</div>';
                }
            }
        }
    }

    public function add_admin_menu()
    {
        add_menu_page(
            __('Flexible Pricing Settings', 'pxg-woocommerce-flexible-pricing'),
            __('Flexible Pricing', 'pxg-woocommerce-flexible-pricing'),
            'manage_options',
            'pxg-woocommerce-flexible-pricing',
            array($this, 'settings_page'),
            'dashicons-admin-generic',
            56
        );
    }

    public function register_settings()
    {
        register_setting('pxg_flexible_pricing_group', 'pxg_flexible_pricing_settings');
        register_setting('pxg_flexible_pricing_group', 'pxg_flexible_pricing_info_default');
    }

    public function settings_page()
    {
        require_once plugin_dir_path(__FILE__) . 'inc/admin-settings-page.php';
    }

    public function load_admin_scripts($hook)
    {
        if ($hook != 'toplevel_page_pxg-woocommerce-flexible-pricing') {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('pxg-admin-script', plugin_dir_url(__FILE__) . 'dist/assets/js/admin.min.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker'), '1.0.0', true);
        wp_enqueue_style('pxg-admin-style', plugin_dir_url(__FILE__) . 'dist/assets/css/admin.min.css');
    }

    public function hide_quantity_field_on_product_page($return, $product)
    {
        return true;
    }

    public function display_custom_price_instead_of_sale_price($price, $product)
    {
        $custom_price = get_post_meta($product->get_id(), '_custom_price_field', true);
        if (!empty($custom_price)) {
            $price = '<span class="custom-price">' . $custom_price . '</span>';
        }

        return $price;
    }

    public function add_custom_price_input_field()
    {
        woocommerce_wp_text_input(
            array(
                'id'          => '_custom_price_field',
                'label'       => __('Custom Price Field', 'woocommerce'),
                'desc_tip'    => true,
                'description' => __('Enter a custom value here', 'woocommerce'),
            )
        );
    }

    public function save_custom_price_input_field($post_id)
    {
        $custom_field_value = isset($_POST['_custom_price_field']) ? sanitize_text_field($_POST['_custom_price_field']) : '';
        update_post_meta($post_id, '_custom_price_field', $custom_field_value);
    }

    public function remove_read_more_button_in_product_loop($button, $product)
    {
        $custom_price = get_post_meta($product->get_id(), '_custom_price_field', true);
        if (!empty($custom_price) && (!$product->is_purchasable())) {
            return '';
        }
        return $button;
    }


    public function disable_add_to_cart_if_custom_price_exists($is_purchasable, $product)
    {
        $custom_price = get_post_meta($product->get_id(), '_custom_price_field', true);
        if (!empty($custom_price)) {
            return false;
        }

        return $is_purchasable;
    }
}

new PXG_WooCommerce_Flexible_Pricing();
