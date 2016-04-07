(function ($) {

    $(function () {
        console.log($);

        var shortcodes = $('.awfn-shortcode');

        $.each(shortcodes, function (i, v) {
            console.log(v);
            var $this = $(this);
            var atts = $(this).data('atts');

            $.ajax({
                url: ajax_url,
                type: 'post',
                data: {
                    action: 'weather_shortcode',
                    atts: atts
                },
                success : function( resp ) {
                    console.log( resp );
                    $this.html(resp.data);
                },
                error : function( x ) {
                    console.log( 'Error posting to shortcode function' );
                }
            })
        });

    });
})(jQuery);