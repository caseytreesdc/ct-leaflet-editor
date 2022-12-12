<?php
/**
 * Plugin Name:       Leaflet Editor
 * Description:       Edit the Leaflet, copy the HTML to the clipboard, paste it in Pardot!
 * Version:           2.0
 * Author:            Tissa Khosla - Casey Trees
 */

add_action( 'admin_menu', 'ct_generate_leaflet_menu' );

function ct_generate_leaflet_menu() {
	
	if( function_exists('acf_add_options_page') ) {

		acf_add_options_sub_page(array(
			'page_title' 	=> 'Leaflet Editor',
			'menu_title'	=> 'Editor',
			'parent_slug'	=> 'edit-leaflet',
		));
		
		$leaflet_menu_slug = 'edit-leaflet';
		
		add_menu_page( 'Leaflet Editor', 'Leaflet', 'manage_options', $leaflet_menu_slug, 'ct_handle_leaflet', 'dashicons-pressthis', 50 );
		add_submenu_page( $leaflet_menu_slug, 'Preview and Copy Leaflet HTML', 'Preview', 'read', $leaflet_menu_slug, 'ct_handle_leaflet' );
	}
}

function ct_handle_leaflet() {
	
	get_leaflet_content();
	
	?>
		<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<textarea readonly style="width: 33%;"><?php echo file_get_contents(get_template_directory()."/resources/current-leaflet.html") ?></textarea>
			<div style="margin: 2px 0; padding: 20px; width: 60%; border-radius: 4px; border: 1px solid #8c8f94;">
				<?php readfile(get_template_directory()."/resources/current-leaflet.html"); ?>
			</div>
		<div>
	<?php
}

function write_leaflet_HTML($dictionary) {
	$stream = file_get_contents(get_template_directory()."/resources/leaflet-template.html");

	foreach ($dictionary as $name => $value) {
		$stream = str_replace('%%'.$name.'%%', $value, $stream);		
	}

	file_put_contents(get_template_directory()."/resources/current-leaflet.html", $stream);
}


function get_leaflet_content() {
	$preheaderText = get_option('options_leaflet_preheader_text');
	$storyOne = get_post(get_option('options_story_1'));
	$storyTwo = get_post(get_option('options_story_2'));
	$storyThree = get_post(get_option('options_story_3'));

	$trackedLinkSnippet = "?utm_source=leaflet&utm_medium=email&utm_campaign=";

	$transferDictionary = array(
		'preheader' => $preheaderText,
		'article-1__link' => get_permalink($storyOne).$trackedLinkSnippet.get_option('options_story_1_tracked_link_campaign'),
		'article-1__alt-text' => get_post_meta(get_fields($storyOne->ID)['related_image'], '_wp_attachment_image_alt', true),
		'article-1__image' => wp_get_attachment_image_url(get_fields($storyOne->ID)['related_image'], 'full'),
		'article-1__heading' => get_post_meta($storyOne->ID, '_yoast_wpseo_opengraph-title')[0],
		'article-1__description' => get_post_meta($storyOne->ID, '_yoast_wpseo_opengraph-description')[0],
		'banner-a__link' => get_option('options_button_1_link'),
		'banner-a__alt-text' => get_post_meta(get_option('options_button_1_desktop'), '_wp_attachment_image_alt')[0],
		'banner-a__image-browser' => wp_get_attachment_image_url(get_option('options_button_1_desktop'), 'full'),
		'banner-a__image-responsive' => wp_get_attachment_image_url(get_option('options_button_1_mobile'), 'full'),
		'article-2__link' => get_permalink($storyTwo).$trackedLinkSnippet.get_option('options_story_2_tracked_link_campaign'),
		'article-2__alt-text' => get_post_meta(get_fields($storyTwo->ID)['related_image'], '_wp_attachment_image_alt', true),
		'article-2__image' => wp_get_attachment_image_url(get_fields($storyTwo->ID)['related_image'], 'full'),
		'article-2__heading' => get_post_meta($storyTwo->ID, '_yoast_wpseo_opengraph-title')[0],
		'article-2__description' => get_post_meta($storyTwo->ID, '_yoast_wpseo_opengraph-description')[0],
		'article-3__link' => get_permalink($storyThree).$trackedLinkSnippet.get_option('options_story_3_tracked_link_campaign'),
		'article-3__alt-text' => get_post_meta(get_fields($storyThree->ID)['related_image'], '_wp_attachment_image_alt', true),
		'article-3__image' => wp_get_attachment_image_url(get_fields($storyThree->ID)['related_image'], 'full'),
		'article-3__heading' => get_post_meta($storyThree->ID, '_yoast_wpseo_opengraph-title')[0],
		'article-3__description' => get_post_meta($storyThree->ID, '_yoast_wpseo_opengraph-description')[0],
		'banner-b__link' => get_option('options_button_2_link'),
		'banner-b__alt-text' => get_post_meta(get_option('options_button_2_desktop'), '_wp_attachment_image_alt')[0],
		'banner-b__image-browser' => wp_get_attachment_image_url(get_option('options_button_2_desktop'), 'full'),
		'banner-b__image-responsive' => wp_get_attachment_image_url(get_option('options_button_2_mobile'), 'full')
	);

	write_leaflet_HTML($transferDictionary);
}

// TODO: make field autocomplete based on top 3 stories in that order: this could be a button, or it exist as such by default
