( function( $ ) {
	$( '.view-more-query' ).on( 'click', function( e ) {
		e.preventDefault();

		const self = $( this );
		const queryEl = $( this ).closest( '.wp-block-query' );
		const postTemplateEl = queryEl.find( '.wp-block-post-template' );

		if ( queryEl.length && postTemplateEl.length ) {
			const block = JSON.parse( queryEl.attr( 'data-attrs' ) );
			const maxPages = block.attrs.query.pages || 0;
console.log(block);
			$.ajax( {
				url: "http://localhost/sites/wordpress/wordpress/",
				dataType: 'json html',
				data: {
					action: 'query_render_more_pagination',
					attrs: queryEl.attr( 'data-attrs' ),
					paged: queryEl.attr( 'data-paged' ),
				},
				complete( xhr ) {
					const nextPage = Number( queryEl.attr( 'data-paged' ) ) + 1;
					console.log(maxPages);
					console.log(nextPage);
					
					if ( maxPages > 0 && nextPage >= maxPages ) {
						self.remove();
					}

					queryEl.attr( 'data-paged', nextPage );
					if ( xhr.responseJSON ) {
                    	console.log( xhr.responseJSON ); // eslint-disable-line
					} else {
						const htmlEl = $( xhr.responseText );

						if ( htmlEl.length ) {
							const html = htmlEl.find( '.wp-block-post-template' ).html() || '';

							console.log(htmlEl);
							if ( html.length ) {
								postTemplateEl.append( html );
								return;
							}
						}

						self.remove();
					}
				},
			} );
		}
	} );
}( jQuery ) );