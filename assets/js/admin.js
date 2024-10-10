jQuery(document).ready(function($) {
    $('.wp-color-picker-field').wpColorPicker();

    $('#add-row').on('click', function() {
        var newRow = `
            <tr class="flexible-pricing-row">
                <td class="hidden"><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][id]" value="${Date.now()}" readonly></td>
                <td><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][title]" value=""></td>
                <td><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][content]" value=""></td>
                <td>
                    <select name="pxg_flexible_pricing_settings[new_${Date.now()}][type]">
                        <option value="text">Text</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="url">URL</option>
                    </select>
                </td>
                <td><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][background_color]" value="#ffffff" class="wp-color-picker-field"></td>
                <td><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][text_color]" value="#000000" class="wp-color-picker-field"></td>
                <td><input type="text" name="pxg_flexible_pricing_settings[new_${Date.now()}][border_color]" value="#cccccc" class="wp-color-picker-field"></td>
                <td class="text-center"><input type="checkbox" name="pxg_flexible_pricing_settings[new_${Date.now()}][status]" value="true"></td>
                <td class="text-center">
                    <span class="remove-row cursor"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg></span>
                    <span class="move-row cursor"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"/>
                    </svg></span>
                </td>
            </tr>`;
        $('#flexible-pricing-table tbody').append(newRow);
        $('.wp-color-picker-field').wpColorPicker();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });

    $('#flexible-pricing-table tbody').sortable({
        items: 'tr.flexible-pricing-row',
        handle: '.move-row',
        helper: function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width());
            });
            
            return $helper;
        },
        forcePlaceholderSize: true,
        placeholder: 'sortable-placeholder',
        start: function(event, ui) {
            ui.placeholder.height(ui.helper.outerHeight());
            ui.placeholder.children().each(function(index) {
                $(this).width(ui.helper.children().eq(index).width());
            });
        }
    });
});
