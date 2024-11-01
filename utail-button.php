<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<script src="//<?php echo UTail::host() ?>/server/index.php/widget.js?<?php echo $query ?>"></script>
<script>
// Store current user id in session
UTail.on('ready', function() {
	jQuery.post('<?php echo admin_url( 'admin-ajax.php' ) ?>', {
		action: 'set_user_id',
		user_id: UTail.userId(),
		_wpnonce: '<?php echo wp_create_nonce( 'utail_nonce_YB8arV4S' ); ?>'
	});
});
// Auto adjust Woocommerce prices
UTail.autoAdjustPrices('body.woocommerce .product .price > .amount');
</script>
