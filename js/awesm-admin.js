jQuery(document).ready(function() {
	jQuery('#awesm_twitter_autopost').change(function() {
		var display = 'none';
		if ( jQuery('#awesm_twitter_autopost').attr('checked') ) display = 'block';
		jQuery('#awesm-sharing-twitter').css('display',display);
	});
	jQuery('#awesm_facebook_autopost').change(function() {
		var display = 'none';
		if ( jQuery('#awesm_facebook_autopost').attr('checked') ) display = 'block';
		jQuery('#awesm-sharing-facebook').css('display',display);
	});
    
    // New update services call
    jQuery('#sortable-a-available input:checkbox').click(function(){
        awesm_update_services('a');
    });
    jQuery('#sortable-b-available input:checkbox').click(function(){
        awesm_update_services('b');
    });
    
    // Button setting tabs
    jQuery('.awesm-tabs').each(function(){
        jQuery(this).children('.awesm-config-service').hide();
        jQuery(this).children('.awesm-config-service').first().show();
    });
    jQuery('.awesm-tabs-nav').each(function(){
        jQuery(this).children('div').first().addClass('active');    
    });
    jQuery('.awesm-tabs-nav a').click(function(){
        if(!jQuery(this).parent().hasClass('active')){
            var item_id = jQuery(this).parent().attr('class');
            jQuery(this).parent().parent().children('div').removeClass('active');
            jQuery(this).parent().addClass('active');
            jQuery(this).parent().parent().parent().children('.awesm-tabs').children('.awesm-config-service').hide();
            jQuery('#'+item_id).show();
        }
        return false;
    });
    
    // manual instructions display
    jQuery('.awesm-set-placement select').each(function(){ 
        if(jQuery(this).val()=='manual'){ 
            jQuery(this).parent().children('.manual_instructions').slideDown(500);
        }
    });
    jQuery('.awesm-set-placement select').change(function(){ 
        if(jQuery(this).val()=='manual'){ 
            jQuery(this).parent().children('.manual_instructions').slideDown(500);
        }else{
            jQuery(this).parent().children('.manual_instructions').slideUp(500);
        }
    });
});

// New update services function
function awesm_update_services(set) {
    var selectedListElements = jQuery('#sortable-'+set+'-available input:checked');
    var selectedServices = [];
    for(var i = 0; i < selectedListElements.length; i++) {
        selectedServices[i] = selectedListElements[i].id.substr(10);
    }
    // update the hidden input field
    var control = jQuery( '#awesm_buttons_'+set )[0];
    control.value = selectedServices.join(',');
}