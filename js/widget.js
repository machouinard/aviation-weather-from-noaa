(function ($) {
    "use strict";
    $(function () {

        console.log( 'wtf' );

        var metar, pireps, shortcode, widget;

        shortcode = $('section.awfn-shortcode');
        widget = $('section.adds-weather-wrapper');

        shortcode.on('click', '#metar', function(e) {
            console.log('sc metar clicked');
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        shortcode.on('click', '#aircraftreport', function(e) {
            console.log('sc pireps clicked');
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $(this).children('section#all-pireps').slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $(this).children('section#all-pireps').slideUp(200);
            }
        });

        widget.on('click', '#metar', function(e) {
            console.log( 'widget metar clicked' );
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        widget.on('click', '#aircraftreport', function() {
            console.log( 'widget pirep clicked' );
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $(this).children('section#all-pireps').slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $(this).children('section#all-pireps').slideUp(200);
            }
        });

    });

}(jQuery));