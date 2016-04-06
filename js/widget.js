(function ($) {
	"use strict";
	$(function () {

        var a = $('.awfn-shortcode');
        console.log(a);
        $.each(a, function (i, v) {
            console.log(v);
            var $this = $(this);
            var atts = $(this).data('atts');
            console.log( atts );
            $(this).html('working...');
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
                    listeners();
                },
                error : function( x ) {
                    alert('error: ' + x );
                }
            })
        });

        var metar, pireps;

        function listeners() {
            /**
             * METAR section selector
             *
             * @type {*|HTMLElement}
             */
            metar = $('section#metar');

            /**
             * Slide decoded METAR into and out of view when clicked as well as change icons
             */
            metar.on('click', function(e) {

                if($(this).find($('.fa') ).hasClass('fa-sort-desc') ) {
                    $(this).find($('.fa') ).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                    $('article:nth-child(3)', this).slideDown(200);
                } else if($(this).find($('.fa') ).hasClass('fa-sort-asc') ) {
                    $(this).find($('.fa') ).removeClass('fa-sort-asc' ).addClass('fa-sort-desc');
                    $('article:nth-child(3)', this).slideUp(200);
                }
            });

            /**
             * PIREPS section selector
             *
             * @type {*|HTMLElement}
             */
            pireps = $('section#aircraftreport');

            /**
             * Slide PIREPS into and out of view when clicked as well as change icons
             */
            pireps.on('click', function(e) {

                if($(this ).find($('.fa') ).hasClass('fa-sort-desc') ) {
                    $(this ).find($('.fa') ).removeClass('fa-sort-desc' ).addClass('fa-sort-asc');
                    $(this ).children('section#all-pireps').slideDown(200);
                } else if($(this ).find($('.fa') ).hasClass('fa-sort-asc') ) {
                    $(this ).find($('.fa') ).removeClass('fa-sort-asc' ).addClass('fa-sort-desc');
                    $(this ).children('section#all-pireps').slideUp(200);
                }
            } );
        }

	});

}(jQuery));