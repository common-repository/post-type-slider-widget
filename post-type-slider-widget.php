<?php 
/*
 * Plugin Name: Post Type Slider Widget
 * Description: Add slider image widget for your custom post type.
 * Version: 1.0.0
 * Author: Elsner Technologies Pvt. Ltd.
 * Author URI: http://www.elsner.com/
 * Text Domain: post-type-slider
 * Copyright: © 2017 Elsner Technologies Pvt. Ltd.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if (!defined('ABSPATH')) { exit; }

function posttype_slider_scripts() {
 
    wp_enqueue_style( 'owl-carousel-css', plugin_dir_url(__FILE__). 'assets/owl.carousel.min.css');
    wp_enqueue_style( 'owl-theme-css', plugin_dir_url(__FILE__). 'assets/owl.theme.default.min.css');
    wp_enqueue_style( 'custom-post-css', plugin_dir_url(__FILE__). 'assets/custom-post.css', array (), 1, false);
    wp_enqueue_script( 'owl-carousel-js', plugin_dir_url(__FILE__). 'assets/owl.carousel.min.js', array (), 1.1, true); 
   
 }
add_action( 'wp_enqueue_scripts', 'posttype_slider_scripts' );

class Post_Type_Slider_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'post_type_slider_widget', 
            'Post Type Slider Widget', 
            array( 'description' => __( 'A Custom Post Type Slider Widget', 'post-type-slider' ), ) 
        );
    }

    /**
     * Front-end display of widget.
     *
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        $posttype =  $instance['post_type'];
        echo $before_widget;
      
            echo $before_title . __($title, 'post-type-slider') . $after_title;

            $post_args = array(
                'post_type' => array($posttype),
                'post_status' => "publish",
                'order' => 'DESC',  
                'orderby' => 'date',
                'posts_per_page' => -1,   
                );
            $post_query = new WP_Query( $post_args );
            
            if ( $post_query->have_posts() ) : ?>
                    <div id="<?php echo $posttype; ?>" class="owl-carousel">
                        <?php while ( $post_query->have_posts() ) : $post_query->the_post();?>   
                            <div class="item">
                            <a href="<?php the_permalink(); ?>">
                            <?php $img = get_the_post_thumbnail_url($post_query->ID);  ?>
                                <img src="<?php echo ($img != '')? $img : plugin_dir_url(__FILE__).'assets/img/placeholder.png';   ?>" />
                                <span class="post-content"><?php echo substr(get_the_title(),0,90); ?></span> 
                            </a>
                            </div>
                        <?php endwhile;  wp_reset_postdata();?>
                    </div>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('#<?php echo $posttype; ?>').owlCarousel({
                        loop: true,
                        margin: 10,
                        autoplay:true,
                        autoplayTimeout:4000,
                        autoplayHoverPause:true,
                        nav: true,
                        navText: ["<img src='<?php echo plugin_dir_url(__FILE__).'assets/img/left.png'; ?>'>", "<img src='<?php echo plugin_dir_url(__FILE__).'assets/img/right.png'; ?>'>"],
                        autoWidth:false,
                        singleItem:true,
                        responsive: {
                            0: {
                                items: 1
                            },
                            600: {
                                items: 1
                            },
                            1000: {
                                items: 1
                            }
                        }
                    });
                });
            </script>
            <?php endif; 
        echo $after_widget;
    }

    /**
     * widget form values as they are saved.
     *
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['post_type'] = strip_tags( $new_instance['post_type'] );
        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
       
         $title = (isset($instance[ 'title' ]) && $instance[ 'title' ] != '')?$instance[ 'title' ]:""; 
         $posttype = (isset($instance[ 'post_type' ]) && $instance[ 'post_type' ] != '')?$instance[ 'post_type' ]:""; 

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?></label> 
            <?php
            $args = array(
                'public'   => true,
                '_builtin' => false,
            );
            $posttypes = get_post_types($args); ?>
            <select name="<?php echo $this->get_field_name( 'post_type' ); ?>">
                <option value="post" <?php echo ($posttype=="post") ? 'selected="selected"' : ''; ?>>post</option>
                <?php if(!empty($posttypes)){ ?>
                <?php foreach($posttypes as $value){ ?>
                    <option value="<?php echo $value; ?>" <?php echo ($posttype==$value) ? 'selected="selected"' : ''; ?>><?php echo $value; ?></option>
                <?php } }?>           
            </select>
        </p>
        <?php 
    }
} // class Post_Type_Slider_Widget

// register Post_Type_Slider_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "post_type_slider_widget" );' ) );