!function(t){t(function(){var a=t(".awfn-shortcode");shortcodeOptions.awfn_debug;t.each(a,function(a,o){var s=t(this),c=t(this).data("atts");t.ajax({url:ajax_url,type:"post",data:{action:"weather_shortcode",security:shortcodeOptions.security,atts:c},success:function(t){s.html(t.data)},error:function(t){}})})})}(jQuery);