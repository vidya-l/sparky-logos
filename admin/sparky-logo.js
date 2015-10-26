jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.sparky_logo_plugin', {
        init : function(ed, url) {

                // Register command for when button is clicked
                ed.addCommand('sparky_logo_insert_shortcode', function() {
                    var data = {
                            'action': 'shortcode_handler'
                        };
                    jQuery.ajax({
                        url: ajaxurl,
                        data: data,
                        success: function (data) {
                            // this is executed when ajax call finished well
                            jQuery('body').append(data);

                        },
                        error: function (xhr, status, error) {
                            // executed if something went wrong during call
                            if (xhr.status > 0) alert('got error: ' + status); // status 0 - when load is interrupted
                        }
                    });

                    jQuery('#sl_shortcode').live("click",function(){
                        var $sl_category = jQuery('#sl_category').val();
                        var $sl_type = jQuery('#sl_type').val();
                        var $sl_no_items = jQuery('#sl_no_items').val();

                        content =  '[sparky_logo category="'+$sl_category+'" number_of_items="'+$sl_no_items+'" type="'+$sl_type+'"]';
                        tinymce.execCommand('mceInsertContent', false, content);
                        jQuery('.sl_overlay').hide();
                        jQuery('.sl_popup').hide();
                    });

                });

            // Register buttons - trigger above command when clicked
            ed.addButton('sparky_logo_button', {title : 'Insert sparky gallery shortcode', cmd : 'sparky_logo_insert_shortcode', image: url + '/gallery.png' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('sparky_logo_button', tinymce.plugins.sparky_logo_plugin);


    jQuery('.sl_close').live('click', function(){
        jQuery('.sl_overlay').hide();
        jQuery('.sl_popup').hide();
    });

   
});