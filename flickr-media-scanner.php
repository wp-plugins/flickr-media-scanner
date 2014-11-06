<?php

/*
 * Plugin Name: Flickr Media Scanner
 * Plugin URI: http://wordpress.org/extend/plugins/jetpack/
 * Description: Finds all Flickr photos used in posts that have been removed
 * Author: Alan Cesarini
 * Version: 1.0.3
 * Author URI: http://alancesarini.com
 * License: GPL2+
 */

$version = '1.0.3';

require( 'includes/class_scanner.php' );

add_action( 'wp_loaded', 'fms_register_assets' );

add_action( 'admin_enqueue_scripts', 'fms_enqueue_assets' );

add_action( 'admin_menu', 'add_menu_item' );

function add_menu_item() {
	$page = add_options_page( __( 'Flickr Media Scanner', 'plugin_textdomain' ), __( 'Flickr Media Scanner', 'plugin_textdomain' ), 'manage_options', 'flickr_media_scanner', 'settings_page' );
}

function settings_page() {

	if( !$flickr_api_key = get_option( 'fms_api_key' ) ) {
		if( isset( $_POST[ 'fms-api-key' ] ) )
			$flickr_api_key = sanitize_text_field( $_POST[ 'fms-api-key' ] );
		else {
			$flickr_api_key = '';
		}
	}
	$selected_category = ( isset( $_POST[ 'fms-scan-category' ] ) ? intval( $_POST[ 'fms-scan-category' ] ) : '' ); 
	$selected_tags = ( isset( $_POST[ 'fms-scan-tags' ] ) ? sanitize_text_field( $_POST[ 'fms-scan-tags' ] ) : '' );
	$categories = get_categories();
?>
	<div class="wrap">
		<h2>Flickr Media Scanner</h2>
		<form method="post" action="<?php echo admin_url( 'options-general.php?page=flickr_media_scanner.php' ) ?>">
			<table class="form-table">
				<tr>
					<th><label for="fms-api-key">Yout Flickr API key</label></th>
					<td><input type="text" name="fms-api-key" id="fms-api-key" value="<?php echo $flickr_api_key; ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th><label for="fms-scan-category">Category</label></th>
					<td>
						<select name="fms-scan-category">
							<option value="-1">--Choose a category--</option>
							<?php foreach( $categories as $category ) { ?> 
								<option value="<?php echo $category->term_id; ?>" <?php if( $category->term_id == $selected_category ) echo 'selected'; ?>><?php echo $category->name; ?></option> 
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="fms-scan-tags">Tags</label></th>
					<td><input type="text" name="fms-scan-tags" value="<?php echo $selected_tags; ?>" class="regular-text" /><span class="description"> (comma separated)</span></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="fms-scan" id="fms-scan" value="Scan now" /></td>
				</tr>
			</table>
		</form>

<?php
	if( isset( $_POST[ 'fms-scan'] ) ) {
		update_option( 'fms_api_key', sanitize_text_field( $_POST[ 'fms-api-key' ] ) );
		echo '<div class="fms-colors"><p class="fms-loading fms-label">Not yet scanned</p><p class="fms-ok fms-label">Exists in Flickr</p><p class="fms-fail fms-label">Has been removed from Flickr</p></div>';
		$args = array();
		if( $selected_category > 0 ) {
			$args[ 'category'] = $selected_category;
		}
		if( $selected_tags != '' ) {
			$args[ 'tags' ] = $selected_tags;
		}
		$scanner = new FMS_Scanner( $flickr_api_key );
		$scanner->scan( $args );
	}
	
	echo '</div>';

}

function fms_register_assets() {

	global $version;

	wp_register_script( 'fms-admin-js', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), $version );
	wp_register_style( 'fms-admin-style', plugins_url( 'assets/css/admin.css', __FILE__ ), false, $version );

}

function fms_enqueue_assets() {

	wp_enqueue_script( 'fms-admin-js' );
	wp_enqueue_style( 'fms-admin-style' );

}