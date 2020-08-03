jQuery( document ).ready(function($) {
    $( '.candlestick' ).candlestick({
		'swipe': false,
		'on':'1',
		'off':'0',
		afterAction: function(obj, dummy, val) {
			$( '#submit' ).prop( 'disabled', false );
			if ( 'c' === $(obj).attr('id').substring( 0, 1 ) && 'default' === val ) {
				$(obj).candlestick('on');
			}
		}
	});
	if ( ! $( '#update' ).length ) {
		$( '#submit' ).prop( 'disabled', true );
	}
});
