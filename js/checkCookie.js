 function checkcookie(){
    setTimeout(function(){
		if( $('body').data('userCookie') !=  $.cookie('usuario') )
            window.location.reload();
		checkcookie();
    }, 4000);

}
 
 $(function() {
	 $('body').data('userCookie', $.cookie('usuario') );
	 checkcookie();
 });
 