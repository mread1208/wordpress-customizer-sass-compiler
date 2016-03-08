jQuery(document).ready(function() {
    
    // Auto add slashes before and after the path to the custom sass file
    var field = document.getElementsByClassName("wpcsc-js-check-slashes");
	jQuery(field).focusout(function() {
        var firstChar = jQuery(field).val().substr(0, 1);
        var lastChar = jQuery(field).val().slice(-1);
        if(firstChar != '/') {
            jQuery(field).val('/' + jQuery(field).val());
        }
        if(lastChar != '/') {
            jQuery(field).val(jQuery(field).val() + '/');
        }
	});
    
    
    // Show and hide the repeater fields for the custom variables
    jQuery('body').on('click', '.wpcsc-js-add-repeater-field', function(e){
        e.preventDefault();
        var wrapper = jQuery(this).parents('.wpcsc-multifield-wrapper').find('.wpcsc-multifields');
        var inputFieldCount = jQuery(wrapper).find('tbody').children().length;
        var inputField = '<tr class="wpcsc-multi-field">' +
                '<td><input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables][' + inputFieldCount + '][key]" value="" placeholder="Sass Variable" required="required" /></td>' +
                '<td><input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables][' + inputFieldCount + '][value]" value="" placeholder="Default Value" required="required" /></td>' +
                '<td><a href="#" class="button wpcsc-js-remove-repeater-field">Remove</a></td>' +
            '</tr>';
        var inputCount = 1;
        jQuery(inputField).appendTo(wrapper).find('input').val('');
        
    });
    jQuery('body').on('click', '.wpcsc-js-remove-repeater-field', function(e){
        e.preventDefault();
        var wrapper = jQuery(this).parents('.wpcsc-multifield-wrapper').find('.wpcsc-multifields');
        jQuery(this).parents('.wpcsc-multi-field').remove();
        
        var count = 0;
        // Find all the fields and rename the count values so they line up in the arrays without error
        jQuery(wrapper).find('.wpcsc-multi-field').each(function() {
            jQuery(this).find('input').each( function() {
                var name = jQuery(this).attr('name',  jQuery(this).attr('name').replace(/\[([0-9])\]/g, '[' + count + ']'));
            });
            count++;
        });
    });
    
    
    
    // Form Validation Scripts
    
    var validateForm = function(theForm, e){
		var checkValidity = theForm.checkValidity(),
        invalidCount = 0, validated = false;
        
        var formElements = theForm,
            len = formElements.length,
            el;
        
        var invalidElements = [];

        // Step through all other form elements
        for (var i = 0; i < len; i++) {
            // cast form element to a jQuery object
            el = jQuery(formElements[i]);

            if(el.hasClass('wpcsc-js-whitespace-validate')) {
                var pattern = /\s/;
                var whitespace = new RegExp(/\s/);
                if(whitespace.test(el.val())) {
                    el.addClass('wpcsc-invalid-input');
                    //invalidElements.push("");
                    invalidElements.push("No Whitespaces Allowed in Custom Variable Names or Values<br />");
                }
            }
        }
        
        // If we have invalid elements... stop the submit event,
        // get the first invalid element, and scroll the user to the first invalid element.
        invalidCount = invalidElements.length;
        if (invalidCount > 0) {
            e.preventDefault();
            var firstEl = invalidElements[0];
            jQuery(theForm).prepend('<div class="error notice is-dismissable"><p>' + invalidElements + '</p></div>');
            jQuery('html,body').animate({
                scrollTop: jQuery('body').offset().top
            }, 500);
        } else {
            validated = true;
        }
        
        return validated; 
	};
    
    jQuery("body").on("click", "input[type=submit]", function(e) {
        var $target = jQuery(e.currentTarget);
        $target.closest('form').addClass('validated');

        $target.closest('form').submit(function(e) {
            validateForm(this, e);
        });
    });
    
});