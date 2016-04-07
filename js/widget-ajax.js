(function ($) {

    $(function () {

        var wrap = $('.adds-weather-wrapper');
        $.each( wrap, function (i, v) {
            console.log('value: ' + v);
            var $this = $(this);
            console.log('this: ' + $this );
            var instance = $this.data('instance');

            $.ajax({
                url: ajax_url,
                type: 'post',
                data: {
                    action: 'weather_widget',
                    instance: instance
                },
                success : function( resp ) {
                    console.log( resp );
                    $this.html(resp.data);
                },
                error : function( x ) {
                    alert('error: ' + x );
                }
            })
        });

    });
})(jQuery);