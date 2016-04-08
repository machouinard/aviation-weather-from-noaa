(function ($) {
    "use strict";
    $(function () {
        $('button#awfn-clear-log').on('click', function (e) {

            if ( confirm( 'Truncate Log Files?' ) ) {
                var nonce = options.secure,
                    ajax_url = options.ajax_url,
                    awfn_debug = options.awfn_debug,
                    log_div = document.getElementById( 'awfn-error-logs' );

                $.ajax({
                    url: ajax_url,
                    type: 'post',
                    data: {
                        action: 'awfn_clear_log',
                        secure: nonce,
                    },
                    success : function( resp ) {
                        log_div.innerHTML = '';
                        if ( awfn_debug ) {
                            console.log( resp.data );
                        }
                    },
                    error : function( resp ) {
                        if ( awfn_debug ) {
                            console.log( resp.responseText );
                        }
                    }
                })
            }

        });
    });
}(jQuery));