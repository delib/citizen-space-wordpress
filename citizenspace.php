<?php 
/*
Plugin Name: Delib Citizen Space integration
Author: Jess Norwood
Version: 0.1
*/

include_once dirname( __FILE__ ) . '/widget.php';
include_once dirname( __FILE__ ) . '/api.php';

wp_register_style('citizenspace.css', plugin_dir_url( __FILE__ ) . 'citizenspace.css');
wp_enqueue_style('citizenspace.css');

function citizenspace_admin_actions() {  
  add_options_page('Citizen Space integration settings', 'Citizen Space', 'manage_options', 'citizenspace', 'citizenspace_admin_do');
  add_submenu_page('tools.php','Citizen Space shortcode builder', 'Citizen Space', 'manage_options', 'citizenspace', 'citizenspace_tool_do');
} 
add_action('admin_menu', 'citizenspace_admin_actions');

/****************************************************
 * Plugin Settings
 ****************************************************/
function citizenspace_admin_do() {
  if(isset($_POST['cs_submit'])) { 
     $url = $_POST['citizenspace_url'];
     if(strpos($url, 'http') !== 0) {
       $url = 'http://'.$url;
     }
      update_option('citizenspace_url', $url);
   }
   $url = get_option('citizenspace_url', '');
   ?>
<div class="wrap">  
    <h2>Citizen Space integration settings</h2>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">  
        <p><?php _e("Citizen Space URL: " ); ?><input type="text" name="citizenspace_url" value="<?php echo $url; ?>" size="40">
        
        <?php if(citizenspace_api_is_valid_url($url)) {
          echo '<span class="citizenspace-good">This looks like a valid Citizen Space site.</span>'; 
        }         
        else {
          echo '<span class="citizenspace-bad">I couldn\'t find a Citizen Space site at this URL.</span>';
        }
        ?>
        </p>  
        <p class="submit">  
        <input type="submit" name="cs_submit" value="Save" />  
        </p>  
    </form>  
</div>  
   <?php
}

/****************************************************
 * Shortcode builder
 ****************************************************/ 
function citizenspace_tool_do() {
   $url = get_option('citizenspace_url', '');
   $code = "[citizenspace_search_results]";
   $query = "";
   
   // Parse the post and build a query string from the nonempty ones
   if(isset($_POST['cs_submit'])) {
     unset($_POST['cs_submit']);
     $args = array();
     foreach($_POST as $k => $v) {
       if($v !== "") {
         $args[$k] = $v;
       }
     }
     $query = http_build_query($args);
     if($query) {
       $code = '[citizenspace_search_results query="' . $query . '"]';
     }
   }
   
   ?>
  <div class="wrap citizenspace-shortcode-builder">  
    <h2>Citizen Space shortcode builder</h2>
    <?php if(citizenspace_api_is_valid_url($url)) {
      echo '<p class="citizenspace-good">Your site is currently set to talk to <a href="'.$url.'">'.$url.'</a>, which is a valid Citizen Space site.</p>';
    }
    else { 
      echo '<p class="citizenspace-bad">Your site is not currently set to talk to a valid Citizen Space site.  Please update your settings.</p>';
      return; 
    }
    ?>

    <form class="citizenspace-search-form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
     <legend><h2>Step 1:</h2><p>Use the following options to choose the consultations you want to embed in your site:</p></legend>
       <?php echo citizenspace_api_advanced_search_fields($query) ?>
        <p class="submit">  
        <input type="submit" name="cs_submit" value="Update code" />  
        </p>
    </form>
  
    <div class="citizenspace-search-results">
        <h2>Example of embedded consultations</h2>
        <?php echo citizenspace_api_search_results($query) ?>
    </div>
    
    <?php if($code) { ?>
      <div id="citizenspace-shortcode-container">
      <h2>Step 2:</h2>Copy and paste the following code (including the square brackets) into your page or post:
      <div id="citizenspace-shortcode">
      <?php echo $code; ?>
      </div>
      </div>
    <?php }
    ?>
</div>
   <?php
}
 
/****************************************************
 * Shortcodes
 ****************************************************/ 
function citizenspace_advanced_search($atts) {
  // get the query off the query string (yes there is room for collisions here)
	$query = http_build_query($_GET);
	$ret = '<div class="citizenspace-search-form advanced"><form action="'.$_SERVER['REQUEST_URI'].'">';
	$ret .= citizenspace_api_advanced_search_fields($query);
	$ret .= '<fieldset class=""><input type="submit" value="Search"/></fieldset></form></div>';
	return $ret;
}
add_shortcode('citizenspace_advanced_search', 'citizenspace_advanced_search' );

function citizenspace_basic_search($atts) {
  // get the query off the query string (yes there is room for collisions here)
	$query = http_build_query($_GET);
	$ret = '<div class="citizenspace-search-form basic"><form action="'.$_SERVER['REQUEST_URI'].'">';
	$ret .= citizenspace_api_search_fields($query);
	$ret .= '<fieldset class=""><input type="submit" value="Search"/></fieldset></form></div>';
	return $ret;
}
add_shortcode('citizenspace_basic_search', 'citizenspace_basic_search' );

function citizenspace_search_results($atts) {
  // get the query from the shortcode attributes
  extract(shortcode_atts(array(
		'query' => '',
	), $atts ) );
	// fall back to the query string
	if(!$query) {
	  	$query = http_build_query($_GET);
	}
	
	$results = citizenspace_api_search_results($query);
  
	$ret = '<div class="citizenspace-search-results">';
	$ret .= $results;
	$ret .= '</div>';
	
	return $ret;
}
add_shortcode( 'citizenspace_search_results', 'citizenspace_search_results' );

function citizenspace_consultation($atts) {
  // get the url from the shortcode attributes
  extract(shortcode_atts(array(
		'url' => '',
	), $atts ) );

	$ret = '<div class="citizenspace-consultation">';
	$ret .= citizenspace_api_consultation_body($url);
	$ret .= '</div>';

	$ret .= '<div class="citizenspace-consultation-sidebar">';
	$ret .= citizenspace_api_consultation_sidebar($url);
	$ret .= '</div>';
	
	return $ret;
}
add_shortcode( 'citizenspace_consultation', 'citizenspace_consultation' );

/****************************************************
 * Consult_view page
 ****************************************************/ 
// Fake a post object so that we can render a page for it.
function citizenspace_consult_view() {
	global $wp_query;
  if(strpos($_SERVER['REQUEST_URI'], 'cs_consultation') !== False) {
		$id=0; // need an id
		$post = new stdClass();
			$post->ID= $id;
			$post->post_category= array('uncategorized'); //Add some categories. an array()???
			$post->post_content='[citizenspace_consultation url="'.$_GET['url'].'"]'; //The full text of the post.
			$post->post_status='publish'; //Set the status of the new post.
			$post->post_title= 'Consultation details'; //The title of your post.
			$post->post_type='page'; //Sometimes you might want to post a page.
		$wp_query->queried_object=$post;
		$wp_query->post=$post;
		$wp_query->found_posts = 1;
		$wp_query->post_count = 1;
		$wp_query->max_num_pages = 1;
		$wp_query->is_single = 1;
		$wp_query->is_404 = false;
		$wp_query->is_posts_page = 0;
		$wp_query->posts = array($post);
		$wp_query->page=true;
		$wp_query->is_post=false;
		$wp_query->page=true;
	}
}
add_action('wp', 'citizenspace_consult_view');


?>
