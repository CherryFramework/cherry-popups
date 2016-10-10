// cherryPortfolioPlugin plugin
( function( $ ) {
	var methods = {
		init : function( options ) {

			var settings = {
				call: function() {}
			}

			return this.each( function() {
				if ( options ) {
					$.extend( settings, options );
				}

				var $this         = $( this ),
					popupSettings = $this.data( 'popup-settings' );

					console.log(popupSettings);

				( function () {

					switch( popupSettings['use'] ) {
						case 'open-page':
							addOpenEventsFunction();
							break;
						case 'close-page':
							addCloseEventsFunction();
							break;
					}


					closePopupEvent( popupSettings['load-open-delay'] );

				} )();

				/*
				 * Add open events functions
				 *
				 * @return {void}
				 */
				function addOpenEventsFunction() {

					switch( popupSettings['open-appear-event'] ) {
						case 'page-load':
							pageLoadEvent( popupSettings['load-open-delay'] );
							break;
						case 'user-inactive':
							userInactiveEvent( popupSettings['inactive-time'] );
							break;
						case 'scroll-page':
							scrollPageEvent( popupSettings['page-scrolling-value'] );
							break;
					}
				}

				/**
				 * Add close events functions
				 *
				 * @return {void}
				 */
				function addCloseEventsFunction() {

				}

				/**
				 * Close button event.
				 *
				 * @return {void}
				 */
				function closePopupEvent() {
					var timeout = null;

					$this.on( 'click', '.cherry-popup-close-button', function( event ) {
						var button = event.currentTarget,
							$parentPopup = $( button ).closest( '.cherry-popup' );

							//$this.toggleClass( 'hide-animation show-animation' );

							$parentPopup.remove();

							/*clearTimeout( timeout );
							timeout = setTimeout( function() { }, 500 );*/
					} );
				}

				/**
				 * Page on load event
				 *
				 * @param  {int} openDelay Open delay time.
				 * @return {void}
				 */
				function pageLoadEvent( openDelay ) {
					var openDelay = +openDelay || 0;

					openDelay = openDelay * 1000;

					$( window ).on( 'load', function() {
						//$this.removeClass( 'hide-state' );
						setTimeout( function() {
							$this.addClass( 'show-animation' );
						}, openDelay );

					} );
				}

				function userInactiveEvent( inactiveDelay ) {
					var inactiveDelay = +inactiveDelay || 0,
						timeout = null,
						isInactive = true;

					inactiveDelay = inactiveDelay * 1000;

					setTimeout( function() {
						if ( isInactive ) {
							$this.removeClass( 'hide-state' );
							setTimeout( function() {
								$this.addClass( 'show-animation' );
							}, 1 );
						}
					}, inactiveDelay );

					$( 'body' ).on( 'click focus resize keyup', function() {
						isInactive = false;
					} );
				}

				function scrollPageEvent( scrollingValue ) {
					var scrollingValue = +scrollingValue || 0,
						isShowed = false;

					$( window ).on( 'scroll.cherryPopupScrollEvent resize.cherryPopupResizeEvent', function() {
						var $window          = $( window ),
							windowHeight     = $window.height(),
							documentHeight   = $( document ).height(),
							scrolledHeight   = documentHeight - windowHeight,
							scrolledProgress = Math.max( 0, Math.min( 1, $window.scrollTop() / scrolledHeight ) ) * 100;

						if ( scrolledProgress > scrollingValue ) {
							$( window ).off( 'scroll.cherryPopupScrollEvent resize.cherryPopupResizeEvent' );

							$this.removeClass( 'hide-state' );
							setTimeout( function() {
								$this.addClass( 'show-animation' );
							}, 1 );

							//$this.removeClass( 'hide-state' );
							//$this.addClass( 'show-state' );
						}
					} );
				}

			});
		},
		destroy: function() {},
		update: function( content ) {}
	};

	$.fn.cherryPopupsPlugin = function( method ) {
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method with name ' + method + ' is not exist for jQuery.cherryPopupsPlugin' );
		}
	}//end plugin

} )( jQuery );
