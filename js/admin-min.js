!function(e){"use strict";String.prototype.basename=function(){return new String(this).substring(this.lastIndexOf("/")+1)},e(function(){e("#awfn-error-logs").on("click",".awfn-clear-log",function(n){var t,o,s=options.secure,a=options.ajax_url,r=options.awfn_debug,c=e(n.target).data("file");t=c.basename(),confirm("Truncate "+t+"?")&&(o=document.getElementById(t),e.ajax({url:a,type:"post",data:{action:"awfn_clear_log",file:c,secure:s},success:function(e){o.innerHTML='<p class="success">&lt;Log Cleared&gt;</p>',r&&console.log(e.data)},error:function(e){r&&console.log(e.responseText)}}))})})}(jQuery);