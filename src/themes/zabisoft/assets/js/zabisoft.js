/**
 * ZabiSoft front end behaviour.
 *
 * No jQuery, no dependencies, ~4 KB. Everything is progressive: if the script
 * fails to load, menus, links and content stay usable.
 *
 * @package ZabiSoft
 */
( function () {
	'use strict';

	var doc = document;

	/**
	 * Query helper.
	 *
	 * @param {string}      selector CSS selector.
	 * @param {Element|Document} scope  Root node.
	 * @return {NodeList} Nodes.
	 */
	function all( selector, scope ) {
		return ( scope || doc ).querySelectorAll( selector );
	}

	/* --------------------------------------------------------------------
	 * 1. Mobile navigation
	 * ------------------------------------------------------------------ */
	function initNavigation() {
		var toggle = doc.querySelector( '.zs-nav__toggle' );
		var menu = doc.getElementById( 'zs-primary-menu' );

		if ( ! toggle || ! menu ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var open = menu.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );

		// Submenu toggles for touch devices.
		all( '.zs-submenu-toggle', menu ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var parent = button.closest( 'li' );

				if ( ! parent ) {
					return;
				}

				var open = parent.classList.toggle( 'zs-open' );
				button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
		} );

		// Keyboard support: Escape closes every open submenu.
		menu.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key ) {
				return;
			}

			all( '.zs-open', menu ).forEach( function ( item ) {
				item.classList.remove( 'zs-open' );
			} );
		} );
	}

	/* --------------------------------------------------------------------
	 * 2. Download countdown gate
	 * ------------------------------------------------------------------ */
	function initCountdown() {
		all( '[data-zs-reveal]' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var targetId = button.getAttribute( 'data-zs-reveal' );
				var seconds = parseInt( button.getAttribute( 'data-zs-seconds' ), 10 );
				var url = button.getAttribute( 'data-zs-url' ) || '';
				var host = button.getAttribute( 'data-zs-host' ) || '';
				var box = doc.getElementById( targetId );

				if ( ! box || ! url ) {
					return;
				}

				if ( ! seconds || seconds < 1 ) {
					window.open( url, '_blank', 'noopener' );
					return;
				}

				var counter = box.querySelector( '[data-zs-counter]' );
				var links = box.querySelector( '[data-zs-links]' );
				var label = button.textContent.trim();

				button.setAttribute( 'hidden', 'hidden' );
				box.removeAttribute( 'hidden' );
				box.classList.add( 'is-active' );

				var remaining = seconds;

				if ( counter ) {
					counter.textContent = String( remaining );
				}

				var timer = window.setInterval( function () {
					remaining -= 1;

					if ( counter ) {
						counter.textContent = String( Math.max( 0, remaining ) );
					}

					if ( remaining <= 0 ) {
						window.clearInterval( timer );
						box.setAttribute( 'hidden', 'hidden' );
						box.classList.remove( 'is-active' );

						if ( links ) {
							links.innerHTML = '';

							var link = doc.createElement( 'a' );
							link.className = 'zs-btn zs-btn--download';
							link.href = url;
							link.target = '_blank';
							link.rel = 'nofollow noopener external';
							link.textContent = label + ( host ? ' — ' + host : '' );

							var note = doc.createElement( 'p' );
							note.className = 'zs-disclosure';
							note.textContent = (
								window.zabisoftData && window.zabisoftData.readyText
									? window.zabisoftData.readyText + ' '
									: ''
							) + ( host ? 'External destination: ' + host : '' );

							links.appendChild( link );
							links.appendChild( note );
							links.removeAttribute( 'hidden' );
							links.classList.add( 'is-active' );
							box.removeAttribute( 'hidden' );
							box.classList.add( 'is-active' );
						}
					}
				}, 1000 );
			} );
		} );
	}

	/* --------------------------------------------------------------------
	 * 3. Back to top
	 * ------------------------------------------------------------------ */
	function initGoTop() {
		var link = doc.querySelector( '[data-zs-go-top]' );

		if ( ! link ) {
			return;
		}

		var onScroll = function () {
			if ( window.scrollY > 400 ) {
				link.classList.add( 'is-visible' );
			} else {
				link.classList.remove( 'is-visible' );
			}
		};

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();

		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

	/* --------------------------------------------------------------------
	 * 4. Screenshot lightbox (simple, accessible, no library)
	 * ------------------------------------------------------------------ */
	function initLightbox() {
		var triggers = all( '[data-zs-lightbox]' );

		if ( ! triggers.length ) {
			return;
		}

		var overlay = doc.createElement( 'div' );
		overlay.className = 'zs-lightbox';
		overlay.setAttribute( 'hidden', 'hidden' );
		overlay.setAttribute( 'role', 'dialog' );
		overlay.setAttribute( 'aria-modal', 'true' );
		overlay.innerHTML = '<button type="button" class="zs-lightbox__close" aria-label="Close">×</button><img alt="">';
		doc.body.appendChild( overlay );

		var image = overlay.querySelector( 'img' );
		var close = overlay.querySelector( '.zs-lightbox__close' );

		function hide() {
			overlay.setAttribute( 'hidden', 'hidden' );
			doc.body.classList.remove( 'zs-lightbox-open' );
		}

		triggers.forEach( function ( trigger ) {
			trigger.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				image.src = trigger.getAttribute( 'href' );
				overlay.removeAttribute( 'hidden' );
				doc.body.classList.add( 'zs-lightbox-open' );
				close.focus();
			} );
		} );

		overlay.addEventListener( 'click', function ( event ) {
			if ( event.target === overlay ) {
				hide();
			}
		} );

		close.addEventListener( 'click', hide );

		doc.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				hide();
			}
		} );
	}

	/* --------------------------------------------------------------------
	 * 5. Smooth anchor scrolling inside software pages
	 * ------------------------------------------------------------------ */
	function initAnchors() {
		all( '.zs-toc a[href^="#"]' ).forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				var id = link.getAttribute( 'href' ).slice( 1 );
				var target = doc.getElementById( id );

				if ( ! target ) {
					return;
				}

				event.preventDefault();
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				window.history.replaceState( null, '', '#' + id );
			} );
		} );
	}

	/* --------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */
	function boot() {
		initNavigation();
		initCountdown();
		initGoTop();
		initLightbox();
		initAnchors();
	}

	if ( 'loading' === doc.readyState ) {
		doc.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
