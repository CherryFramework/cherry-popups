( function( $ ) {
	"use strict";

	CherryJsCore.utilites.namespace('cherryPopupsFrontScripts');
	CherryJsCore.cherryPopupsFrontScripts = {
		init: function () {
			CherryJsCore.variable.$document.on( 'ready', this.readyRender.bind( this ) );
		},

		readyRender: function () {
			$( 'head' ).append( '<style title="cherry-dynamic-css-styles" type="text/css">' + window.cherryPopupDunamicStyles + '</style>' );
			this.popupsPluginInit();
		},

		popupsPluginInit: function() {
			if ( $( '.cherry-popup-wrapper' )[0] ) {
				$( '.cherry-popup-wrapper' ).cherryPopupsPlugin();
			}
		},
	}
	CherryJsCore.cherryPopupsFrontScripts.init();
} ( jQuery ) );

