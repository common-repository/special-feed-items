<?php
/*
Plugin Name: Special Feed Items
Plugin URI: http://noscope.com/?p=3137
Description: Emphasize special posts by prepending a unicode character in front of noteworthy posts in your RSS feed.
Version: 1.0.1
Author: Joen Asmussen
Author URI: http://noscope.com
*/

function sfiAddSymbol($title) {

	if (is_feed()) {
	
		// Options
		$symbol = get_option('sfi_symbol');
		$category = get_option('sfi_category');
		$omit = get_option('sfi_omit'); // Are we excluding one category, or only including one?
		$cid = get_cat_id($category);
	
		// Add the symbol
		$s = "";
		if ($omit == "true") {
			
			if (!in_category($cid)) {
				$s .= $symbol." ";	
			}
			
		} else {
		
			if (in_category($cid)) {
				$s .= $symbol." ";	
			}
			
		}	
		
		// Output
		return "<![CDATA[" . $s . $title . "]]>";
			
	} else {
		return $title;
	}
}

add_filter('the_title_rss', 'sfiAddSymbol');







// Options
add_option("sfi_symbol", "★", "", "yes");
add_option("sfi_category", "asides", "", "yes");
add_option("sfi_omit", "true", "", "yes");

// Register options page
function sfi_admin_init() {
	if ( function_exists('register_setting') ) {
		register_setting('sfi_settings', 'option-1', '');
	}
}
function add_sfi_option_page() {
	global $wpdb;
	add_options_page('Special Feed Items Options', 'Special Feed Items', 8, basename(__FILE__), 'sfi_options_page');
}
add_action('admin_init', 'sfi_admin_init');
add_action('admin_menu', 'add_sfi_option_page');

// Options function
function sfi_options_page() {
	if (isset($_POST['info_update'])) {
			
		// Update the options
		$sfi_symbol = $_POST["sfi_symbol"];
		update_option("sfi_symbol", $sfi_symbol);
		$sfi_category = $_POST["sfi_category"];
		update_option("sfi_category", $sfi_category);
		$sfi_omit = $_POST["sfi_omit"];
		update_option("sfi_omit", $sfi_omit);


		// Give an updated message
		echo "<div class='updated fade'><p><strong>Special Feed Items options have been updated.</strong></p></div>";
		
	}

	// Show options page
	?>

		<div class="wrap">
		<form method="post" action="options-general.php?page=special-feed-items.php">
			<h2>Special Feed Items Settings</h2>
			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="sfi_symbol">Symbol to use for your special category:</label>
					</th>
					<td>
						<?php
						// unmulf:
						$sfi_symbol = get_option('sfi_symbol');

						echo "<input type='text' size='50' ";
						echo "name='sfi_symbol' ";
						echo "id='sfi_symbol' ";
						echo "value='".$sfi_symbol."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Default: <code>&#9733;</code>.</p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="sfi_category">Special category:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='sfi_category' ";
						echo "id='sfi_category' ";
						echo "value='".get_option('sfi_category')."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Default: <code>asides</code> (category name).</p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="sfi_omit">Include or exclude the special category:</label>
					</th>
					<td>
						<?php
						if (get_option('sfi_omit') == "false") {
							$falseselected = " selected='selected'";	
							$trueselected = "";	
						} else {
							$falseselected = "";	
							$trueselected = " selected='selected'";	
						}
						echo "<select ";
						echo "id='sfi_omit' ";
						echo "name='sfi_omit'>";
						echo "<option value='false'". $falseselected .">Include</option>";
						echo "<option value='true'".$trueselected.">Exclude</option>";
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Should the special category be the only category that has the special symbol (include), or should it be the only category that doesn't have the special symbol (exclude)?</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<?php if ( function_exists('settings_fields') ) settings_fields('sfi_settings'); ?>
				<input type='submit' name='info_update' value='Save Changes' />
			</p>
		</div>
		</form>

<?php
}
?>