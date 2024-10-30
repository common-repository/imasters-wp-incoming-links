<?php
/**
 * Check if update successfull and show message.
 */
if ( isset( $_GET['updated'] ) ) :

    if ( $_GET['updated'] ) :

        // Set up a user message
        $text_message 	= __('Settings updated successfully.', 'iwpil');
        $class_name 	= 'updated fade';
    endif;
endif;

/**
 * Show a message to user about the insertion proccess
 */
if ( !empty($text_message) ) :
?>
<div id="message" class="<?php echo $class_name; ?>">
	<p><?php echo $text_message; ?></p>
</div>
<?php endif; ?>
<div class="wrap">
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Settings','iwpil'); ?></h2>

    <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>
        <table class="form-table">
            <tr valign="top">
                <th class="check-column" scope="row">
                    <label for="imasters_wp_incominglinks_new_page"><?php _e('Open incoming links in a new page.','iwpil'); ?></label>
                </th>
                <td>
                    <input id="imasters_wp_incominglinks_new_page" type="checkbox" name="imasters_wp_incominglinks_new_page" value="1" <?php checked('1', get_option('imasters_wp_incominglinks_new_page')); ?>/>
                    <span class="description"><?php _e('Mark this option to open in a new window the origin page','iwpil'); ?>.</span>
                    <br/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="input-gbs"><?php _e('Google Blog Search Listing','iwpil'); ?> </label>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'iwpil' ); ?>" />
                        <?php ?>
                    </p>
                </th>
                <td>
                    <input id="imasters_wp_incominglinks_itens_per_page" class="regular-text" type="text" value="<?php echo get_option('imasters_wp_incominglinks_itens_per_page'); ?>" name="imasters_wp_incominglinks_itens_per_page">
                    <span class="description"><?php _e('How many links show at most','iwpil'); ?>.</span>
                    <br/>
                </td>
            </tr>
            <input type="hidden" name="action" value="update"/>
            <input type="hidden" name="page_options" value="imasters_wp_incominglinks_itens_per_page,imasters_wp_incominglinks_new_page" />
        </table>
    </form>
</div>