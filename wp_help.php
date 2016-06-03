<?php 
//upload max size in htaccess file

/*php_value upload_max_filesize 64M
php_value post_max_size 64M
php_value max_execution_time 300
php_value max_input_time 300*/
define('DISALLOW_FILE_EDIT', true);  //wp-config-disallow admin to edit file
//*)loop
    get_header();
    if ( have_posts() ) : while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    else :
        _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
    endif;
    get_sidebar();
    get_footer(); 
/*
next_post_link() – a link to the post published chronologically after the current post
previous_post_link() – a link to the post published chronologically before the current post
the_category() – the category or categories associated with the post or page being viewed
the_author() – the author of the post or page
the_content() – the main content for a post or page
the_excerpt() – the first 55 words of a post’s main content followed by an ellipsis (…) or read more link that goes to the full post. You may also use the “Excerpt” field of a post to customize the length of a particular excerpt.
the_ID() – the ID for the post or page
the_meta() – the custom fields associated with the post or page
the_shortlink() – a link to the page or post using the url of the site and the ID of the post or page
the_tags() – the tag or tags associated with the post
the_title() – the title of the post or page
the_time() – the time or date for the post or page. This can be customized using standard php date function formatting.
is_home() – Returns true if the current page is the homepage
is_admin() – Returns true if an administrator is logged in and visiting the site
is_single() – Returns true if the page is currently displaying a single post
is_page() – Returns true if the page is currently displaying a single page
is_page_template() – Can be used to determine if a page is using a specific template, for example: is_page_template('about-page.php')
is_category() – Returns true if page or post has the specified category, for example: is_category('news')
is_tag() – Returns true if a page or post has the specified tag
is_author() – Returns true if a specific author is logged in and visiting the site
is_search() – Returns true if the current page is a search results page
is_404() – Returns true if the current page does not exist
has_excerpt() – Returns true if the post or page has an excerpt

*/?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
/* * See if the current post is in category 3. 
   * If it is, the div is given the CSS class "post-category-three".
   * Otherwise, the div is given the CSS class "post".
*/
if ( in_category( 3 ) ) : ?>
<div class="post-category-three">
  <?php else : ?>
  <div class="post">
    <?php endif; ?>
    // Display the post's title.
    <h2>
      <?php the_title() ?>
    </h2>
    // Display a link to other posts by this posts author. <small>
    <?php _e( 'Posted by ', 'textdomain' ); the_author_posts_link() ?>
    </small> // Display the post's content in a div.
    <div class="entry">
      <?php the_content() ?>
    </div>
    // Display a comma separated list of the post's categories.
    <?php _e( 'Posted in ', 'textdomain' ); the_category( ', ' ); ?>
    // closes the first div box with the class of "post" or "post-cat-three" </div>
  // Stop the Loop, but allow for a "if not posts" situation
  <?php endwhile; else :
/*
 * The very first "if" tested to see if there were any posts to
 * display. This "else" part tells what do if there weren't any.
 */
_e( 'Sorry, no posts matched your criteria.', 'textdomain' );
 // Completely stop the Loop.
 endif;

//**)Automatic feed links enables post and comment RSS feeds by default.  
 add_theme_support( 'automatic-feed-links' );
 add_theme_support( 'post-formats', array( 'aside', 'gallery','link','image','quote','status','video','audio','chat' ) );


//*)You can use rewind_posts() to loop through the same query a second time. This is useful if you want to display the same query twice in different locations on a page.
    if ( have_posts() ) : while ( have_posts() ) : the_post();
        the_title();
    endwhile;
    endif;
 
    // Use rewind_posts() to use the query a second time.
    rewind_posts();
 
    // Start a new loop
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;

//*)hide admin bar
add_filter('show_admin_bar', '__return_false');
//*)Change Howdy Text

		add_filter('admin_bar_menu','change_howdy_text_toolbar');
		function change_howdy_text_toolbar($wp_admin_bar)
		{
			$getgreetings = $wp_admin_bar->get_node('my-account');
			$rpctitle = str_replace('Howdy','Namaste',$getgreetings->title);
			$wp_admin_bar->add_node(array("id"=>"my-account","title"=>$rpctitle));
		}

//*)create shortcode
		add_action('init',function(){
			add_shortcode('manage-login','login');
		});
		function login(){}
//*)Remove auto generated p tag 

		remove_filter( 'the_content', 'wpautop' );	
	
//*)Add menu page and submenu page in admin

		function theme_options_panel(){
		  add_menu_page('Membership', 'Membership', 'manage_options', 'theme-options', 'wps_theme_func');
		  add_submenu_page( 'theme-options', 'Members', 'Members', 'manage_options', 'Members', 'wps_theme_func');
		  add_submenu_page( 'theme-options', 'Membership Level', 'Membership Level', 'manage_options', 'MembershipLevel', ' wps_theme_func_settings');
		  
		}
		add_action('admin_menu', 'theme_options_panel');
		
		function wps_theme_func(){
			
			include('manage-members-admin.php');
		}
		function wps_theme_func_settings(){
			
			include('manage-membershiplevel-admin.php');
		}

//*)Featured image in admin
		add_theme_support( 'post-thumbnails' );          //featured image
		
//*)Menu in admin
		function register_my_menus() {
		  register_nav_menus(
			array(
			  'header-menu' => __( 'Header Menu' ),
			  'footer-menu' => __( 'Footer Menu' )
			)
		  );
		}
		add_action( 'init', 'register_my_menus' );	

//*)excerpt in admin
			function my_custom_init() {
				add_post_type_support( 'page', 'excerpt' );
			}
			add_action('init', 'my_custom_init');
			// Changing excerpt more
		   function new_excerpt_more($more) {
				global $post;
				return '… <a href="'. get_permalink($post->ID) . '">' . 'Read More &raquo;' . '</a>';
		   }
		   add_filter('excerpt_more', 'new_excerpt_more');
//*)search by post
		function SearchFilter($query) {
			if ($query->is_search) {
			$query->set('post_type', 'post');
			}
			return $query;
		}
		add_filter('pre_get_posts','SearchFilter');
//*)create and display custom sidebar
		register_sidebar( 
			array(
				'name'          => __( 'Home Part 1', '' ),
				'id'            => 'sidebar-1',
				'description'   => __( 'Appears in the footer section of the site.', '' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			) 
		);
		
		dynamic_sidebar('sidebar-1'); //to display in front side
		
//*)custom post

		add_action( 'init', 'codex_Portfolio_init' );
				/**
				 * Register a News post type.
				 *
				 * @link http://codex.wordpress.org/Function_Reference/register_post_type
				 */
				function codex_Portfolio_init() {
					$labels = array(
						'name'               => _x( 'Portfolios', 'post type general name', 'your-plugin-textdomain' ),
						'singular_name'      => _x( 'Portfolio', 'post type singular name', 'your-plugin-textdomain' ),
						'menu_name'          => _x( 'Portfolio', 'admin menu', 'your-plugin-textdomain' ),
						'name_admin_bar'     => _x( 'Portfolio', 'add new on admin bar', 'your-plugin-textdomain' ),
						'add_new'            => _x( 'Add New', 'Portfolio', 'your-plugin-textdomain' ),
						'add_new_item'       => __( 'Add New Portfolio', 'your-plugin-textdomain' ),
						'new_item'           => __( 'New Portfolio', 'your-plugin-textdomain' ),
						'edit_item'          => __( 'Edit Portfolio', 'your-plugin-textdomain' ),
						'view_item'          => __( 'View Portfolio', 'your-plugin-textdomain' ),
						'all_items'          => __( 'All Portfolio', 'your-plugin-textdomain' ),
						'search_items'       => __( 'Search Portfolio', 'your-plugin-textdomain' ),
						'parent_item_colon'  => __( 'Parent Portfolio:', 'your-plugin-textdomain' ),
						'not_found'          => __( 'No Portfolio found.', 'your-plugin-textdomain' ),
						'not_found_in_trash' => __( 'No Portfolio found in Trash.', 'your-plugin-textdomain' )
					);

					$args = array(
						'labels'             => $labels,
						'description'        => __( 'Description.', 'your-plugin-textdomain' ),
						'public'             => true,
						'publicly_queryable' => true,
						'show_ui'            => true,
						'show_in_menu'       => true,
						'query_var'          => true,
						'rewrite'            => array( 'slug' => 'Portfolio' ),
						'capability_type'    => 'post',
						'has_archive'        => true,
						'hierarchical'       => false,
						'menu_position'      => null,
						'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
					);

					register_post_type( 'portfolio', $args );
				}

				// create two taxonomies, News Categories and writers for the post type "book"
				add_action( 'init', 'create_portfolio_tax' );
				function create_portfolio_tax() {
					register_taxonomy(
						'Portfolio Categories',
						'portfolio',
						array(
							'label' => __( 'Portfolio Categories' ),
							'rewrite' => array( 'slug' => 'Portfolio category' ),
							'hierarchical' => true,
						)
					);
				}

				/* custom column -----Featured Image-------*/
				add_image_size('featured_preview', 55, 55, true);
				function ST4_get_featured_image($post_ID) {
					$post_thumbnail_id = get_post_thumbnail_id($post_ID);
					if ($post_thumbnail_id) {
						$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
						return $post_thumbnail_img[0];
					}
				}
				function ST4_columns_head($defaults) {
					$defaults['featured_image'] = 'Featured Image';
					return $defaults;
				}
				 
				// SHOW THE FEATURED IMAGE
				function ST4_columns_content($column_name, $post_ID) {
					if ($column_name == 'featured_image') {
						$post_featured_image = ST4_get_featured_image($post_ID);
						if ($post_featured_image) {
							echo '<img src="' . $post_featured_image . '" />';
						}
					}
				}
				add_filter('manage_posts_columns', 'ST4_columns_head');
				add_action('manage_posts_custom_column', 'ST4_columns_content', 10, 2);
				/* custom column -----/Featured Image-------*/

				/* custom column -----Category-------*/
				function ST4_columns_category($defaults) {
					$defaults['category'] = 'Categories';
					return $defaults;
				}
				function ST4_columns_category_content($column_name, $post_ID) {
					if ($column_name == 'category') {
						$post_types = get_post_types( '', 'names' ); 
						foreach ( $post_types as $post_type ) {
						  if($post_type == 'news'):
							the_terms( $post->ID, 'News Categories', ' ', ' / ' );
						  elseif($post_type == 'portfolio'):
							the_terms( $post->ID, 'Portfolio Categories', ' ', ' / ' );
						  endif;
						}
					}
				}
				add_filter('manage_posts_columns', 'ST4_columns_category');
				add_action('manage_posts_custom_column', 'ST4_columns_category_content', 10, 2);
                
                //Extra  ALL POST TYPES: posts AND custom post types
				add_filter('manage_posts_columns', 'ST4_columns_head');
				add_action('manage_posts_custom_column', 'ST4_columns_content', 10, 2);
				// ONLY WORDPRESS DEFAULT POSTS
				add_filter('manage_post_posts_columns', 'ST4_columns_head', 10);
				add_action('manage_post_posts_custom_column', 'ST4_columns_content', 10, 2);
				// ONLY WORDPRESS DEFAULT PAGES
				add_filter('manage_page_posts_columns', 'ST4_columns_head', 10);
				add_action('manage_page_posts_custom_column', 'ST4_columns_content', 10, 2);
				add_filter('manage_book_posts_columns', 'ST4_columns_book_head');
				add_action('manage_book_posts_custom_column', 'ST4_columns_book_content', 10, 2);
				// ADD TWO NEW COLUMNS
				function ST4_columns_head($defaults) {
					$defaults['first_column']  = 'First Column';
					$defaults['second_column'] = 'Second Column';
					return $defaults;
				}
				 
				function ST4_columns_content($column_name, $post_ID) {
					if ($column_name == 'first_column') {
						// First column
					}
					if ($column_name == 'second_column') {
						// Second column
					}
				}
				add_filter('manage_post_posts_columns', 'ST4_columns_remove_category');
 
				// REMOVE DEFAULT CATEGORY COLUMN
				function ST4_columns_remove_category($defaults) {
					// to get defaults column names:
					// print_r($defaults);
					unset($defaults['categories']);
					return $defaults;
				?>
  <?php /* Template Name: Portfolio */ ?>
  <?php get_header(); ?>
  <div style="background:#E9E9E9">
    <div class="container" style="margin-top:20px">
      <div class="col-lg-8">
        <?php $category = 'Portfolio Categories';
								$cats = get_categories( array( 'taxonomy' => $category, 'orderby' => 'menu_order', 'order' => 'ASC'));
								//var_dump($cats);
								echo '<a href="http://localhost/wordpress/testcustom/?page_id=98">All</a>';
								foreach($cats as $cat){ 
								 echo '<ul class="nav nav-pills">';?>
        <a href="<?php echo get_category_link( $cat->cat_ID ); ?>"><?php echo($cat->name);?> </a> <?php echo '</ul>';}?>
        <?php 
						$args = array( 'post_type' => 'portfolio', 'posts_per_page' => 10 );
						$loop = new WP_Query( $args );
						//var_dump($loop);
						if($loop->have_posts()):
							while ($loop-> have_posts() ) : $loop->the_post();?>
        <div class="row well" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <h2><a href="<?php the_permalink(); ?>">
            <?php the_title();?>
            </a></h2>
          <p> <span>By </span>
            <?php //if ( 'post' == get_post_type() ) {
											printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
												esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
												esc_attr( sprintf( __( 'View all posts by %s', '' ), get_the_author() ) ),
												get_the_author()
											);
										//}?>
            <span>on </span><a href="<?php get_permalink()?>"><?php echo get_the_date(); ?>
            <?php the_modified_time('d.m.Y');?>
            <?php //the_time();?>
            </a> <span> in </span>
            <?php //the_category(',');?>
            <?php the_terms( $post->ID, 'Portfolio Categories', ' ', ' / ' ); ?>
          </p>
          <div class="row">
            <div class="col-lg-3" style="text-align:center">
              <?php if(has_post_thumbnail()):
											the_post_thumbnail(array(200,200)); 
										endif;?>
            </div >
            <div class="col-lg-7" style="margin-left:15px">
              <?php the_content();
										//the_excerpt();?>
              <?php echo get_the_tag_list('<p>Tags: ',', ','</p>'); ?> </div>
          </div>
          <?php if(is_single()):?>
          <h3 onClick="showCommentForm()">comment here</h3>
          <div class="commentForm" style="display:none">
            <?php comments_template();?>
          </div>
          <?php endif; ?>
        </div>
        <?php  endwhile;
						endif;
						?>
      </div>
      <div class="col-lg-3 well pull-right" style="margin-left:10px">
        <div>
          <form role="search" method="get" id="searchform" class="searchform " action="<?php echo esc_url( home_url( '/' ) ); ?>" style="">
            <label class="screen-reader-text text-orange" for="s">Search for : </label>
            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s"  class="" />
            <input type="submit" id="searchsubmit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" class="btn btn-primary"/>
          </form>
        </div>
        <?php //dynamic_sidebar('sidebar');?>
      </div>
    </div>
  </div>
  <?php get_footer();?>
  <script>
				$(".tagcloud > a").addClass("btn btn-sm btn-primary");
				function showCommentForm(){
					$(".commentForm").show();
					}
				</script>
  <?php
			//add meta box
			add_action( 'add_meta_boxes', 'cd_meta_box_add' );
			function cd_meta_box_add()
			{
				add_meta_box( 'my-meta-box-id', 'My First Meta Box', 'cd_meta_box_cb', 'post', 'normal', 'high' );
			}
			?>
  <?php
			function cd_meta_box_cb()
			{
				// $post is already set, and contains an object: the WordPress post
				global $post;
				$values = get_post_custom( $post->ID );
				//var_dump($values);
				$text = isset( $values['my_meta_box_text'] ) ? $values['my_meta_box_text'][0] : '';
				$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';
				$check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';
				 
				// We'll use this nonce field later on when saving.
				wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
				?>
  <p>
    <label for="my_meta_box_text">Text Label</label>
    <input type="text" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo $text; ?>" />
  </p>
  <p>
    <label for="my_meta_box_select">Color</label>
    <select name="my_meta_box_select" id="my_meta_box_select">
      <option value="red" <?php selected( $selected, 'red' ); ?>>Red</option>
      <option value="blue" <?php selected( $selected, 'blue' ); ?>>Blue</option>
    </select>
  </p>
  <p>
    <input type="checkbox" id="my_meta_box_check" name="my_meta_box_check" <?php checked( $check, 'on' ); ?> />
    <label for="my_meta_box_check">Do not check this</label>
  </p>
  <?php    
			}
			?>
  <?php
			add_action( 'save_post', 'cd_meta_box_save' );
			function cd_meta_box_save( $post_id )
			{
				// Bail if we're doing an auto save
				if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
				 
				// if our nonce isn't there, or we can't verify it, bail
				if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
				 
				// if our current user can't edit this post, bail
				if( !current_user_can( 'edit_post' ) ) return;
				 
				// now we can actually save the data
				$allowed = array( 
					'a' => array( // on allow a tags
						'href' => array() // and those anchors can only have href attribute
					)
				);
				 
				// Make sure your data is set before trying to save it
				if( isset( $_POST['my_meta_box_text'] ) )
					update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
					 
				if( isset( $_POST['my_meta_box_select'] ) )
					update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );
					 
				// This is purely my personal preference for saving check-boxes
				$chk = isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_select'] ? 'on' : 'off';
				update_post_meta( $post_id, 'my_meta_box_check', $chk );
			}
			?>
  <?Php //***----Dashboard Widget---- ***//?>
  <?php function testDashboardWidget(){
                wp_add_dashboard_widget('TestWidget','TestWidget','testFunction');
            }
            add_action('wp_dashboard_setup','testDashboardWidget');
            function testFunction(){
                echo "hello dashboard widget";	
            }?>
  <?php 
//**----PHP Code Example to Query a WordPress Post
//Example 1
//The following code will Query the post with post id 26 and Show the title and the content.
?>
  <?php
            $post_id = 26;
            $queried_post = get_post($post_id);
            $title = $queried_post->post_title;
            echo $title;
            echo $queried_post->post_content;
            ?>
  <?php //Example 3
//Using an Array… The following code will query every post number in ‘thePostIdArray’ and show the title of those posts.
?>
  <?php $thePostIdArray = array("28","74", "82", "92"); ?>
  <?php $limit = 4 ?>
  <?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); $counter++; ?>
  <?php if ( $counter < $limit + 1 ): ?>
  <div class="post" id="post-<?php the_ID(); ?>">
    <?php $post_id = $thePostIdArray[$counter-1]; ?>
    <?php $queried_post = get_post($post_id); ?>
    <h2><?php echo $queried_post->post_title; ?></h2>
  </div>
  <?php endif; ?>
  <?php endwhile; ?>
  <?php endif; ?>
  <?php 
//How to Display the Post Content Like WordPress
//When you retrieve the post content from the database you get the unfiltered content. If you want to achieve the same output like WordPress does in its’ posts or pages then you need to apply filter to the content. You can use the following code:
?>
  <?php
            $post_id = 26;
            $queried_post = get_post($post_id);
            $content = $queried_post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            echo $content;
            ?>
  <?php 
//**----For a range of all the returned fields that you can use, check the WordPress site here.

//Find out if we are in a particular WordPress post
//Lets say you want to apply some custom tweak when a particular post is being viewed. You will need to programmatically determine when you are in this specific post (example: Post ID 2). The following snippet of code will be helpful for this:

			if (is_single("2"))
			{
			//Do your custom tweak for post whose ID is 2
			}

//You can do the same thing for pages too (5 is the page ID in this example):

			if (is_page("5"))
			{
			//Do your custom tweak for post whose ID is 2
			}

//Query X Number of Recent Posts
//You can use the “wp_get_recent_posts” function to retrieve X number of recent posts and then display them however you want to. Here is an example:


//Query 5 recent published post in descending order
			$args = array( 'numberposts' => '5', 'order' => 'DESC','post_status' => 'publish' );
			$recent_posts = wp_get_recent_posts( $args );
			foreach( $recent_posts as $recent )
			{
				echo 'Post ID: '.$recent["ID"];
				echo 'Post URL: '.get_permalink($recent["ID"]);
				echo 'Post Title: '.$recent["post_title"];
				//Do whatever else you please with this WordPress post
			}
			
			
			
			/////////////////////////
			 $args = array(
			'numberposts' => 10,
			'offset' => 0,
			'category' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'include' =>'' ,
			'exclude' => '',
			'meta_key' => '',
			'meta_value' =>'',
			'post_type' => 'post',
			'post_status' => 'draft, publish, future, pending, private',
			'suppress_filters' => true );
		
			$recent_posts = wp_get_recent_posts( $args, ARRAY_A );

			/////////////////////
?>
  <?php 
//**----Get function of wp database
?>
  <?php require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" ); ?>
  <?php global $wpdb;
            $qryGetuserDetail="SELECT * FROM ".$wpdb->prefix."UserMaster";
            $Result=$wpdb->get_results($qryGetuserDetail);
            var_dump($Result);?>
  <?php 
//**----Display Subcategories on Category Pages in WordPress 
				//If you want to show the main category also on subcategory pages, just remove the parameter depth=”1″ from the code above?>
  <?php
				if (is_category()) {
				$this_category = get_category($cat);
				}
				?>
  <?php
				if($this_category->category_parent)
				$this_category = wp_list_categories('orderby=id&show_count=0
				&title_li=&use_desc_for_title=1&child_of='.$this_category->category_parent.
				"&echo=0"); else
				$this_category = wp_list_categories('orderby=id&depth=1&show_count=0
				&title_li=&use_desc_for_title=1&child_of='.$this_category->cat_ID.
				"&echo=0");
				if ($this_category) { ?>
  <ul>
    <?php echo $this_category; ?>
  </ul>
  <?php } ?>
  <?php 
//**----Show Related Post 
	//1
	
				$tags = wp_get_post_tags($post->ID);
				if ($tags) {
					$tag_ids = array();
					foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
					
					$args=array(
						'tag__in' => $tag_ids,
						'post__not_in' => array($post->ID),
						'showposts'=>5, // Number of related posts that will be shown.
						'caller_get_posts'=>1
					);
					$my_query = new wp_query($args);
					if( $my_query->have_posts() ) {
						echo '<h3>Related Posts</h3><ul>';
						while ($my_query->have_posts()) {
							$my_query->the_post();
						?>
  <li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
    <?php the_title(); ?>
    </a></li>
  <?php
						}
						echo '</ul>';
					}
				}
				$categories = get_the_category($post->ID);
				if ($categories) {
					$category_ids = array();
					foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
					
					$args=array(
						'category__in' => $category_ids,
						'post__not_in' => array($post->ID),
						'showposts'=>5, // Number of related posts that will be shown.
						'caller_get_posts'=>1
					);
					// Rest is the same as the previous code
				}
				?>
  <?php //**----Show Popular Posts ?>
  <li>
    <h3>Popular Posts</h3>
    <ul class="bullets">
      <?php 
            $popular_posts = $wpdb->get_results("SELECT id,post_title FROM {$wpdb->prefix}posts ORDER BY comment_count DESC LIMIT 0,10");
            foreach($popular_posts as $post) {
                print "<li><a href='". get_permalink($post->id) ."'>".$post->post_title."</a></li>\n";
            }
            ?>
    </ul>
  </li>
  <?php //**----Adding Favicon to Older WordPress (4.2 or below)?>
  <link rel="icon" href="http://www.wpbeginner.com/favicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="http://www.wpbeginner.com/favicon.png" type="image/x-icon" />
  <?php 
/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
			function example_add_dashboard_widgets() {
				wp_add_dashboard_widget(
						 'example_dashboard_widget',         // Widget slug.
						 'Example Dashboard Widget',         // Title.
						 'example_dashboard_widget_function' // Display function.
					);	
			}
			add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );
			/**
			 * Create the function to output the contents of our Dashboard Widget.
			 */
			function example_dashboard_widget_function() {
				// Display whatever it is you want to show.
				echo "Hello World, I'm a great Dashboard Widget";
			}
?>
  <?php 

//**----Use wp_trim_words() to trim your text as you want
			$content = get_the_content();
			$trimmed_content = wp_trim_words( $content, 40, ' ...Read More' );
			echo $trimmed_content;
			
			$excerpt = get_the_excerpt();
			echo mb_strimwidth($excerpt, 0, 180, '...'); 
			
			function custom_excerpt_length( $length ) {
				return 20;
			}
			add_filter( 'excerpt_length', 'custom_excerpt_length');
			
?>
  <?php 

//upload file
$upload = wp_upload_bits( $_FILES['myfile']['name'], null, file_get_contents( $_FILES['myfile']['tmp_name'] ) );
echo 'Well uploaded! The path to this file is ' . $upload['file'] . ' and the url to this file is ' . $upload['url'];
?>
  <?php
query_posts(
	array(
		'post_type' => array('client','events'),
		'posts_per_page' => -1,
		'tax_query' => array(
		'relation' => 'OR',
			array(
				'taxonomy' => 'tagevents',
				'field' => 'slug',
				'terms' => 'favourite'
			),
			array(
				'taxonomy' => 'eventtags',
				'field' => 'slug',
				'terms' => 'favourite'
			)
		)
	)
);
//How to check if a page exists by url?
get_page_by_path() ;

/* insert data of contact form 7 plugin */
add_action('wpcf7_before_send_mail', 'save_form' );
 
function save_form( $wpcf7 ) {
   global $wpdb;
   $submission = WPCF7_Submission::get_instance();
   if ( $submission ) {
       $submited = array();
       $submited['title'] = $wpcf7->title();
       $submited['posted_data'] = $submission->get_posted_data();
    }
     $data = array(
   		'name'  => $submited['posted_data']['name'],
   		'email' => $submited['posted_data']['email']
   	     );
     $wpdb->insert( 'wp_tps_forms', 
		    array( 
                          'form'  => $submited['title'], 
			   'data' => serialize( $data ),
			   'date' => date('Y-m-d H:i:s')
			)
		);
}
?>
  <?php
 if ( has_post_thumbnail()) {
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
    echo '<a href="' . $large_image_url[0] . '" title="' . the_title_attribute('echo=0') . '" >';
    the_post_thumbnail('thumbnail');
    echo '</a>';
 }
 ?>
  <?php
/**
 * Show Recent Comments

 */
function bg_recent_comments($no_comments = 5, $comment_len = 80, $avatar_size = 48) {
	$comments_query = new WP_Comment_Query();
	$comments = $comments_query->query( array( 'number' => $no_comments ) );
	$comm = '';
	if ( $comments ) : foreach ( $comments as $comment ) :
		$comm .= '<li><a class="author" href="' . get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID . '">';
		$comm .= get_avatar( $comment->comment_author_email, $avatar_size );
		$comm .= get_comment_author( $comment->comment_ID ) . ':</a> ';
		$comm .= '<p>' . strip_tags( substr( apply_filters( 'get_comment_text', $comment->comment_content ), 0, $comment_len ) ) . '...</p></li>';
	endforeach; else :
		$comm .= 'No comments.';
	endif;
	echo $comm;	
}?>
  <div class="widget recent-comments">
    <h3>Recent Comments</h3>
    <?php bg_recent_comments(); ?>
  </div>
  <?php

///share

				  function social_media() {
 
if (is_single()) {
    global $post;
        echo '<div class="social-post">
        <div class="counter-twitter"><a data-related="DIY_WP_Blog" href="http://twitter.com/share" class="twitter-share-button" data-text="' . get_the_title($post->ID) . ' —" data-url="' . get_permalink($post->ID) . '" data-count="vertical">Tweet</a></div>' . "\n";
?>
  <div class="counter-fb-like">
    <div id="fb-root"></div>
    <fb:like layout="box_count" href="<?php the_permalink(); ?>" send="false" width="50" show_faces="false"></fb:like>
  </div>
  <div class="counter-google-one">
    <g:plusone size="tall" href="<?php the_permalink(); ?>"></g:plusone>
  </div>
</div>
<?php }
}
 
function java_to_bottom() {
    if (is_single(array())) { // Change the name to match the name(s) of the pages using the form ?>
<script>(function(d, s) {
  var js, fjs = d.getElementsByTagName(s)[0], load = function(url, id) {
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.src = url; js.id = id;
    fjs.parentNode.insertBefore(js, fjs);
  };
  load('//connect.facebook.net/en_US/all.js#xfbml=1', 'fbjssdk');
  load('https://apis.google.com/js/plusone.js', 'gplus1js');
  load('//platform.twitter.com/widgets.js', 'tweetjs');
}(document, 'script'));</script>
<?php } }
add_action('wp_footer', 'java_to_bottom');
				  ?>
<?php social_media(); ?>
<?php
  //send mail to users
   function email_members($post_ID)  {
    global $wpdb;
    $usersarray = $wpdb->get_results("SELECT user_email FROM $wpdb->users;");    
    $users = implode(",", $usersarray);
    mail($users, "New WordPress recipe online!", 'A new recipe have been published on http://www.wprecipes.com');
    return $post_ID;
}

add_action('publish_post', 'email_members');
?>
<?php

//dashicons.

//HTML
  echo '<div class="dashicons dashicons-search"></div>';
//CSS
//content: "\f179";
//To enable Dashicons in the front-end of WordPress add the code below to your functions.php file.

wp_enqueue_style( 'dashicons-style', get_stylesheet_uri(), array('dashicons'), '1.0' );
?>
