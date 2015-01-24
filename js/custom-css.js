( function( global, $ ) {
    var editor,
        syncCSS = function() {
            jQuery( '#stringcan-css-box' ).val( editor.getSession().getValue() );
        },
        loadAce = function() {
            editor = ace.edit( 'custom_css' );
            global.safecss_editor = editor;
            editor.getSession().setUseWrapMode( true );
            editor.setShowPrintMargin( false );
            editor.getSession().setValue( jQuery( '#stringcan-css-box' ).val() );
            editor.getSession().setMode( "ace/mode/css" );
            jQuery.fn.spin&&$( '#custom_css_container' ).spin( false );
            //jQuery( '#lsuct-login-form' ).submit( syncCSS );
            jQuery( '#custom_css' ).keyup( syncCSS );
        };
    if ( $.browser.msie&&parseInt( $.browser.version, 10 ) <= 7 ) {
        //$( '#custom_css_container' ).hide();
        $( '#stringcan-css-box' ).show();
        return false;
    } else {
        jQuery( global ).load( loadAce );
    }
    global.aceSyncCSS = syncCSS;
} )( this, jQuery );
