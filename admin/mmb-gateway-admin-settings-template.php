<?php

/**
 * Provide a admin area view for the plugin
 *
 *
 * @since      1.0.0
 *
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/admin
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit;
}

if ($this->is_valid_for_use()): ?>
    <h3><?php echo $this->method_title; ?></h3>

    <?php echo (!empty($this->method_description)) ? wpautop($this->method_description) : ''; ?>

    <table class="form-table">
        <?php $this->generate_settings_html(); ?>
    </table>

    <?php
else: ?>
    <div class="inline error">
        <p>
            <strong><?php _e('Payment gateway is disabled', 'mmb-gateway-woocommerce'); ?></strong>:
            <?php _e('EVOMexico Gateway does not support your store currency.', 'mmb-gateway-woocommerce'); ?>
        </p>
    </div>
<?php endif;
