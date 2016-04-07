(function ($) {
    "use strict";
    $(function () {

        var metar, pireps, shortcode, widget;

        shortcode = $('section.awfn-shortcode');
        widget = $('section.adds-weather-wrapper');

        shortcode.on('click', '#metar', function(e) {
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        shortcode.on('click', '#aircraftreport', function(e) {
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $(this).children('section#all-pireps').slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $(this).children('section#all-pireps').slideUp(200);
            }
        });

        widget.on('click', '#metar', function(e) {
            if ($(this).find($('.fa')).hasClass('fa-sort-desc')) {
                $(this).find($('.fa')).removeClass('fa-sort-desc').addClass('fa-sort-asc');
                $('article:nth-child(3)', this).slideDown(200);
            } else if ($(this).find($('.fa')).hasClass('fa-sort-asc')) {
                $(this).find($('.fa')).removeClass('fa-sort-asc').addClass('fa-sort-desc');
                $('article:nth-child(3)', this).slideUp(200);
            }
        });

        widget.on('click', '#aircraftreport', function() {
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