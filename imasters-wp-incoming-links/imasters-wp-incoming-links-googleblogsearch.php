<?php
require_once (ABSPATH . WPINC . "/feed.php");
$google_blog_search_feed = 'http://blogsearch.google.com/blogsearch_feeds?hl=en&ie=utf-8&num='. get_option('imasters_wp_incominglinks_option') .'&output=rss&q=link:';
$google_blog_search_url = 'http://blogsearch.google.com/blogsearch?hl=en&ie=utf-8&num=10&q=link:';
$myUrl = $imasters_wp_incoming_links->get_siteurl();
$rss = fetch_feed($google_blog_search_feed . $myUrl);
?>
<div class="wrap">
<?php
if ( isset($rss->items)) :
?>

    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Google Blog Search'); ?> <cite><a href="<?php echo $google_blog_search_url . $myUrl; ?>"><?php _e('More &raquo;', 'iwpil'); ?></a></cite></h2>
    <table class="widefat fixed">
        <thead>
            <tr>
                <th scope="col"><?php _e('Origin page','iwpil'); ?></th>
                <th scope="col"><?php _e('Date','iwpil'); ?></th>
                <th scope="col"><?php _e('Description','iwpil'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rss->items = array_slice($rss->items, 0, 20);
            if ( count($rss->items) == 0) : ?>
                <tr>
                <td colspan="3"><?php _e('Any log register inserted in Google Blog Search','iwpil'); ?></td>
                </tr>
            <?php
            else :
                foreach ($rss->items as $item ) : ?>
                    <tr>
                        <td>
                            <ul>
                                <li><a href="<?php echo wp_filter_kses($item['link']); ?>"><?php echo wptexturize(wp_specialchars($item['title'])); ?></a></li>
                            </ul>
                        </td>
                        <td><?php echo mysql2date( 'F j, Y', $item['dc']['date'] ); ?></td>
                        <td><?php echo $item['description']; ?></td>
                    </tr>
                <?php endforeach ?>
        </tbody>
        <?php endif ?>
    </table>
    <?php else :?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-incoming-links/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Google Blog Search'); ?></h2>
    <tbody>
    <tr>
    <div class="updated fade"><p><?php _e('Any log register inserted in Google Blog Search','iwpil'); ?></p></div>
    </tr>
    </tbody>
    <?php endif ?>
    <?php
    add_action('activity_box_end', array('jj_gblogsearch','jj_preview_gblog_search_links'));
    ?>
</div>