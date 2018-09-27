(function( $ ) {
	'use strict';

    (function (o, n, e, p, a, y) {
        var s = n.createElement(p);
        s.type = "text/javascript";
        s.src = e;
        s.onload = s.onreadystatechange = function () {
            if (!o && (!s.readyState
                || s.readyState === "loaded")) {
                y();
            }
        };

        var t = n.getElementsByTagName("script")[0];
        p = t.parentNode;
        p.insertBefore(s, t);
    })(false, document, "https://cdn.rawgit.com/TransbankDevelopers/transbank-sdk-js-onepay/v1.5.3/lib/merchant.onepay.min.js",
        "script",window, function () {
            console.log("Onepay JS library successfully loaded.");
            var checkout_form = jQuery( 'form.checkout' );
            checkout_form.on( 'checkout_place_order_onepay', function() {
                checkout_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' );

                if (jQuery('.woocommerce-invalid:visible').length > 0) {
                    return true;
                }

                jQuery.ajax({
					type:		'POST',
					url:		wc_checkout_params.checkout_url,
					data:		checkout_form.serialize(),
					dataType:   'json',
					success:	function( result ) {
                        try {
                            if ( 'success' === result.result ) {
                                var options = {
                                    endpoint: transaction_url,
                                    callbackUrl: commit_url
                                    };

                                if (window.commerce_url) {
                                    options.commerceLogo = window.commerce_url;
                                }
                                Onepay.checkout(options);
                            } else if ( 'failure' === result.result ) {
                                throw 'Result failure';
                            } else {
                                throw 'Invalid response';
                            }
                        } catch( err ) {
							if ( true === result.reload ) {
								window.location.reload();
								return;
                            }

                            jQuery( ".form-row:first" ).addClass( 'woocommerce-invalid' );
                            checkout_form.submit();
						}
					},
					error:	function( jqXHR, textStatus, errorThrown ) {
						wc_checkout_form.submit_error( '<div class="woocommerce-error">' + errorThrown + '</div>' );
					}
				});
                return false;
            });
        });

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
