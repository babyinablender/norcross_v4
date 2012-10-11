<?php

include('lib/rkv-admin.php');
include('lib/rkv-structure.php');
include('lib/rkv-builds.php');
include('lib/rkv-types.php');
include('lib/rkv-meta.php');
include('lib/rkv-widgets.php');
include('lib/rkv-shortcodes.php');

 // Start up the engine 
class NorcrossVersionFour {


    /**
     * This is our constructor. There are many like it, but this one is mine.
     *
     * @return Norcrossv4
     */

    public function __construct() {
        add_action ( 'after_setup_theme',               array( $this, 'after_setup_theme'       )               );
        add_action ( 'wp_enqueue_scripts',              array( $this, 'scripts_styles'          ),      10      );
        add_action ( 'pre_get_posts',                   array( $this, 'ordered_sort'            )               );
        add_action ( 'wp_footer',                       array( $this, 'social_scripts'          )               );
        add_action ( 'template_redirect',               array( $this, 'redirects'               ),      1       );
        add_action ( 'gists_cron',                      array( $this, 'run_gists_cron'          )               );
        
        add_filter ( 'wp_nav_menu_items',               array( $this, 'nav_search'              ),      10, 2   );
        add_filter ( 'post_thumbnail_html',             array( $this, 'fix_thumbs'              ),      10      );
        add_filter ( 'image_send_to_editor',            array( $this, 'fix_thumbs'              ),      10      );
        add_filter ( 'nav_menu_css_class',              array( $this, 'nav_active'              ),      10, 2   );
        add_filter ( 'jpeg_quality',                    array( $this, 'jpeg_quality'            )               );
        add_filter ( 'get_avatar',                      array( $this, 'burt_avatar'             ),      10, 5   );
        add_filter ( 'body_class',                      array( $this, 'body_class'              )               );
    }

    /**
     * Set up various theme items
     *
     * @return Norcrossv4
     */

    public function after_setup_theme() {
 
        // add main menu
        register_nav_menu( 'primary', 'Primary Menu' );

        // add sidebars

        register_sidebar(array(
            'name'          => __( 'Blog Sidebar' ),
            'id'            => 'blog-sidebar',
            'description'   => __( 'Widgets for blog posts.' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="nav-header">',
            'after_title'   => '</h4>'
        ));

        register_sidebar(array(
            'name'          => __( 'Tutorials Sidebar' ),
            'id'            => 'tutorials-sidebar',
            'description'   => __( 'Widgets for tutorials pages.' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="nav-header">',
            'after_title'   => '</h4>'
        ));

        register_sidebar(array(
            'name'          => __( 'Main Sidebar' ),
            'id'            => 'main-sidebar',
            'description'   => __( 'Widgets for general use.' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="nav-header">',
            'after_title'   => '</h4>'
        ));

        // set up thumbnails
        add_theme_support   ( 'post-thumbnails' ); 
        add_image_size      ( 'blog-page', 130, 130, true );
        add_image_size      ( 'speaking', 250, 250, true );

        // add cron job for gists
        if ( !wp_next_scheduled( 'gists_cron' ) ) {
            wp_schedule_event(time(), 'twicedaily', 'gists_cron');
        }
        /*
        $timestamp = wp_next_scheduled( 'gists_cron' );
        wp_unschedule_event($timestamp, 'gists_cron' );
        */

    }


    /**
     * Add search to nav bar
     *
     * @return Norcrossv4
     */

    public function nav_search($items, $args) {
 
        ob_start();
            get_search_form();
            $searchform = ob_get_contents();
            ob_end_clean();
 
        $items .= '<li id="menu-item-0" class="menu-item search-nav">' . $searchform . '</li>';
 
        return $items;
    }

    /**
     * Set some custom classes
     *
     * @return Norcrossv4
     */


    public function body_class($classes) {
        
        if (is_page_template('page-instagram.php') ):
            $classes[] = 'instagram';
        endif;
        
    return $classes;

    }

    /**
     * Remove height/width attributes on images so they can be responsive
     *
     * @return Norcrossv4
     */

    public function fix_thumbs( $html ) {
        $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
        return $html;
    }

    /**
     * Add 'active' class to match up with bootstrap
     *
     * @return Norcrossv4
     */

    public function nav_active($classes, $item) {
        if($item->menu_item_parent == 0 && in_array('current-menu-item', $classes)) {
            $classes[] = "active";
        }
    
        return $classes;
    }

    /**
     * set image quality to 100%
     *
     * @return Norcrossv4
     */

    public function jpeg_quality($quality) {
        
        return 100;
    }

    /**
     * Load CSS and JS files
     *
     * @return Norcrossv4
     */

    public function scripts_styles() {

    // CSS first
//    wp_enqueue_style( 'bootstrap-custom', get_bloginfo('stylesheet_directory').'/lib/css/bootstrap.custom.min.css', array(), null, 'all' );
    wp_enqueue_style( 'bootstrap-custom', get_bloginfo('stylesheet_directory').'/lib/css/bootstrap.custom.css', array(), null, 'all' );
    wp_enqueue_style( 'bootstrap-core', get_bloginfo('stylesheet_directory').'/lib/css/bootstrap.responsive.min.css', array(), null, 'all' );
//    wp_enqueue_style( 'typography', get_bloginfo('stylesheet_directory').'/lib/css/typography.css', array(), null, 'all' );
    
    if (is_singular('plugins') || is_page_template('page-instagram.php')) :
        wp_enqueue_style( 'colorbox', get_bloginfo('stylesheet_directory').'/lib/css/colorbox.css', array(), null, 'all' );
        wp_enqueue_script( 'colorbox', get_bloginfo('stylesheet_directory').'/lib/js/jquery.colorbox.js', array('jquery'), null, true );
    
    endif;    

    if (is_singular('post')) :
        wp_enqueue_script( 'expander', get_bloginfo('stylesheet_directory').'/lib/js/expander.js', array('jquery'), null, true );
    
    endif; 

    // now scripts
    wp_enqueue_script( 'bootstrap', get_bloginfo('stylesheet_directory').'/lib/js/bootstrap.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'rkv-init', get_bloginfo('stylesheet_directory').'/lib/js/rkv.init.js', array('jquery'), null, true );

    }

    /**
     * sort and counts on plugin archive page 
     *
     * @return Norcrossv4
     */

    public function ordered_sort($query) {
        if (is_admin())
            return;

        if ( is_post_type_archive('plugins') && $query->is_main_query() ) {
            $query->query_vars['order']             = 'ASC';
            $query->query_vars['orderby']           = 'name';
            $query->query_vars['posts_per_page']    = -1;
            return;
        }

        if ( is_post_type_archive('snippets') && $query->is_main_query() ) {
            $query->query_vars['posts_per_page']    = 18;
            return;
        }

        if ( is_post_type_archive('speaking') && $query->is_main_query() ) {
            $query->query_vars['order']             = 'ASC';
            $query->query_vars['orderby']           = 'menu_order';
            $query->query_vars['posts_per_page']    = 4;
            return;
        }

    }

    /**
     * load social sharing scripts
     *
     * @return Norcrossv4
     */


    public function social_scripts() {
        if (is_singular(array('post', 'tutorials', 'plugins') ) ) {
    ?>

        <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
        

        <script type="text/javascript">
        (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>

    <?php } }

    /**
     * set missing gravatars to Burt Reynolds
     *
     * @return Norcrossv4
     */

    public function validate_gravatar($email) {
        // Build the Gravatar URL by hasing the email address
        $url = 'http://www.gravatar.com/avatar/' . md5( strtolower( trim ( $email ) ) ) . '?d=404';
        // Now check the headers...
        $headers = @get_headers( $url );
        // If 200 is found, the user has a Gravatar; otherwise, they don't.
        return preg_match( '|200|', $headers[0] ) ? true : false;
    }

    public function gimme_burt() {
        $burt_array = scandir( get_stylesheet_directory(__FILE__) . '/images/burt' );
        $burt_faces = array_filter( $burt_array, create_function('$x','return in_array(strtolower(substr($x,-3)),array("png","jpg","gif"));'));
        $burt_face  = array_rand(array_flip($burt_faces), 1);
        return $burt_face;
    }

    public function burt_avatar($avatar, $id_or_email, $size, $default, $alt) {
      if (is_admin() )
        return $avatar;

        if (isset($id_or_email) && $id_or_email === 1) {

            $u_id       = get_current_user_id();
            $email      = get_the_author_meta( 'user_email', $u_id );
            $grav_img   = 'http://www.gravatar.com/avatar/'.md5(strtolower($email)) . '?d=' . urlencode($default) . '&s=' . $size;
            $gravatar   = '<img src="'.$grav_img.'"/>';
            return $gravatar;

        } else {

            $email  = $id_or_email->comment_author_email;
            $exist  = $this->validate_gravatar($email);

        }

        if($exist == true ) {
            $grav_img   = 'http://www.gravatar.com/avatar/'.md5(strtolower($email)) . '?d=' . urlencode($default) . '&s=' . $size;
            $gravatar   = '<img src="'.$grav_img.'"/>';
            return $gravatar;
        } else {
            $burt_img   = $this->gimme_burt();
            $gravatar   = '<img src="'.get_stylesheet_directory_uri().'/images/burt/'.$burt_img.'" height="'.$size.'" width="'.$size.'"/>';
            return $gravatar;
        }

//        return $gravatar;
    }

    /**
     * call download redirects
     *
     * @return Norcrossv4
     */

    public function redirects() {
        global $wp_query;
        global $post_id;
        
        $dl_file    = get_post_meta($post_id, '_rkv_download_url', true);

        if(empty( $dl_file ))
            return;

        // partners redirect
        if ( is_singular('downloads') ) :
            wp_redirect( esc_url_raw( $dl_file ), 301 );
            exit();
        endif;

    }

    /**
     * helper function for getting Gists
     *
     * @return Norcrossv4
     */

    public function gist_check() {

        $args = array (
            'fields'        => 'ids',
            'post_type'     => 'snippets',
            'numberposts'   => -1,
            'meta_key'      => '_rkv_gist_id',
        );

        $gist_query = get_posts( $args );

        $gist_count = (count($gist_query) > 0 ) ? true : false;

        if($gist_count == false)
            $gist_ids[] = 0;

        if($gist_count == true) {
            foreach ($gist_query as $gist):
                $gist_ids[] = get_post_meta($gist, '_rkv_gist_id', true);
            endforeach;
        }

        return $gist_ids;

    }

    /**
     * run cron for gists
     *
     * @return Norcrossv4
     */

    public function run_gists_cron() {

        $args   = array (
            'sslverify'     => false,
        );

        // grab username and total gists to grab
        $user   = 'norcross';
        $number = 100;

        // set number of items to return
        if (!empty ($number) ) { $max = $number; } else { $max = 100; } // 100 is the max return in the GitHub API
    
        $request    = new WP_Http;
        $url        = 'https://api.github.com/users/'.urlencode($user).'/gists?&per_page='.$max.'';
        $response   = wp_remote_get ( $url, $args );

        $data_raw   = $response['body'];
        $data_array = json_decode($data_raw);

        // loop through each gist and create a post
        foreach ($data_array as $data) {

            $gist_id    = $data->id;
            $gist_check = $this->gist_check();

            if (!in_array($gist_id, $gist_check)) {
                    
                $gist_url   = $data->html_url;
                $gist_title = $data->description;

                // get and convert timestamp
                $date   = $data->created_at;
                $stamp  = strtotime($date);
                $pubdt  = date('Y-m-d H:i:s', $stamp);

                // build new snippet array
                    
                $snippet = array(
                    'post_type'     => 'snippets',
                    'post_title'    => $gist_title,
                    'post_name'     => $gist_id,
                    'post_content'  => '',
                    'post_excerpt'  => '',
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'post_date'     => $pubdt,
                );

                // add the post to the database
                $new_gist = wp_insert_post($snippet, true);

                // add some postmeta
                if (!is_wp_error($new_gist) ) {
                    add_post_meta($new_gist, '_rkv_gist_id', $gist_id);
                    add_post_meta($new_gist, '_rkv_gist_url', $gist_url);
                }

            } // end the array comparison

        } // end the foreach

    } // end the function


/// end class
}


// Instantiate our class
$NorcrossVersionFour = new NorcrossVersionFour();
