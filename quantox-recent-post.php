 <?php 

/*----------------------------------------------------------------------------------- 
 Plugin Name: Quantox Recent Posts Widget
 Description: This is custom Recent Post Widget
 Version: 1.0
 Author: Marko Štalmatović
 -----------------------------------------------------------------------------------*/

// Register and load custom widget
function quantox_recent_post_widget_init() {
    register_widget( 'quantox_recent_post_widget' );
}
add_action( 'widgets_init', 'quantox_recent_post_widget_init' );

// Extends a WP core class - WP_Widget for our custom widget
class quantox_recent_post_widget extends WP_Widget{
    
    public function __construct(){
        $widget_details = array(
            'classname' => 'quantox_recent_post_widget',
            'id' => 'quantox_recent_post_widget',
            'description' => 'Quantox Recent Post Widget'
        );
        parent::__construct( 'quantox_recent_post_widget', 'Quantox Recent Post Widget', $widget_details );
    }
    
    // Display our widget on Frontend
    public function widget( $args, $instance ) {
        
        if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Quantox Recent Posts' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        
        $quantox_post_per = ( ! empty( $instance['quantox_post_per'] ) ) ? absint( $instance['quantox_post_per'] ) : 3;
		if ( ! $quantox_post_per ) {
			$quantox_post_per = 3;
		}
        $thumbnail_box_checkbox = isset( $instance['thumbnail-box-checkbox'] ) ? $instance['thumbnail-box-checkbox'] : false;
        $excerpt_box_checkbox = isset( $instance['excerpt-box-checkbox'] ) ? $instance['excerpt-box-checkbox'] : false;
        $character_excerpt = ( ! empty( $instance['character_excerpt'] ) ) ? absint( $instance['character_excerpt'] ) : 15;
		if ( ! $character_excerpt ) {
			$character_excerpt = 15;
		}
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $quantox_post_per,
			'excerpt_length'      => $character_excerpt,
			'excerpt_more'        => $excerpt_box_checkbox,
			'thumbnail_filename'  => $thumbnail_box_checkbox,
			'post_status'         => 'publish',
		), $instance ) );
		if ( ! $r->have_posts() ) { return; } ?>
		<?php echo $args['before_widget']; ?>
        <?php if ( $title ) { echo $args['before_title'] . $title . $args['after_title']; }?>
		<ul>
			<?php foreach ( $r->posts as $recent_post ) : ?>
				<?php
				$post_title = get_the_title( $recent_post->ID );
				$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
				?>
				<li>
					<a href="<?php the_permalink( $recent_post->ID ); ?>"><?php echo $title ; ?></a>
					<?php if ( $thumbnail_box_checkbox ) : ?>
						<div class="post-thumbnaill"><?php echo get_the_post_thumbnail( '', $recent_post->ID ); ?></div>
					<?php endif; ?>
					
                    <?php if ( $excerpt_box_checkbox ) : ?>
						<div class="post-content"><?php echo get_the_excerpt( '', $recent_post->ID ); ?></div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $args['after_widget']; 
    }
     
    // Backend Widget Form
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'quantox_recent_post_widget_domain' );
        }
        $quantox_post_per = isset( $instance['quantox_post_per'] ) ? absint( $instance['quantox_post_per'] ) : 3;
        $excerpt_character = isset( $instance['character_excerpt'] ) ? absint( $instance['character_excerpt'] ) : 20;
        $thumbnail_box_checkbox = isset( $instance['thumbnail-box-checkbox'] ) ? (bool) $instance['thumbnail-box-checkbox'] : false;          
        $excerpt_box_checkbox = isset( $instance['excerpt-box-checkbox'] ) ? (bool) $instance['excerpt-box-checkbox'] : false; 
        $quantox_order = new WP_Query(array(
            'orderby' => $instance['quantox_order_by'],
		));
        
        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
       	
    <p>
        <label for="<?php echo $this->get_field_id( 'quantox_post_per' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'quantox_post_per' ); ?>" name="<?php echo $this->get_field_name( 'quantox_post_per' ); ?>" type="number" step="1" min="1" value="<?php echo $quantox_post_per; ?>" size="3" />
    </p>
      
    <p>
        <input class="checkbox" type="checkbox"<?php checked( $thumbnail_box_checkbox ); ?> id="<?php echo $this->get_field_id( 'thumbnail-box-checkbox' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail-box-checkbox' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'thumbnail-box-checkbox' ); ?>"><?php _e( 'Show thumbnails?' ); ?></label>
   </p>
    
    <p>
		<label>Order By: </label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'quantox_order_by' ) ); ?>">
			<option value="DESC" <?php echo ('DESC'==$quantox_order)?'selected':''; ?>>DESC</option>
			<option value="ASC" <?php echo ('ASC'==$quantox_order)?'selected':''; ?>>ASC</option>
		</select>
    </p>
        
    <p>
        <input class="checkbox" type="checkbox"<?php checked( $excerpt_box_checkbox ); ?> id="<?php echo $this->get_field_id( 'excerpt-box-checkbox' ); ?>" name="<?php echo $this->get_field_name( 'excerpt-box-checkbox' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'excerpt-box-checkbox' ); ?>"><?php _e( 'Show Excerpt?' ); ?></label>
   </p>
    
    <p>   
       <label for="<?php echo $this->get_field_id( 'character_excerpt' ); ?>"><?php _e( 'Number of character in excerpt:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'character_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'character_excerpt' ); ?>" type="number" step="1" min="1" value="<?php echo $excerpt_character; ?>" size="15" />
    </p>
        
    <?php
    }
    // Update and save a backend widget settings
    public function update( $new_instance, $old_instance ) {  
        $instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['quantox_post_per'] = (int) $new_instance['quantox_post_per'];
        $instance['character_excerpt'] = (int) $new_instance['character_excerpt'];
        $instance['thumbnail-box-checkbox'] = isset( $new_instance['thumbnail-box-checkbox'] ) ? (bool) $new_instance['thumbnail-box-checkbox'] : false;
        $instance['excerpt-box-checkbox'] = isset( $new_instance['excerpt-box-checkbox'] ) ? (bool) $new_instance['excerpt-box-checkbox'] : false;
        $instance['quantox_order_by'] = ( ! empty( $new_instance['quantox_order_by'] ) ) ? strip_tags( $new_instance['quantox_order_by'] ) : '';
        return $instance;
    }
} ?>
