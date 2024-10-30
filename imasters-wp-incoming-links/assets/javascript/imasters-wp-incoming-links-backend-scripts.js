// JavaScript Document

( function($) {

	$( function($) {
				
		IWPIL.iwpil_clear();

                IWPIL.iwpil_uninstall_button();
                
	});

	IWPIL = {
	
		iwpil_clear : function() {
			
			$('#clearlog').click( function() {
				var confirm_delete = confirm( confirm_delete_message );
				
				if ( !confirm_delete )
					return false;
			});
			
		},

                iwpil_uninstall_button : function() {
                    var button = $('input[name="do"]');
                    var checkbox = $('#uninstall_iwpil_yes');
                    button.hide();
                    checkbox.attr( 'checked', '' ).click(function() {
                        var is_checked = checkbox.attr( 'checked' );
                        if ( is_checked )
                            button.fadeIn();
                        else
                            button.fadeOut();
                    })
                }
	}

})(jQuery);