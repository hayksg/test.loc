$(function(){
	//////////   For keeping footer in the bottom of the page   ///////////////
	if ($(document).height() <= $(window).height()) {
		$('footer').addClass('container navbar-fixed-bottom');
	}
	
	//////////   Do not show class breadcrumb when it need   //////////////////
    if ($('.breadcrumb li').length == 0) {
    	$('.breadcrumb').remove();
    }
    
    //////////   For link Home   //////////////////////////////////////////////
    if ($(document).width() < 768) {
    	$('.navbar-header ul.nav').removeClass('navbar-nav');
    	$('.navbar-header ul.nav').css('margin-top', '4px');
    	if ($('.navbar-header ul.nav li').data('title') == 'Main Page') {
    		$('.navbar-header ul.nav li a').css('color', '#fff');
    	}
    } else {
    	$('.navbar-header ul.nav li a').css('fontSize', '14px');
    }
    
    //////////   For input type file   ////////////////////////////////////////
    if ($(document).width() < 570) {
    	$(':file').jfilestyle({inputSize: '100%'});
    	$('div.jfilestyle').removeClass('jfilestyle-corner');
    	$('div.jfilestyle input').css('marginBottom', '5px');
    } else {
    	$(':file').jfilestyle({inputSize: '250px'});
    }
    
    //////////   For button to up   ///////////////////////////////////////////
    $(window).scroll(function () {
		if ($(this).scrollTop() > 300) {
			$('#back-to-top').fadeIn();
		} else {
			$('#back-to-top').fadeOut();
		}
	});
	// scroll body to 0px on click
	$('#back-to-top').click(function () {
		$('#back-to-top').tooltip('hide');
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});

	$('#back-to-top').tooltip('show');
});