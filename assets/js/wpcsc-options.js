jQuery(document).ready(function() {
    jQuery('body').on('click', '.wpcsc-js-add-repeater-field', function(e){
        e.preventDefault();
        var wrapper = jQuery(this).parents('.wpcsc-multifield-wrapper').find('.wpcsc-multifields');
        var inputFieldCount = jQuery(wrapper).children().length;
        var inputField = '<div class="wpcsc-multi-field">' +
                ' <input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables][' + inputFieldCount + '][key]" value="" placeholder="Sass Variable" required="required" />' +
                ' <input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables][' + inputFieldCount + '][value]" value="" placeholder="Default Value" required="required" />' +
                ' <a href="#" class="button wpcsc-js-remove-repeater-field">Remove</a>' +
            '</div>';
        var inputCount = 1;
        jQuery(inputField).appendTo(wrapper).find('input').val('');
        
    });
    jQuery('body').on('click', '.wpcsc-js-remove-repeater-field', function(e){
        e.preventDefault();
        var wrapper = jQuery(this).parents('.wpcsc-multifield-wrapper').find('.wpcsc-multifields');
        jQuery(this).parent('.wpcsc-multi-field').remove();
        
        var count = 0;
        // Find all the fields and rename the count values so they line up in the arrays without error
        jQuery(wrapper).find('.wpcsc-multi-field').each(function() {
            jQuery(this).find('input').each( function() {
                var name = jQuery(this).attr('name',  jQuery(this).attr('name').replace(/\[([0-9])\]/g, '[' + count + ']'));
            });
            count++;
        });
    });
});