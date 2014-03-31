function checkScrollNewsletterNotification()
{
	scroll = $(window).scrollTop();
	maxScroll = $(document).height() - $(window).height();

	if ( scroll >= maxScroll * 0.7 )
	{
		$('.newsletter_notification').addClass('visible');
		$(this).unbind('scroll', checkScrollNewsletterNotification);
	}
}

$(function() {
	$(window).scroll( checkScrollNewsletterNotification );
	
	$('.newsletter_notification a').click( function(e) {
		e.preventDefault();
		$('.newsletter_notification').addClass('closed');
	})
});