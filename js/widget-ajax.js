(function ($) {

    $(function () {

        var wrap = $('.adds-weather-wrapper');
        $.each( wrap, function (i, v) {

            var $this = $(this);
            var instance = $this.data('instance');

            $.ajax({
                url: ajax_url,
                type: 'post',
                data: {
                    action: 'weather_widget',
                    instance: instance
                },
                success : function( resp ) {
                    // console.log( resp );
                    $this.html(resp.data);
                },
                error : function( x ) {
                    console.log('widget error posting to weather_widget' );
                }
            })
        });

    });
})(jQuery);