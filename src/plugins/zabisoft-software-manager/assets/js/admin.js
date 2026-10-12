/* global jQuery, wp, zsmData */
( function ( $ ) {
	'use strict';

	/**
	 * Re-number the mirror rows after adding or removing one.
	 */
	function renumber() {
		$( '#zsm-mirrors .zsm-mirror' ).each( function ( index ) {
			$( this ).find( '.zsm-mirror__number' ).text( index + 1 );
		} );

		$( '#zsm-mirrors' ).data( 'next-index', $( '#zsm-mirrors .zsm-mirror' ).length );
	}

	/**
	 * Update the name attributes of a moved row.
	 *
	 * @param {jQuery} row  Row element.
	 * @param {number} index New index.
	 */
	function reindex( row, index ) {
		row.attr( 'data-index', index );

		row.find( 'input, select, textarea' ).each( function () {
			var name = $( this ).attr( 'name' );

			if ( ! name ) {
				return;
			}

			$( this ).attr( 'name', name.replace( /zsm_mirrors\[\d+\]/, 'zsm_mirrors[' + index + ']' ) );
		} );
	}

	$( function () {
		/* -----------------------------------------------------------------
		 * Download mirrors
		 * --------------------------------------------------------------- */
		var $mirrors = $( '#zsm-mirrors' );

		$( '#zsm-add-mirror' ).on( 'click', function ( event ) {
			event.preventDefault();

			var index   = $mirrors.find( '.zsm-mirror' ).length;
			var markup  = $( '#tmpl-zsm-mirror-row' ).html() || '';
			var $newRow = $( markup.replace( /\{\{INDEX\}\}/g, index ) );

			$mirrors.append( $newRow );
			renumber();
			$newRow.find( 'input[type="text"], input[type="url"]' ).first().trigger( 'focus' );
		} );

		$mirrors.on( 'click', '.zsm-mirror__remove', function ( event ) {
			event.preventDefault();

			if ( ! window.confirm( zsmData.i18n.confirmRemove ) ) {
				return;
			}

			$( this ).closest( '.zsm-mirror' ).slideUp( 150, function () {
				$( this ).remove();
				renumber();
			} );
		} );

		$mirrors.on( 'click', '.zsm-check-link', function ( event ) {
			event.preventDefault();

			var $button = $( this );
			var $row    = $button.closest( '.zsm-mirror' );
			var $url    = $row.find( '.zsm-mirror__url' );
			var $result = $row.find( '.zsm-check-result' );
			var url     = $.trim( $url.val() );

			if ( ! url ) {
				$result.removeClass( 'is-ok' ).addClass( 'is-bad' ).text( zsmData.i18n.emptyUrl );
				return;
			}

			$button.prop( 'disabled', true );
			$result.removeClass( 'is-ok is-bad' ).text( zsmData.i18n.checking );

			$.post(
				zsmData.ajaxUrl,
				{
					action: 'zsm_check_link',
					nonce: zsmData.nonce,
					url: url
				}
			)
				.done( function ( response ) {
					if ( response && response.success ) {
						$result
							.toggleClass( 'is-ok', !! response.data.ok )
							.toggleClass( 'is-bad', ! response.data.ok )
							.text( response.data.message );
					} else {
						$result.addClass( 'is-bad' ).text( zsmData.i18n.failed );
					}
				} )
				.fail( function () {
					$result.addClass( 'is-bad' ).text( zsmData.i18n.failed );
				} )
				.always( function () {
					$button.prop( 'disabled', false );
				} );
		} );

		/* -----------------------------------------------------------------
		 * Screenshots
		 * --------------------------------------------------------------- */
		var frame = null;

		$( '#zsm-add-screenshots' ).on( 'click', function ( event ) {
			event.preventDefault();

			if ( 'undefined' === typeof wp || ! wp.media ) {
				return;
			}

			if ( ! frame ) {
				frame = wp.media( {
					title: zsmData.i18n.pickImages,
					library: { type: 'image' },
					button: { text: zsmData.i18n.useImages },
					multiple: true
				} );

				frame.on( 'select', function () {
					var selection = frame.state().get( 'selection' );
					var $list     = $( '#zsm-screenshots .zsm-screenshots__list' );

					selection.each( function ( attachment ) {
						var data = attachment.toJSON();
						var src  = ( data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url );

						if ( $list.find( 'li[data-id="' + data.id + '"]' ).length ) {
							return;
						}

						$list.append(
							'<li data-id="' + data.id + '">' +
								'<img src="' + src + '" alt="">' +
								'<button type="button" class="zsm-screenshots__remove" aria-label="' + zsmData.i18n.remove + '">&times;</button>' +
								'<input type="hidden" name="zsm_screenshots[]" value="' + data.id + '">' +
							'</li>'
						);
					} );
				} );
			}

			frame.open();
		} );

		$( '#zsm-screenshots' ).on( 'click', '.zsm-screenshots__remove', function ( event ) {
			event.preventDefault();

			$( this ).closest( 'li' ).remove();
		} );

		/* -----------------------------------------------------------------
		 * Keep the order of the mirror rows meaningful after a drag.
		 * --------------------------------------------------------------- */
		if ( $.fn.sortable ) {
			$mirrors.sortable( {
				handle: '.zsm-mirror__handle',
				items: '> .zsm-mirror',
				axis: 'y',
				update: function () {
					$( '#zsm-mirrors .zsm-mirror' ).each( function ( index ) {
						reindex( $( this ), index );
					} );

					renumber();
				}
			} );
		}
	} );
} )( jQuery );
