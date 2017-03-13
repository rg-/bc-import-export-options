<?php 

/*
 * Plugin Name: BC Import Export Options
 * Version: 1.0.0
 * Plugin URI: http://rgdesign.org
 * Description: Export & Import options only for Wordpress, ACF, Woocommerce and so on compatible.
 * Author: Roberto Garc&iacute;a
 * Author URI: http://rgdesign.org
 * Requires at least: 4.7.2
 * Tested up to: 4.7.2
 *
 * Text Domain: bc-ieo
 * Domain Path: /language/
 *
 * @package WordPress
 * @author Roberto García
 * @since 1.0.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('BCIEO_VERSION', '1.0.0' );
define('BCIEO_BASENAME', dirname( plugin_basename(__FILE__) ));

add_action('plugins_loaded', 'acfgfs_load_textdomain'); 
function acfgfs_load_textdomain() {
	load_plugin_textdomain( 'bc-ieo', false, BCIEO_BASENAME . '/language/' );
} 

global $ieo_urls;
$ieo_urls = array();
$ieo_urls['main'] = 'admin.php?page=bc-import-export-options';
$ieo_urls['export'] = 'admin.php?page=bc-export-options';
$ieo_urls['export-acf'] = 'admin.php?page=bc-export-options&select=acf';
$ieo_urls['import'] = 'admin.php?page=bc-import-options';

$acf_enabled = false;
if (function_exists('get_field')) {
	$acf_enabled = true;
}
define('BCIEO_ACF', $acf_enabled);

/* commons */ 

function bc_get_all_acf_options(){ 
	$options_select_merge = array();
	$options_objects = get_field_objects('options');
	foreach($options_objects as $k=>$v){ 
		$val_count = 0;
		if(is_array($v['value'])){ 
			$values = $v['value'][0];
			
			foreach($values as $kk=>$vv){
				$sub_key = 'options_'.$k.'_'.$val_count.'_'.$kk.''; 
				$key_merge = $sub_key;
				$value_marge = $vv;
				$options_select_merge[$key_merge] = $value_marge;
				
			} 
		}else{
			$key_merge = 'options_'.$k;
			$value_marge = $v['value']; 			
			$options_select_merge[$key_merge] = $value_marge;
		} 
			$val_count++;
	}
	return $options_select_merge;
}

function bc_i_e_page_wrapper_fx($where='start',$args=null){
	global $ieo_urls;
	if( !$where || $where=='start' ){
		
		$out = '<div class="wrap">';
		$out .= '<h2><span class="optionspage-icon"><img src="'.plugins_url("exchange.png", __FILE__).'"/></span> '.__("Import & Export Options","bc-ieo").' </h2>';
	
		if( $args && is_array($args) ){
			
			$type = $args['type'];
			$class = $args['class'] ? $args['class'] : '';
			$content = $args['content'] ? $args['content'] : '';
			if($type=='notice'){
				$out .= '<div class="notice '.$class.'">'.$content.'</div>';
			}
			
		}
		
		$out .= '<div id="poststuff">';
		$out .= '<div id="post-body" class="metabox-holder columns-2">';
		$out .= '<div id="post-body-content">';
		$out .= '<div class="meta-box-sortables ui-sortable">';
	
	}
	
	if( $where=='end' ){
		
		$out = '</div><!-- /.meta-box-sortables -->';
		$out .= '</div><!-- /#post-body-content -->';
		
		ob_start();
		do_action('bc_i_e_page_wrapper_aside');
		$out .= ob_get_contents();
		ob_end_clean(); 
		
		$out .= '</div><!-- /#post-body -->';
		$out .= '</div><!-- /#poststuff -->';
		$out .= '</div><!-- /.wrap -->';
		
	}
	
	echo $out;
}

add_action('bc_i_e_page_wrapper','bc_i_e_page_wrapper_fx',10,2); 

function bc_i_e_page_wrapper_aside_fx(){
	global $ieo_urls;
	?>
	
	<div id="postbox-container-1" class="postbox-container">
		<div class="meta-box-sortables">
			
			<div class="postbox">
				<h2><span><?php _e("About this tool","bc-ieo"); ?></span></h2>
				<div class="inside">
					<p><?php _e("Plugin version: ","bc-ieo"); echo BCIEO_VERSION;  ?></p>
					<p><?php _e("This plugins is under development, if you find bugs, problems, doubts, anything, just contact me here: roberto@rgdesign.org.","bc-ieo"); ?></p>
					<p><?php _e("Thanks :)!","bc-ieo"); ?></p>
				</div>
			</div>
			
			<div class="postbox">
				<h2><span><?php _e("Quick links","bc-ieo"); ?></span></h2>
				<div class="inside">
					<ul>
						<li><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['main']; ?>"><?php _e("Back to Main page","bc-ieo"); ?></a></li>
						<li><b>Export</b></li>
						<li><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['export']; ?>"><?php _e("Export All Options","bc-ieo"); ?></a></li>
						<?php if(BCIEO_ACF){?>
							<li><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['export-acf']; ?>"><?php _e("Export ACF Options","bc-ieo"); ?></a></li>
						<?php } ?>
						<li><b>Import</b></li>
						<li><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['import']; ?>"><?php _e("Import Options","bc-ieo"); ?></a></li>
					</ul>
				</div>
			</div>
			
		</div>
	</div>
	
	<?php
	
}
add_action('bc_i_e_page_wrapper_aside','bc_i_e_page_wrapper_aside_fx'); 

/* commons END */ 



function bc_import_export_init() {
	 
	
	add_menu_page(__("Import & Export Options","bc-ieo"), __("Import & Export Options","bc-ieo"), 'activate_plugins', 'bc-import-export-options', 'bc_import_export_options_page', plugins_url('exchange.png', __FILE__));
	
	add_submenu_page('bc-import-export-options', __("Export Options","bc-ieo"), __("Export Options","bc-ieo"), 'activate_plugins', 'bc-export-options', 'bc_import_export_options_page_export');
	add_submenu_page('bc-import-export-options', __("Import Options","bc-ieo"), __("Import Options","bc-ieo"), 'activate_plugins', 'bc-import-options', 'bc_import_export_options_page_import');
		
}

ob_start(); 

function bc_import_export_options_page(){
	global $ieo_urls;
	$args = array(); 
	$args['type'] = 'notice';
	$args['class'] = 'notice-info';
	$args['content'] = '<p>'.__("This tool Exports <b>ONLY OPTIONS</b> from the 'options' database table.","bc-ieo").'</p>';
	
	do_action('bc_i_e_page_wrapper','start',$args);
	
	?> 
	
		<div class="postbox">
			<h2><span><?php _e("Export","bc-ieo"); ?></span></h2>
			<div class="inside">
				<p><?php _e("You can choose to export all options, select by plugin (ex: only ACF options, only Woocommerce options), or select just the ones you need. A JSON file will be created with those options and their values.","bc-ieo"); ?></p>
				<p><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['export']; ?>"><?php _e("Export ALL Options","bc-ieo"); ?></a></p>
				<?php if(BCIEO_ACF) { ?><p><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['export-acf']; ?>"><?php _e("Export <b>ACF</b> Options Only","bc-ieo"); ?></a></p>
				<?php } ?>
			</div>
		</div>
		
		<div class="postbox">
			<h2><span><?php _e("Import","bc-ieo"); ?></span></h2>
			<div class="inside">
				<p><?php _e("Take the JSON file and Import (using this tool) into another WP install. You can also use this as a backup for those options, or as an options starter for some particular options you choose. You can even edit the JSON file to quickly import (and update) some, or all, options already created","bc-ieo"); ?>.</p>
				<p><span class="dashicons dashicons-arrow-right"></span> <a href="<?php echo $ieo_urls['import']; ?>"><?php _e("Go to the Import Options Page","bc-ieo"); ?></a></p>
			</div>
		</div>	
	
	<?php
	
	do_action('bc_i_e_page_wrapper','end');
	
} 

function bc_import_export_options_page_export(){
	global $ieo_urls;
	
	$use_selected_all = true;
	$use_selected_acf = false;  
	$use_selected_woo = false;
	
	if( isset($_GET['select']) == 'acf' && BCIEO_ACF ){
		$use_selected_all = false;
		$use_selected_acf = true;
	}  
	
	if (!isset($_POST['export'])) { 
		do_action('bc_i_e_page_wrapper','start');
		?>
		<div class="postbox">
			<h2 class="hndlse"><span><?php _e("Export Options","bc-ieo"); ?></span></h2>
			<div class="inside">
				<p><?php _e("A JSON file will be created with all the option names and values saved for this setup, this options do NOT include post, post types, pages, menus, widgets, comments and so on.","bc-ieo"); ?></p>
				<p><?php _e("This export file is only intended to use with the Import Options feature also provided.","bc-ieo"); ?></p>
				<p><?php _e("You can choose which options to export and then import on some other setup, ex, moving from dev to production or cloning some configuration to another setup.","bc-ieo"); ?></p>
				
				<p><a href="#" class="switch-checboxes-all"><?php _e("Select All","bc-ieo"); ?></a> | <a href="#" class="switch-checboxes-none"><?php _e("Unselect All","bc-ieo"); ?></a> | | <a href="#" class="toggle-selected" data-switch="<?php _e("Show unselected","bc-ieo"); ?>"><?php _e("Hidde unselected","bc-ieo"); ?></a></p>
				<form method='post'>
				
					<table class="table-hooks">
					<?php
					
						if($use_selected_all){
							
							$options = wp_load_alloptions(); // Get all options data, return array  
							foreach ($options as $key => $value) {
								//$value = maybe_unserialize($value); 
								?>
								<tr><th scope="row"><label><input type="checkbox" name="_is_<?php echo $key; ?>" id="_is_<?php echo $key; ?>" checked="checked" /> <b><?php echo $key; ?></label></b></th> <td><input class="regular-text all-options" readonly type="text" name="_<?php echo $key; ?>" id="_<?php echo $key; ?>" value='<?php echo $value;?>'/></td></tr>
								<?php 
							} 
							
						}else{
							
							if($use_selected_acf){
								
								$options = bc_get_all_acf_options();
								foreach($options as $key => $value){
									//$value = maybe_unserialize($value); 
									?>
									<tr><th scope="row"><label><input type="checkbox" name="_is_<?php echo $key; ?>" id="_is_<?php echo $key; ?>" checked="checked" /> <b><?php echo $key; ?></label></b></th> <td><input class="regular-text all-options" readonly type="text" name="_<?php echo $key; ?>" id="_<?php echo $key; ?>" value='<?php echo $value;?>'/></td></tr>
									<?php 
								}
								
							}
							
						}
					
					?>
					</table> 
					
					<p class="submit">
						<?php wp_nonce_field('ieo-export'); ?>
						<input class="button button-primary button-large" type='submit' name='export' value='<?php _e('Export selected options','bc-ieo');?>'/>
					</p>
				</form>
			</div>
		</div>
		<?php
		do_action('bc_i_e_page_wrapper','end');
		
	}elseif ( check_admin_referer('ieo-export') ) {
		
		$blogname = str_replace(" ", "", get_option('blogname'));
		$date = date("m-d-Y");
		$json_name = $blogname."-".$date; 
		 
		if($use_selected_all){
			$options = wp_load_alloptions(); 
			foreach ($options as $key => $value) {
				$value = maybe_unserialize($value);
				if(isset($_POST['_is_'.$key.''])){
					$need_options[$key] = $value;
				}
			} 
			$json_file = json_encode($need_options); 
		}else{
			if($use_selected_acf){
				$options = bc_get_all_acf_options(); 
				foreach($options as $key => $value) {
					$value = maybe_unserialize($value);
					if(isset($_POST['_is_'.$key.''])){
						$need_options[$key] = $value;
					}
				}
				$json_file = json_encode($need_options);
			}
		} 
		ob_clean();
		
		echo $json_file;
		header("Content-Type: text/json; charset=" . get_option( 'blog_charset'));
		header("Content-Disposition: attachment; filename=$json_name.json");
		exit();
		
	}
}

function bc_import_export_options_page_import(){
	global $ieo_urls;
	$error = false;
	if (isset($_FILES['import']) && check_admin_referer('ieo-import')) {
		
		$args = array();
		$args['type'] = 'notice';
		
		if ($_FILES['import']['error'] > 0) { 
			$args['class'] = 'notice-error';
			$args['content'] = '<p>'.__("Something went wrong :(.","bc-ieo").'</p>'; 
			$error = true; 
		}else{ 
			
			$file_name = $_FILES['import']['name'];
			$file_ext = strtolower(end(explode(".", $file_name)));
			$file_size = $_FILES['import']['size'];
			
			if (($file_ext == "json") && ($file_size < 500000)) {
				
				$encode_options = file_get_contents($_FILES['import']['tmp_name']);
				$options = json_decode($encode_options, true);
				foreach ($options as $key => $value) {
					update_option($key, $value);	
				}
				
				$args['class'] = 'notice-success'; 
				$args['content'] = '<p>'.__("All options where created/updated.","bc-ieo").'</p>'; 
				
			}else{
				
				$args['class'] = 'notice-error'; 
				$args['content'] = '<p>'.__("Invalid file or file size too big.","bc-ieo").'</p>'; 
				
			}
			
		}
		
		do_action('bc_i_e_page_wrapper','start',$args);
		
		?>
		
		<div class="postbox">
			<h2><span>Import Options</span></h2>
			<div class="inside">
				<?php
				
					if(!empty($options)){
						?>
						<p><?php _e("This is the list for all options keys and values imported.","bc-ieo"); ?></p>
						<table class="table-hooks">
						<?php
						foreach($options as $key => $value){ 
							?>
							<tr><th scope="row"><label><!--<input type="checkbox" name="_is_<?php echo $key; ?>" id="_is_<?php echo $key; ?>" checked="checked" /> --><b><?php echo $key; ?></label></b></th> <td><input class="regular-text all-options" readonly type="text" name="_<?php echo $key; ?>" id="_<?php echo $key; ?>" value="<?php echo $value;?>"/></td></tr>
							<?php 
						}
						?>
						</table>
						<p><a class="button button-primary" href="<?php echo $ieo_urls['import']; ?>"><?php _e("Import Again?","bc-ieo"); ?></a></p>
						<?php
					}
				
					if($error){
						
						?>
						<p><a class="button button-primary" href="<?php echo $ieo_urls['import']; ?>"><?php _e("Try Again","bc-ieo"); ?></a></p>
						<?php
						
						wp_die("Error happens");
						
					}
				?>
			</div>
		</div>
		
		
		<?php
		do_action('bc_i_e_page_wrapper','end');
		
	}else{
		
		do_action('bc_i_e_page_wrapper','start');
		?>
		<div class="postbox">
			<h2><span><?php _e("Import Options","bc-ieo"); ?></span></h2>
			<div class="inside">
				<p><?php _e("Click Browse button and choose a json file that you backup before.","bc-ieo"); ?></p>
				<p><?php _e("Press Import button, Wordpress do the rest for you.","bc-ieo"); ?></p>
				<form method='post' enctype='multipart/form-data'>
					<p class="submit">
						<?php wp_nonce_field('ieo-import'); ?>
						<input type="file" name="import" />
						<input type="submit" name="submit" value="<?php _e("Import","bc-ieo"); ?>" class="button button-primary"/>
					</p>
				</form> 
			</div>
		</div>
		<?php
		do_action('bc_i_e_page_wrapper','end');
	}
	
}

add_action('admin_menu', 'bc_import_export_init',10);

/*

	Scripts & Styles added

*/

function bc_ieo_register_sub_pages_styles(){
	
	?>
	<style>
	
		.toplevel_page_bc-import-export-options .wp-menu-image img{
			padding: 6px 0 0!important;
			width:70%;
			height:auto;
		}
	
		.table-hooks{
			
		}
		.table-hooks th, .table-hooks td{
			
		}
		.table-hooks th{
			text-align:left;
			padding:5px 25px 5px 5px;
		}
		.table-hooks td{
			text-align:right;
			padding:5px;
		}
		.table-hooks th input+b{
			color:red;
		}
		.table-hooks th input:checked+b{
			color:green;
		}
		.table-hooks tr:nth-child(odd) {
		   background-color: #fff;
		}
	</style> 
	<?php
	
}
add_action('admin_head', 'bc_ieo_register_sub_pages_styles');

function bc_ieo_register_sub_pages_scripts(){
	
	?>
	<script>
		jQuery(document).ready(function(){
			
			function restoreToggleUnselected(){
				if(jQuery('.toggle-selected').hasClass('selected')){
					jQuery('.toggle-selected').html( jQuery('.toggle-selected').attr('data-original') );
					jQuery('.toggle-selected').removeClass('selected');
					jQuery('.table-hooks tr').fadeIn();
				}
			}
			
			jQuery('.switch-checboxes-all').on('click',function(){
				jQuery('.table-hooks input:checkbox').attr('checked','checked');
				restoreToggleUnselected();
				return false;
			});
			jQuery('.switch-checboxes-none').on('click',function(){
				jQuery('.table-hooks input:checkbox').removeAttr('checked');
				restoreToggleUnselected();
				return false;
			});
			
			jQuery('.toggle-selected').attr('data-original',jQuery('.toggle-selected').html());
			jQuery('.toggle-selected').on('click',function(){
				if( jQuery('.table-hooks input:checkbox:checked').length>0 && jQuery('.table-hooks input:checkbox:checked').length != jQuery('.table-hooks input:checkbox').length ){
					if(!jQuery(this).hasClass('selected')){
						jQuery(this).html( jQuery(this).attr('data-switch') );
						jQuery(this).addClass('selected');
						jQuery('.table-hooks tr').fadeOut(0);
						jQuery('.table-hooks input:checkbox:checked').parent().parent().parent().fadeIn();
					}else{
						jQuery(this).html( jQuery(this).attr('data-original') );
						jQuery(this).removeClass('selected');
						jQuery('.table-hooks tr').fadeIn();
					}
				}
				return false;
			});
			 
			
		})
	</script> 
	<?php
	
}
add_action('admin_footer', 'bc_ieo_register_sub_pages_scripts');

?>