<?php
defined('ABSPATH') || exit;
?>

<!-- you can add the images here -->
<div id="bridge-io-collapseContainer">
    <div id="bridge-io-collapse">
        <div class="bridge-io-step"><img src="<?php echo BRIDGEAPI_IO_ASSETS_URI ?>/images/banque.png">
            <span class="bridge-io-step-text"><?php _e('Select your Bank', 'bridgeapi-io');?></span>
        </div>
        <span class="bridge-io-arrow"></span>
        <div class="bridge-io-step">
            <img src="<?php echo BRIDGEAPI_IO_ASSETS_URI ?>/images/telephone.png">
            <span class="bridge-io-step-text"><?php _e('Authenticate', 'bridgeapi-io');?></span>
        </div>
        <span class="bridge-io-arrow"></span>
        <div class="bridge-io-step">
            <img src="<?php echo BRIDGEAPI_IO_ASSETS_URI ?>/images/fermer.png">
            <span class="bridge-io-step-text"><?php _e('Validate', 'bridgeapi-io');?></span>
        </div>
        <span class="bridge-io-arrow"></span>
        <div class="bridge-io-step">
            <img src="<?php echo BRIDGEAPI_IO_ASSETS_URI ?>/images/verification-du-panier.png">
            <span class="bridge-io-step-text"><?php _e('Done!', 'bridgeapi-io');?></span></div>
    </div>
</div>

<div class="bridge-io">
	<div class="bridge-io-spinner"></div>
	<div class="bridge-io-error"></div>

	<div class="bridge-io-banks">
		<section id="bridge-io-search">
			<input type="search" id="bridge-io-search-input" placeholder="<?php _e('Search a bank', 'bridgeapi-io'); ?>">
		</section>
		<section id="bridgeapi-io-banks">
			
		</section>
        <span class="seeMore"><?php _e('Scroll down to see more banks', 'bridgeapi-io');?> <img src="<?php echo BRIDGEAPI_IO_ASSETS_URI ?>/images/down-arrow.png"></span>
	</div>

	<input type="hidden" name="bridge-io-bank" id="bridge-io-bank">
</div>