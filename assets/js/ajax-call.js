jQuery(document).ready( function($) {
	jQuery("#wp_type").change( function() {
		jQuery('#wp_placeholder').empty();
        jQuery("#wp_placeholder").append("<option value=''>- select placeholder -</option>");
		var data = {
			action: 'fill_placeholder',
                    post_var: jQuery(this).val(),
                    nonce: jQuery('#wp_nonce').val()
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			var obj = jQuery.parseJSON(response);
			if(obj != null && obj.length > 0){
				jQuery('#wp_placeholder').empty();
				for(i = 0; i < obj.length; i++){
					var newOption = "<option value='"+obj[i] +"'>"+ obj[i] + "</option>"; 
					jQuery("#wp_placeholder").append(newOption);
				}
			}
	 	});
	 	return false;
	});
	
	jQuery(".delete_template").click( function() {
		var answer = confirm ("Are you sure you want to delete this template?")
		if(!answer) return false;
		
		var data = {
			action: 'delete_template',
                post_var: jQuery(this).attr("data-id"),
		    	nonce: jQuery(this).attr("data-nonce")
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			alert(response);
			location.reload();
		});
		return false;
	});
	
	jQuery("#btninsert").click( function() {
		var selectedval = jQuery("#wp_placeholder").val();
		if(selectedval != null && selectedval.length > 0){
			var val = jQuery("#wp_body").val();
			val = val + " {" + selectedval + "}";
			jQuery("#wp_body").val(val);
		}
	 	return false;
	});
	
	jQuery("#wp_template").change( function() {
		$("#wp_body").prop("disabled", true);
		jQuery("#wp_body").val('');
		var data = {
			action: 'fill_body',
                post_var: jQuery(this).val(),
                nonce: jQuery('#wp_nonce').val()
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			jQuery("#wp_body").val(response);
			$("#wp_body").prop("disabled", false);
	 	});
	 	return false;
	});
});