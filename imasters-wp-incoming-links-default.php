<?php
    if( isset( $_POST['clearlog'] ) ) :
        IMASTERS_WP_Incoming_Links::truncate_logs();
    endif;

    // Instance the object pagination
    include 'assets/includes/Pagination.class.php' ;
    $objPagination = new Pagination;
    // Get the current page
    $current_page = ( isset($_GET['paged']) and !empty($_GET['paged']) ) ? (int)$_GET['paged'] : 1;
    $objPagination->current_page = $current_page;
    // Get how many records to show a time
    $objPagination->items_per_page = $imasters_wp_incoming_links->itens_per_page;

    $objIncominglinks = $imasters_wp_incoming_links->get_incoming_links( empty($_GET['iwpil-filter-time']) ? '' : $_GET['iwpil-filter-time'], $objPagination->get_sql_limit() );
    $total_item_logs = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
    $objPagination->set_total_items( $total_item_logs );

    if( isset($objIncominglinks) ) :
?>

<div class="wrap">
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('iMasters WP Incoming Links','iwpil'); ?></h2>
    <form id="iwpil" method="get" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <div class="tablenav">
            <div class="alignleft actions">
                <select id="iwpil-filter-time" name="iwpil-filter-time">
                    <option value="all"<?php echo ( isset( $_GET['iwpil-filter-time'] ) and $_GET['iwpil-filter-time'] == 'all' ) ? 'selected="selected"' : ''; ?>> <?php _e(' Show incoming links registered at: ','iwpil'); ?> </option>
                    <option value="all"<?php echo ( isset( $_GET['iwpil-filter-time'] ) and $_GET['iwpil-filter-time'] == 'all' ) ? 'selected="selected"' : ''; ?>> <?php _e('All','iwpil'); ?> </option>
                    <option value="24h"<?php echo ( isset( $_GET['iwpil-filter-time'] ) and $_GET['iwpil-filter-time'] == '24h' ) ? 'selected="selected"' : ''; ?>> <?php _e('24h','iwpil'); ?> </option>
                    <option value="1w"<?php echo ( isset( $_GET['iwpil-filter-time'] ) and $_GET['iwpil-filter-time'] == '1w' ) ? 'selected="selected"' : ''; ?>> <?php _e('1 week','iwpil'); ?> </option>
                    <option value="1m"<?php echo ( isset( $_GET['iwpil-filter-time'] ) and $_GET['iwpil-filter-time'] == '1m' ) ? 'selected="selected"' : ''; ?>> <?php _e('1 month','iwpil'); ?> </option>
                </select>
                <input type="submit" class="button-secondary action" value="<?php _e( 'Filter', 'iwpil' ) ?>" />
                <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
            </div>
            <!--Pagination -->
            <div>
                <?php if($objIncominglinks):
                    //echo $objPagination->get_meta_information();
                    echo $objPagination->get_navigation();
                else :
                    echo "";
                endif; ?>
            </div>
            <!--end Pagination -->
        </div>
        
        <table class="widefat">
            <thead>
                <tr>
                    <?php if ( $imasters_wp_incoming_links->is_showing_all() ) : ?><th scope="col"><?php _e('Date','iwpil'); ?></th><?php endif; ?>
                    <?php if ( !$imasters_wp_incoming_links->is_showing_all() ) : ?><th scope="col"><?php _e('Times','iwpil'); ?></th><?php endif; ?>
                    <th scope="col"><?php _e('Origin page','iwpil'); ?></th>
                    <th scope="col"><?php _e('Target page','iwpil'); ?></th>
                    <?php if ( $imasters_wp_incoming_links->is_showing_all() ) : ?><th scope="col">IP</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if($objIncominglinks):
                    foreach($objIncominglinks as $objIncominglink): ?>
                    <tr>
                        <?php if ( $imasters_wp_incoming_links->is_showing_all() ) : ?><td><?php echo date( get_option( 'date_format' ), strtotime($objIncominglink->incoming_links_registered_at) ); ?> </td><?php endif;?>
                        <?php if ( !$imasters_wp_incoming_links->is_showing_all() ) : ?><td><?php echo $objIncominglink->total; ?></td><?php endif; ?>
                        <td><a href="<?php echo $objIncominglink->incoming_links_origin_page; ?>"><?php echo $imasters_wp_incoming_links->get_origin_page($objIncominglink->incoming_links_origin_page); ?></a></td>
                        <td><a href="<?php echo $objIncominglink->incoming_links_target_page; ?>"><?php echo $objIncominglink->incoming_links_target_page; ?></a></td>
                        <?php if ( $imasters_wp_incoming_links->is_showing_all() ) : ?><td><?php echo $objIncominglink->incoming_links_ip; ?> </td> <?php endif; ?>
                    </tr>
                    <?php
                    endforeach;
                else : ?>
                    <tr>
                        <td colspan="4"><?php _e('Any log registed yet...','iwpil'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
      </form>
  <!--Pagination -->
  <?php
        if($objIncominglinks): ?>
        <div class="tablenav">
            <?php
            echo $objPagination->get_meta_information();
            echo $objPagination->get_navigation();
            ?>
        </div>
  <?php else :
        echo "";
        endif;?>
    <!--end Pagination -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" >
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <input id="clearlog" class="button-primary action" type="submit" name="clearlog" value="<?php _e( 'Clear Log', 'iwpil' ); ?>"/>
                    <span class="description"><?php _e( 'Clear all incomings links registered by iMasters WP Incoming links', 'iwpil' ); ?></span>
                </th>
            </tr>
        </table>
    </form>
</div>
<?php else: ?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'iMasters WP Incoming Links', 'iwpil' ); ?></h2>
    <tbody>
        <tr>
            <div class="updated fade"><p><?php _e('Any log registed yet...','iwpil'); ?></p></div>
        </tr>
    </tbody>
<?php endif ?>