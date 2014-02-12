$(function() {
    //Enable SyntaxHighlighter
    SyntaxHighlighter.all();

    //Search functionality
    $('.form-search button').on('click', function(e) {
    	var $el = $(e.currentTarget);
    	if ($el.hasClass('disabled')) {
    		e.preventDefault();
    	}
    });

    $('.search-query').on('keyup', function(e) {
    	var $el = $(e.currentTarget),
    		$btn = $('.form-search button'),
    		$len = $el.val().length;

    	if ($len > 0) {
    		$btn.removeClass('disabled');
    	} else if ($len == 0) {
    		$btn.addClass('disabled');
    	}
    });
});