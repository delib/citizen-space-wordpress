<?php

/****************************************************
 * Sidebar widget
 ****************************************************/
class CitizenSpaceWidget extends WP_Widget {

	function CitizenSpaceWidget() {
		$widget_ops = array('classname' => 'widget_cs_search_results', 'description' => __( "Lists consultations from a Citizen Space site") );
		$this->WP_Widget('widget_cs_search_results', __('Citizen Space consultations'), $widget_ops);
		$this->alt_option_name = 'widget_cs_search_results';
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_cs_search_results', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;

        if ($instance['cs_home']) {
            $title = '<a href="' . $instance['cs_home'] . '">' . $title . '</a>';
        }
        
    ?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		
		The widget contents

		<?php echo $after_widget; ?>
    <?php
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_cs_search_results', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['cs_home'] = $new_instance['cs_home'];
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();
		return $instance;
	}
	
	function flush_widget_cache() {
		wp_cache_delete('widget_cs_search_results', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$cs_home = isset($instance['cs_home']) ? esc_attr($instance['cs_home']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('cs_home'); ?>"><?php _e('Citizen Space URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('cs_home'); ?>" name="<?php echo $this->get_field_name('cs_home'); ?>" type="text" value="<?php echo $cs_home; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of consultations to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

<?php
	}
}


function cs_search_results_widget_init() {
	register_widget('CitizenSpaceWidget');
}
// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'cs_search_results_widget_init');

?>
