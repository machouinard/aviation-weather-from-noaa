(function ($) {
    "use strict";
    $(function () {

        var metar, pireps, shortcode, widget;

        shortcode = $('section.awfn-shortcode');
        widget = $('section.adds-weather-wrapper');

        shortcode.on('click', '#metar', function(e) {
            if ($(this).find($('.fas')).hasClass('fa-sort-down')) {
                $(this).find($('.fas')).removeClass('fa-sort-down').addClass('fa-sort-up');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fas')).hasClass('fa-sort-up')) {
                $(this).find($('.fas')).removeClass('fa-sort-up').addClass('fa-sort-down');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        shortcode.on('click', '#aircraftreport', function(e) {
            if ($(this).find($('.fas')).hasClass('fa-sort-down')) {
                $(this).find($('.fas')).removeClass('fa-sort-down').addClass('fa-sort-up');
                $(this).children('section#all-pireps').slideDown(200);
            } else if ($(this).find($('.fas')).hasClass('fa-sort-up')) {
                $(this).find($('.fas')).removeClass('fa-sort-up').addClass('fa-sort-down');
                $(this).children('section#all-pireps').slideUp(200);
            }
        });

        widget.on('click', '#metar', function(e) {
            if ($(this).find($('.fas')).hasClass('fa-sort-down')) {
                $(this).find($('.fas')).removeClass('fa-sort-down').addClass('fa-sort-up');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fas')).hasClass('fa-sort-up')) {
                $(this).find($('.fas')).removeClass('fa-sort-up').addClass('fa-sort-down');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        widget.on('click', '#aircraftreport', function() {
            if ($(this).find($('.fas')).hasClass('fa-sort-down')) {
                $(this).find($('.fas')).removeClass('fa-sort-down').addClass('fa-sort-up');
                $(this).children('section#all-pireps').slideDown(200);
            } else if ($(this).find($('.fas')).hasClass('fa-sort-up')) {
                $(this).find($('.fas')).removeClass('fa-sort-up').addClass('fa-sort-down');
                $(this).children('section#all-pireps').slideUp(200);
            }
        });

    });

}(jQuery));
