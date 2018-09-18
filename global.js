/* Global Javascript file */
jQuery(function($) {

	$(document).ready(function(){
        console.log( JSON.parse( site_data['customdata'] ) );
        console.log( JSON.parse( site_data['postdata'] ) );
        console.log( JSON.parse( site_data['tagdata'] ) );
        console.log( JSON.parse( site_data['catdata'] ) );
	});

    $(window).load(function() {
    });

    $(document).ajaxStart(function() {
    });

    $(document).ajaxComplete(function() { // http://api.jquery.com/ajaxstop/

    });

});
