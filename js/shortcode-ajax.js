(function ($) {

    $(function () {

        var shortcodes = $('.awfn-shortcode');
        // Our debug setting was localized in main file
        var awfn_debug = ( '1' === shortcodeOptions.awfn_debug ) ? true : false;

        $.each(shortcodes, function (i, v) {

            var $this = $(this);
            var atts = $(this).data('atts');

            $.ajax({
                url: ajax_url,
                type: 'post',
                data: {
                    action: 'weather_shortcode',
                    security: shortcodeOptions.security,
                    atts: atts
                },
                success : function( resp ) {
                    if ( awfn_debug ) {
                        //console.log( 'shortcode ajax success' );
                    }
                    $this.html(resp.data);
                },
                error : function( x ) {
                    if( awfn_debug ) {
                        //console.log( 'Shortcode error posting to weather_shortcode' );
                    }
                }
            })
        });

    });
})(jQuery);
