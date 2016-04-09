(function ($) {

    $(function () {

        var wrap = $('.adds-weather-wrapper');
        // Our debug setting was localized in main file
        var awfn_debug = ( '1' === widgetOptions.awfn_debug ) ? true : false;

        $.each( wrap, function (i, v) {

            var $this = $(this);
            var instance = $this.data('instance');

            $.ajax({
                url: ajax_url,
                type: 'post',
                data: {
                    action: 'weather_widget',
                    security: widgetOptions.security,
                    instance: instance
                },
                success : function( resp ) {
                    if ( awfn_debug ) {
                        console.log( 'widget ajax success' );
                    }
                    $this.html(resp.data);
                },
                error : function( x ) {
                    if ( awfn_debug ) {
                        console.log('widget error posting to weather_widget' );
                    }
                }
            })
        });

    });
})(jQuery);