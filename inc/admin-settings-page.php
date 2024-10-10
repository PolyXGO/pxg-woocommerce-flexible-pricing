<div class="wrap">
    <h1><?php _e('Flexible Pricing Settings', 'pxg-woocommerce-flexible-pricing'); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('pxg_flexible_pricing_group'); ?>
        <?php
        $options = get_option('pxg_flexible_pricing_settings');
        $default_type = get_option('pxg_flexible_pricing_info_default');

        $type_list = array();
        if (!empty($options) && is_array($options)) {
            foreach ($options as $option) {
                if (!empty($option['type'])) {
                    $type_list[] = $option['type'];
                }
            }
            $type_list = array_unique($type_list);
        }
        ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pxg_flexible_pricing_info_default"><?php _e('Default Display Type', 'pxg-woocommerce-flexible-pricing'); ?></label>
                </th>
                <td>
                    <select name="pxg_flexible_pricing_info_default" id="pxg_flexible_pricing_info_default">
                        <?php if (!empty($type_list)) : ?>
                            <?php foreach ($type_list as $type) : ?>
                                <option value="<?php echo esc_attr($type); ?>" <?php selected($default_type, $type); ?>>
                                    <?php echo ucfirst(esc_html($type)); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value=""><?php _e('No types available', 'pxg-woocommerce-flexible-pricing'); ?></option>
                        <?php endif; ?>
                    </select>
                    <span class="description"><?php _e('Select the default type to display in the front-end. The first data is displayed by default.', 'pxg-woocommerce-flexible-pricing'); ?></span>
                </td>
            </tr>
        </table>

        <table class="form-table" id="flexible-pricing-table">
            <thead>
                <tr>
                    <th class="hidden">&nbsp;</th>
                    <th><?php _e('Title', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th><?php _e('Content', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th><?php _e('Type', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th><?php _e('Background Color', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th><?php _e('Text Color', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th><?php _e('Border Color', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th class="text-center"><?php _e('Status', 'pxg-woocommerce-flexible-pricing'); ?></th>
                    <th class="text-center"><?php _e('Actions', 'pxg-woocommerce-flexible-pricing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($options)) {
                    foreach ($options as $key => $option) {
                ?>
                        <tr class="flexible-pricing-row">
                            <td class="hidden"><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][id]" value="<?php echo esc_attr($option['id']); ?>" readonly></td>
                            <td><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][title]" value="<?php echo esc_attr($option['title']); ?>"></td>
                            <td><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][content]" value="<?php echo esc_attr($option['content']); ?>"></td>
                            <td>
                                <select name="pxg_flexible_pricing_settings[<?php echo $key; ?>][type]">
                                    <option value="text" <?php selected($option['type'], 'text'); ?>>Text</option>
                                    <option value="phone" <?php selected($option['type'], 'phone'); ?>>Phone</option>
                                    <option value="email" <?php selected($option['type'], 'email'); ?>>Email</option>
                                    <option value="url" <?php selected($option['type'], 'url'); ?>>URL</option>
                                </select>
                            </td>
                            <td><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][background_color]" value="<?php echo esc_attr(isset($option['background_color']) ? $option['background_color'] : '#ffffff'); ?>" class="wp-color-picker-field"></td>
                            <td><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][text_color]" value="<?php echo esc_attr(isset($option['text_color']) ? $option['text_color'] : '#000000'); ?>" class="wp-color-picker-field"></td>
                            <td><input type="text" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][border_color]" value="<?php echo esc_attr(isset($option['border_color']) ? $option['border_color'] : '#cccccc'); ?>" class="wp-color-picker-field"></td>
                            <td class="text-center">
                                <input type="checkbox" name="pxg_flexible_pricing_settings[<?php echo $key; ?>][status]" <?php checked(isset($option['status']) ? $option['status'] : false, 'true'); ?> value="true">
                            </td>
                            <td class="text-center">
                                <span class="remove-row cursor"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                    </svg></span>
                                <span class="move-row cursor"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8" />
                                    </svg></span>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <button type="button" class="button" id="add-row">Add New</button>
        <?php submit_button(); ?>
    </form>
</div>
