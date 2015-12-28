jQuery(document).ready(function() {
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
    
    
    
    // Form Validation Script
    
    var hasHtml5Validation = function() {
        return typeof document.createElement('input').checkValidity === 'function';
    };
    
    var validateForm = function(theForm, e){
		var checkValidity = theForm.checkValidity(),
        invalidCount = 0, validated = false;

	    if (!checkValidity) {
	        var formElements = theForm,
	            len = formElements.length,
	            invalidElements = [],
	            el;
	
	        // Step through all other form elements
	        for (var i = 0; i < len; i++) {
	            // cast form element to a jQuery object
	            el = jQuery(formElements[i]);
	
	            // Check for required
	            if (el.attr('required')) {
	
	                // ... and blank value
	                if (el.val() == '') {
	                    el.addClass('invalid');
	                    invalidElements.push(el);
	                }
	
	                // ... and nothing selected (dropdowns)
	                if (el.nodeName === "SELECT" && el.selectedIndex === 0) {
	                    el.addClass('invalid');
	                    invalidElements.push(el);
	                }
	            }
	            
	            if (el.attr('pattern')) {
	            	if(el.validity.valid == false){
	            		 invalidElements.push(el);
	            	}
	            }
	        }
	
	        // Check to see if at least one radio is selected in a radio group.
	        // If not mark the group's legend as invalid and add the element to the
	        // invalidElements Array
	        jQuery(theForm).find('input[type="radio"]').each(function() {
	            var name = jQuery(this).attr("name");
	
	            if (jQuery("input:radio[name=" + name + "]:checked").length == 0) {
	                jQuery(this).closest("fieldset").find("legend").addClass("invalid");	              
	                invalidElements.push(this);
	            }
	        });
		
	        // If we have invalid elements... stop the submit event,
	        // get the first invalid element, and scroll the user to the first invalid element.
	        invalidCount = invalidElements.length;
	        if (invalidCount > 0) {
	            e.preventDefault();
	            var firstEl = invalidElements[0];
	            jQuery('html,body').animate({
	                scrollTop: jQuery(theForm).offset().top
	            }, 1000);
	        }
	    }
        
	    return validated;
	};
    
    if (hasHtml5Validation()) {
        jQuery("input[type=submit]").click(function(e) {
            var $target = jQuery(e.currentTarget);
            $target.closest('form').addClass('validated');

            $target.closest('form').submit(function(e) {
                validateForm(this, e);
            });
        });
    }
    
    
});