<!-- Uninstall iMasters WP Incoming Links -->
<?php
    if( !current_user_can('install_plugins')):
        die('Access Denied');
    endif;
$base_name = plugin_basename('imasters-wp-incoming-links/imasters-wp-incoming-links.php');
$base_page = 'admin.php?page='.$base_name;
if (! empty ($_GET['mode']))
  $mode = trim($_GET['mode']);
else
  $mode = '';
$iwpil_tables = array($wpdb->imasters_wp_incoming_links);
$iwpil_settings = array('imasters_wp_incominglinks_itens_per_page','imasters_wp_incominglinks_new_page');

//Form Process
if( isset( $_POST['do'], $_POST['uninstall_iwpil_yes'] ) ) :
    echo '<div class="wrap">';
    ?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP incoming Links', 'iwpil') ?></h2>
    <?php
    switch($_POST['do']) {
        //  Uninstall iMasters WP Incoming Links
        case __('Uninstall iMasters WP incoming Links', 'iwpil') :
        if(trim($_POST['uninstall_iwpil_yes']) == 'yes') :
        echo '<h3>'.__( 'Tables', 'iwpil').'</h3>';
        echo '<ol>';
        foreach($iwpil_tables as $table) :
            $wpdb->query("DROP TABLE {$table}");
            printf(__('<li>Table \'%s\' has been deleted.</li>', 'iwpil'), "<strong><em>{$table}</em></strong>");
        endforeach;
        echo '</ol>';
        echo '<h3>'.__( 'Options', 'iwpil').'</h3>';
        echo '<ol>';
        foreach($iwpil_settings as $setting) :
            $delete_setting = delete_option($setting);
            if($delete_setting) {
            printf(__('<li>Option \'%s\' has been deleted.</li>', 'iwpil'), "<strong><em>{$setting}</em></strong>");
            }
            else {
                printf(__('<li>Error deleting Option \'%s\'.</li>', 'iwpil'), "<strong><em>{$setting}</em></strong>");
                }
        endforeach;
        echo '</ol>';
        echo '<br/>';
        $mode = 'end-UNINSTALL';
        endif;
        break;
    }
endif;
    switch($mode) {
    //  Deactivating Uninstall iMasters WP Incoming Links
    case 'end-UNINSTALL':
        $deactivate_url = 'plugins.php?action=deactivate&amp;plugin=imasters-wp-incoming-links/imasters-wp-incoming-links.php';
        if(function_exists('wp_nonce_url')) {
            $deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_imasters-wp-incoming-links/imasters-wp-incoming-links.php');
        }
    echo sprintf(__('<a href="%s" class="button-primary">Deactivate iMasters WP Incoming Links</a> Disable that plugin to conclude the uninstalling.', 'iwpil'), $deactivate_url);
    echo '</div>';
    break;
    default:
    ?>
    <!-- Uninstall iMasters WP Incoming Links -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
        <div class="wrap">
            <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP Incoming Links', 'iwpil'); ?></h2>
            <p><?php _e('Uninstaling this plugin the options and table used by iMasters WP Incoming Links will be removed.', 'iwpil'); ?></p>
            <div class="error">
            <p><?php _e('Warning:', 'iwpil'); ?>
            <?php _e('This process is irreversible. We suggest that you do a database backup first.', 'iwpil'); ?></p>
            </div>
            <table>
                <tr>
                    <td>
                    <?php _e('The following WordPress Options and Tables will be deleted:', 'iwpil'); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('WordPress Options', 'iwpil'); ?></th>
                        <th><strong><?php _e('WordPress Tables', 'iwpil'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td valign="top">
                        <ol>
                        <?php
                        foreach($iwpil_settings as $settings)
                            printf( "<li>%s</li>\n", $settings );
                        ?>
                        </ol>
                    </td>
                    <td valign="top" class="alternate">
                        <ol>
                            <?php
                            foreach( $iwpil_tables as $table_name )
                                printf( "<li>%s</li>\n", $table_name );
                            ?>
                        </ol>
                    </td>
                </tr>
            </table>
            <p>
                <input type="checkbox" name="uninstall_iwpil_yes" id="uninstall_iwpil_yes" value="yes" />
                <label for="uninstall_iwpil_yes"><?php _e('Yes. Uninstall iMasters WP Incoming links now', 'iwpil'); ?></label>
            </p>
            <p>
                <input type="submit" name="do" value="<?php _e('Uninstall iMasters WP incoming Links', 'iwpil'); ?>" class="button-primary" />
            </p>
        </div>
    </form>
<?php
}
?>