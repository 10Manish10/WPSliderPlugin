<?php

class WP_Slider_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config, $thumbnailsize ) {
		
		?>
		
		<?php 
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		$config = str_replace("&quot;", "\&quot;", $config);
		?>
		
		<h3><?php _e( 'General Options', 'wp_slider' ); ?></h3>
		
		<div id="wp-slider-id" style="display:none;"><?php echo $id; ?></div>
		<div id="wp-slider-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wp-slider-pluginfolder" style="display:none;"><?php echo WP_SLIDER_URL; ?></div>
		<div id="wp-slider-jsfolder" style="display:none;"><?php echo WP_SLIDER_URL . 'engine/'; ?></div>
		<div id="wp-slider-viewadminurl" style="display:none;"><?php echo admin_url('admin.php?page=wp_slider_show_item'); ?></div>		
		<div id="wp-slider-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
		<div id="wp-slider-ajaxnonce" style="display:none;"><?php echo wp_create_nonce( 'wp-slider-ajaxnonce' ); ?></div>
		<div id="wp-slider-saveformnonce" style="display:none;"><?php wp_nonce_field('wp-slider', 'wp-slider-saveform'); ?></div>
		<div id="wp-slider-usepostsave" style="display:none;"><?php echo get_option( 'wp_slider_usepostsave', 0 ); ?></div>		
		<div id="wp-slider-addextrabackslash" style="display:none;"><?php echo get_option( 'wp_slider_addextrabackslash', 0 ); ?></div>
		<div id="wp-slider-thumbnailsize" style="display:none;"><?php echo $thumbnailsize; ?></div>
		<?php 
			$cats = get_categories();
			$catlist = array();
			foreach ( $cats as $cat )
			{
				$catlist[] = array(
						'ID' => $cat->cat_ID,
						'cat_name' => $cat ->cat_name
				);
			}
		?>
		<div id="wp-slider-catlist" style="display:none;"><?php echo json_encode($catlist); ?></div>
		
		<?php 
		$custom_post_types = get_post_types( array('_builtin' => false), 'objects' );
	
		$custom_post_list = array();
		foreach($custom_post_types as $custom_post)
		{
			$custom_post_list[] = array(
					'name' => $custom_post->name,
					'taxonomies' => array()
				);
		}

		foreach($custom_post_list as &$custom_post)
		{
			$taxonomies = get_object_taxonomies($custom_post['name'], 'objects');			
			if (!empty($taxonomies))
			{
				
				$taxonomies_list = array();
				foreach($taxonomies as $taxonomy)
				{
					$terms = get_terms($taxonomy->name);
					
					$terms_list = array();
					foreach($terms as $term)
					{
						$terms_list[] = array(
								'name' => str_replace('"', '', str_replace("&quot;", "", $term->name)),
								'slug' => $term->slug
							);
					}

					$taxonomies_list[] = array(
							'name' => str_replace('"', '', str_replace("&quot;", "", $taxonomy->name)),
							'terms' => $terms_list
						);
				}
				
				$custom_post['taxonomies'] = $taxonomies_list;
			}
		}
		?>
		<div id="wp-slider-custompostlist" style="display:none;"><?php echo json_encode($custom_post_list); ?></div>
		
		<?php 
			$langlist = array();
			$default_lang = '';
			$currentlang = '';
			if ( get_option( 'wp_slider_supportmultilingual', 1 ) == 1 )
			{
				if (class_exists('SitePress'))
				{
					$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc');

					if ( !empty($languages) )
					{
						$default_lang = apply_filters('wpml_default_language', NULL );
						$currentlang = apply_filters('wpml_current_language', NULL );
						foreach($languages as $key => $lang)
						{
							$lang_item = array(
									'code' => $lang['code'],
									'translated_name' => $lang['translated_name']
							);
							if ($key == $default_lang)
								array_unshift($langlist, $lang_item);
							else
								array_push($langlist, $lang_item);
						}				
					}
				}
			}
		?>
		<div id="wp-slider-langlist" style="display:none;"><?php echo json_encode($langlist); ?></div>
		<div id="wp-slider-defaultlang" style="display:none;"><?php echo $default_lang; ?></div>
		<div id="wp-slider-currentlang" style="display:none;"><?php echo $currentlang; ?></div>

		<div style="margin:0 12px;">
		<table class="wp-form-table">
			<tr>
				<th><?php _e( 'Name', 'wp_slider' ); ?></th>
				<td><input name="wp-slider-name" type="text" id="wp-slider-name" value="My Slider" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php _e( 'Width', 'wp_slider' ); ?> / <?php _e( 'Height', 'wp_slider' ); ?></th>
				<td><input name="wp-slider-width" type="text" id="wp-slider-width" value="960" class="small-text" /> / <input name="wp-slider-height" type="text" id="wp-slider-height" value="540" class="small-text" /></td>
			</tr>
		</table>
		</div>
		
		<h3><?php _e( 'Designing', 'wp_slider' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wp-tab-buttons" id="wp-slider-toolbar">
			<li class="wp-tab-button step1 wp-tab-buttons-selected"><?php _e( 'Images & Videos', 'wp_slider' ); ?></li>
			<li style="display: none"></li>
			<li style="display: none"></li>
			<li style="display: none"></li>

			<!-- <li class="wp-tab-button step2"><?php _e( 'Skins', 'wp_slider' ); ?></li>
			<li class="wp-tab-button step3"><?php _e( 'Options', 'wp_slider' ); ?></li> -->
			<!-- <li class="wp-tab-button step4"><?php _e( 'Preview', 'wp_slider' ); ?></li> -->
			<li class="laststep"><input class="button button-primary" type="button" value="<?php _e( 'Save & Publish', 'wp_slider' ); ?>"></input></li>
		</ul>
				
		<ul class="wp-tabs" id="wp-slider-tabs">
			<li class="wp-tab wp-tab-selected">	
			
				<div class="wp-toolbar">	
					<input type="button" class="button" id="wp-add-image" value="<?php _e( 'Add Image', 'wp_slider' ); ?>" />
					<!-- <input type="button" class="button" id="wp-add-video" value="<?php _e( 'Add Video', 'wp_slider' ); ?>" />
					<input type="button" class="button" id="wp-add-youtube" value="<?php _e( 'Add YouTube', 'wp_slider' ); ?>" />
					<input type="button" class="button" id="wp-add-vimeo" value="<?php _e( 'Add Vimeo', 'wp_slider' ); ?>" />
					<input type="button" class="button" id="wp-add-posts" value="<?php _e( 'Add WordPress Posts', 'wp_slider' ); ?>" />
					<input type="button" class="button" id="wp-add-custompost" value="<?php _e( 'Add WooCommerce / Custom Post Type', 'wp_slider' ); ?>" />
					<label style="float:right;"><input type="button" class="button" id="wp-reverselist" value="<?php _e( 'Reverse List', 'wp_slider' ); ?>" /></label> -->
					<label style="float:right;padding-top:4px;margin-right:8px;"><input type='checkbox' id='wp-newestfirst' value='' /> Add new item to the beginning</label>
				</div>
        		
        		<ul class="wp-table" id="wp-slider-media-table">
			    </ul>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wp-tab">
				<form>
					<fieldset>
						
						<?php 
						$skins = array(
								"classic" => "Classic",
								"cube" => "Cube",
								"content" => "Content",
								"elegant" => "Elegant",
								"contentbox" => "ContentBox",
								"events" => "Events",
								"featurelist" => "FeatureList",
								"frontpage" => "Frontpage",
								"mediagallery" => "Media Gallery",
								"mediapage" => "Mediapage",
								"multirows" => "Multirows",
								"gallery" => "Gallery",
								"header" => "Header",
								"lightbox" => "Lightbox",
								"navigator" => "Navigator",
								"numbering" => "Numbering",
								"pink" => "Pink",
								"redandblack" => "Red & Black",
								"rotator" => "Rotator",
								"showcase" => "Showcase",
								"simplicity" => "Simplicity",
								"stylish" => "Stylish",
								"vertical" => "Vertical",
								"verticalnumber" => "VerticalNumber",
								"light" => "Light",
								"rightthumbs" => "Vertical Thumbnails",
								"righttabs" => "Vertical Tabs",
								"righttabsdark" => "Dark Vertical Tabs",
								"lefttabs" => "Left Side Vertical Tabs",
								"thumbnails" => "Thumbnails Slider",
								"textnavigation" => "Text Navigation",
								"simplecontrols" => "Simple Controls",
								"topcarousel" => "Top Carousel",
								"bottomcarousel" => "Bottom Carousel"
								);
						
						$skin_index = 0;
						foreach ($skins as $key => $value) {
							$skin_disabled = (WP_SLIDER_VERSION_TYPE == 'L' && $skin_index++ > 2);
						?>
							<div class="wp-tab-skin<?php if ($skin_disabled) echo " wp-slider-skin-commercial-only";?>" >
							<label><input type="radio" name="wp-slider-skin" value="<?php echo $key; ?>" selected <?php if ($skin_disabled) echo "disabled"; ?>> <?php echo $value; ?> <br /><img class="selected" style="width:300px;" src="<?php echo WP_SLIDER_URL; ?>images/<?php echo $key; ?>.jpg" /></label>
							<?php if ($skin_disabled) { ?>
								<div class="wp-slider-skin-commercial-lock"></div>
								<div class="wp-slider-skin-commercial-textblock"><div class="wp-slider-skin-commercial-text"><p>This skin is only available in Commercial Version.</p><p><a href="https://www.wp.com/wordpress-slider/order/?ref=lite" target="_blank">Upgrade to Commercial Version</a></p><p><a href="https://www.wp.com/wordpress-slider/examples/?ref=lite" target="_blank">View Demos Created with Commercial Version</a></p></div></div>
							<?php }?>
							</div>
						<?php
						}
						?>
						
					</fieldset>
				</form>
			</li>
			<li class="wp-tab">
			
				<div class="wp-slider-options">
					<div class="wp-slider-options-menu" id="wp-slider-options-menu">
						<div class="wp-slider-options-menu-item wp-slider-options-menu-item-selected"><?php _e( 'Slider options', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Transition effects', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Skin options', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Bullets & Thumbnails', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Text effect', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'SEO', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Lightbox options', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Social Media options', 'wp_slider' ); ?></div>
						<div class="wp-slider-options-menu-item"><?php _e( 'Advanced options', 'wp_slider' ); ?></div>
					</div>
					
					<div class="wp-slider-options-tabs" id="wp-slider-options-tabs">
						<div class="wp-slider-options-tab wp-slider-options-tab-selected">
							<table class="wp-form-table-noborder">
								<tr>
									<th>Slideshow</th>
									<td><label><input name='wp-slider-autoplay' type='checkbox' id='wp-slider-autoplay' value='' /> Auto slideshow</label>
									<br /><label><input name='wp-slider-pauseonmouseover' type='checkbox' id='wp-slider-pauseonmouseover' value='' /> Pause the slideshow on mouse over</label>
									<br /><label><input name='wp-slider-randomplay' type='checkbox' id='wp-slider-randomplay' value='' /> Random slideshow</label>
									<br /><label><input name='wp-slider-loadimageondemand' type='checkbox' id='wp-slider-loadimageondemand' value='' /> Load images on demand</label>
									<br /><label><input name='wp-slider-transitiononfirstslide' type='checkbox' id='wp-slider-transitiononfirstslide' value='' /> Apply transition to first slide</label>
									</td>
								</tr>
								<tr>
									<th>Video</th>
									<td><label><input name='wp-slider-autoplayvideo' type='checkbox' id='wp-slider-autoplayvideo' value='' /> Auto play video</label>
									<p><label><input name='wp-slider-playmutedandinlinewhenautoplay' type='checkbox' id='wp-slider-playmutedandinlinewhenautoplay' value='' /> Play muted video when autoplay on page load</label></p>
									</td>
								</tr>
								<tr>
									<th>Responsive</th>
									<td><label><input name='wp-slider-isresponsive' type='checkbox' id='wp-slider-isresponsive' value='' /> Create a responsive slider</label><br />
									<label><input name='wp-slider-fullwidth' type='checkbox' id='wp-slider-fullwidth' value='' /> Create a full width slider</label> &nbsp;&nbsp;
									<label><input name='wp-slider-isfullscreen' type='checkbox' id='wp-slider-isfullscreen' value='' /> Extend to the parent container height</label>
									</td>
								</tr>
								<tr>
									<th>Aspect ratio on small screens</th>
									<td><label><input name='wp-slider-ratioresponsive' type='checkbox' id='wp-slider-ratioresponsive' value='' /> Change aspect ratio on small screens</label><br />
									<label>Extend height to <input name='wp-slider-ratiomediumheight' type='number' step='0.1' id='wp-slider-ratiomediumheight' value='1.2' class="small-text" /> times of the original height when the screen width is less than <input name='wp-slider-ratiomediumscreen' type='number' id='wp-slider-ratiomediumscreen' value='900' class="small-text"  /> px</label>
									<br><label>Extend height to <input name='wp-slider-ratiosmallheight' type='number' step='0.1' id='wp-slider-ratiosmallheight' value='2' class="small-text" /> times of the original height when the screen width is less than <input name='wp-slider-ratiosmallscreen' type='number' id='wp-slider-ratiosmallscreen' value='640' class="small-text"  /> px</label>
									</td>
								</tr>
								<tr>
									<th>Image resize mode</th>
									<td><label>
										<select name='wp-slider-scalemode' id='wp-slider-scalemode'>
										  <option value="fit">Resize to fit</option>
										  <option value="fill">Resize to fill</option>
										  <option value="flexheight">Same width, flexible height</option>
										</select>
									</label></td>
								</tr>
								<tr>
									<th>Text</th>
									<td><label><input name='wp-slider-showtext' type='checkbox' id='wp-slider-showtext' value='' /> Show text</label></td>
								</tr>
								<tr>
									<th>Timer</th>
									<td><label><input name='wp-slider-showtimer' type='checkbox' id='wp-slider-showtimer' value='' /> Show a line timer at the bottom of the image when slideshow playing</label></td>
								</tr>
								<tr>
									<th>Loop times ( 0 will loop forever)</th>
									<td><label><input name='wp-slider-loop' type='number' size="10" id='wp-slider-loop' value='0' class='small-text' /></label></td>
								</tr>
								<tr>
									<th>Slideshow interval (ms)</th>
									<td><label><input name='wp-slider-slideinterval' type='number' size="10" id='wp-slider-slideinterval' value='8000' /></label></td>
								</tr>
								<tr>
									<th>Inline CSS</th>
									<td><label><input name='wp-slider-disableinlinecss' type='checkbox' id='wp-slider-disableinlinecss' value='' /> Disable inline CSS (you may need to add the CSS code manually to your WordPress theme style.css file)</label>
									</td>
								</tr>
								<tr>
									<th>WooCommerce slider</th>
									<td><label><input name='wp-slider-addwoocommerceclass' type='checkbox' id='wp-slider-addwoocommerceclass' value='' /> Add class name woocommerce to the slider</label>
									</td>
								</tr>
								<tr>
									<th>Extra attributes to IMG elements</th>
									<td>
									<label><input name='wp-slider-addextraattributes' type='checkbox' id='wp-slider-addextraattributes' value='' /> Add extra attributes to IMG elements:</label>
									<label><input name="wp-slider-imgextraprops" type="text" id="wp-slider-imgextraprops" value="" class="regular-text" /></label></td>
								</tr>
							</table>
						</div>
						<div class="wp-slider-options-tab">
							<table class="wp-form-table-noborder">
								<tr>
									<td>
									<div class="wp-form-half">
										<table>
										<tr><td><label><input name='wp-slider-effect-fade' type='checkbox' id='wp-slider-effect-fade' value='fade' /> Fade</label></td><td><label>Duration (ms): <input name='wp-slider-fadeduration' type='number' class="small-text" id='wp-slider-fadeduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-crossfade' type='checkbox' id='wp-slider-effect-crossfade' value='crossfade' /> Fade out then fade in</label></td><td><label>Duration (ms): <input name='wp-slider-crossfadeduration' type='number' class="small-text" id='wp-slider-crossfadeduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-fadeoutfadein' type='checkbox' id='wp-slider-effect-fadeoutfadein' value='fadeoutfadein' /> Crossfade</label></td><td><label>Duration (ms): <input name='wp-slider-fadeoutfadeinduration' type='number' class="small-text" id='wp-slider-fadeoutfadeinduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-slide' type='checkbox' id='wp-slider-effect-slide' value='slide' /> Slide</label></td><td><label>Duration (ms): <input name='wp-slider-slideduration' type='number' class="small-text" id='wp-slider-slideduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-cssslide' type='checkbox' id='wp-slider-effect-cssslide' value='cssslide' /> CSS slide</label></td><td><label>Duration (ms): <input name='wp-slider-cssslideduration' type='number' class="small-text" id='wp-slider-cssslideduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-elastic' type='checkbox' id='wp-slider-effect-elastic' value='slide' /> Elastic slide</label></td><td><label>Duration (ms): <input name='wp-slider-elasticduration' type='number' class="small-text" id='wp-slider-elasticduration' value='1000' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-slice' type='checkbox' id='wp-slider-effect-slice' value='slice' /> Slice</label></td><td><label>Duration (ms): <input name='wp-slider-sliceduration' type='number' class="small-text" id='wp-slider-sliceduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-blinds' type='checkbox' id='wp-slider-effect-blinds' value='blinds' /> Blinds</label></td><td><label>Duration (ms): <input name='wp-slider-blindsduration' type='number' class="small-text" id='wp-slider-blindsduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-blocks' type='checkbox' id='wp-slider-effect-blocks' value='blocks' /> Blocks</label></td><td><label>Duration (ms): <input name='wp-slider-blocksduration' type='number' class="small-text" id='wp-slider-blocksduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-shuffle' type='checkbox' id='wp-slider-effect-shuffle' value='shuffle' /> Shuffle</label></td><td><label>Duration (ms): <input name='wp-slider-shuffleduration' type='number' class="small-text" id='wp-slider-shuffleduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-tiles' type='checkbox' id='wp-slider-effect-tiles' value='tiles' /> Tiles</label></td><td><label>Duration (ms): <input name='wp-slider-tilesduration' type='number' class="small-text" id='wp-slider-tilesduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-kenburns' type='checkbox' id='wp-slider-effect-kenburns' value='kenburns' /> Ken burns</label></td><td><label>Duration (ms): <input name='wp-slider-kenburnsduration' type='number' class="small-text" id='wp-slider-kenburnsduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-flip' type='checkbox' id='wp-slider-effect-flip' value='flip' /> Flip</label></td><td><label>Duration (ms): <input name='wp-slider-flipduration' type='number' class="small-text" id='wp-slider-flipduration' value='1500' /></label></td></tr>
										<tr><td><label><input name='wp-slider-effect-flipwithzoom' type='checkbox' id='wp-slider-effect-flipwithzoom' value='Flip with zoom' /> Flip with zoom</label></td><td><label>Duration (ms): <input name='wp-slider-flipwithzoomduration' type='number' class="small-text" id='wp-slider-flipwithzoomduration' value='1500' /></label></td></tr>
										</table>
									</div>
									<div class="wp-form-half">
										<table>
										<tr><td><label><input name='wp-slider-effect-threed' type='checkbox' id='wp-slider-effect-threed' value='threed' /> 3D</label></td><td><label>Duration (ms): <input name='wp-slider-threedduration' type='number' class="small-text" id='wp-slider-threedduration' value='1000' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedfallback' id='wp-slider-threedfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedwithzoom' type='checkbox' id='wp-slider-effect-threedwithzoom' value='threedwithzoom' /> 3D with zoom</label></td><td><label>Duration (ms): <input name='wp-slider-threedwithzoomduration' type='number' class="small-text" id='wp-slider-threedwithzoomduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedwithzoomfallback' id='wp-slider-threedwithzoomfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedhorizontal' type='checkbox' id='wp-slider-effect-threedhorizontal' value='threedhorizontal' /> 3D horizontal</label></td><td><label>Duration (ms): <input name='wp-slider-threedhorizontalduration' type='number' class="small-text" id='wp-slider-threedhorizontalduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedhorizontalfallback' id='wp-slider-threedhorizontalfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedhorizontalwithzoom' type='checkbox' id='wp-slider-effect-threedhorizontalwithzoom' value='threedhorizontalwithzoom' /> 3D horizontal with zoom</label></td><td><label>Duration (ms): <input name='wp-slider-threedhorizontalwithzoomduration' type='number' class="small-text" id='wp-slider-threedhorizontalwithzoomduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedhorizontalwithzoomfallback' id='wp-slider-threedhorizontalwithzoomfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedflip' type='checkbox' id='wp-slider-effect-threedflip' value='threedflip' /> 3D flip</label></td><td><label>Duration (ms): <input name='wp-slider-threedflipduration' type='number' class="small-text" id='wp-slider-threedflipduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedflipfallback' id='wp-slider-threedflipfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedflipwithzoom' type='checkbox' id='wp-slider-effect-threedflipwithzoom' value='threedflipwithzoom' /> 3D flip with zoom</label></td><td><label>Duration (ms): <input name='wp-slider-threedflipwithzoomduration' type='number' class="small-text" id='wp-slider-threedflipwithzoomduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedflipwithzoomfallback' id='wp-slider-threedflipwithzoomfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-effect-threedtiles' type='checkbox' id='wp-slider-effect-threedtiles' value='threedtiles' /> 3D tiles</label></td><td><label>Duration (ms): <input name='wp-slider-threedtilesduration' type='number' class="small-text" id='wp-slider-threedtilesduration' value='1500' /></label>
										<br><label>Fallback to effect on Internet Explorer:
										<select name='wp-slider-threedtilesfallback' id='wp-slider-threedtilesfallback'>
										  <option value="fade">Fade</option>
										  <option value="crossfade">Crossfade</option>
										  <option value="fadeoutfadein">Fade out fade in</option>
										  <option value="slide">Slide</option>
										  <option value="cssslide">CSS slide</option>
										  <option value="elastic">Elastic slide</option>
										  <option value="slice">Slice</option>
										  <option value="blinds">Blinds</option>
										  <option value="blocks">Blocks</option>
										  <option value="shuffle">Shuffle</option>
										  <option value="tiles">Tiles</option>
										  <option value="kenburns">Ken burns</option>
										  <option value="flip">Flip</option>
										  <option value="flipwithzoom">Flip with zoom</option></select>
										</label>
										</td></tr>
										</table>
									</div>
									<div style="clear:both;"></div>
									</td>
								</tr>
							</table>
						</div>
						<div class="wp-slider-options-tab">
							<p class="wp-slider-options-tab-title"><?php _e( 'Skin option will be restored to its default value if you switch to a new skin in the Skins tab.', 'wp_slider' ); ?></p>
							<table class="wp-form-table-noborder">
								<tr>
									<th>Slideshow padding</th>
									<td>Padding left: <input name='wp-slider-paddingleft' type='number' class="small-text" id='wp-slider-paddingleft' value='0' />
									Padding right: <input name='wp-slider-paddingright' type='number' class="small-text" id='wp-slider-paddingright' value='0' />
									Padding top: <input name='wp-slider-paddingtop' type='number' class="small-text" id='wp-slider-paddingtop' value='0' />
									Padding bottom: <input name='wp-slider-paddingbottom' type='number' class="small-text" id='wp-slider-paddingbottom' value='0' />
									</td>
								</tr>
								<tr>
									<th>Show bottom shadow</th>
									<td><label><input name='wp-slider-showbottomshadow' type='checkbox' id='wp-slider-showbottomshadow'  /> Show bottom shadow</label>
									</td>
								</tr>
								<tr>
									<th>Show thumbnail preview</th>
									<td><label><input name='wp-slider-navshowpreview' type='checkbox' id='wp-slider-navshowpreview'  /> Show thumbnail preview</label>
									</td>
								</tr>
								<tr>
									<th>Border size</th>
									<td><label><input name='wp-slider-border' type='number' class="small-text" id='wp-slider-border' value='0' /></label></td>
								</tr>
								<tr>
									<th>Arrows</th>
									<td><label>
										<select name='wp-slider-arrowstyle' id='wp-slider-arrowstyle'>
										  <option value="mouseover">Show on mouseover</option>
										  <option value="always">Always show</option>
										  <option value="none">Hide</option>
										</select>
									</label></td>
								</tr>
								<tr>
									<th>Arrow image</th>
									<td>
										<img id="wp-slider-displayarrowimage" />
										<br />
										<label>
											<input type="radio" name="wp-slider-arrowimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wp-slider-customarrowimage' type='text' class="regular-text" id='wp-slider-customarrowimage' value='' />
										</label>
										<br />
										<label>
											<input type="radio" name="wp-slider-arrowimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wp-slider-arrowimage' id='wp-slider-arrowimage'>
											<?php 
												$arrowimage_list = array("arrows-32-32-0.png", "arrows-32-32-1.png", "arrows-32-32-2.png", "arrows-32-32-3.png", "arrows-32-32-4.png", 
														"arrows-36-36-0.png",
														"arrows-36-80-0.png",
														"arrows-48-48-0.png", "arrows-48-48-1.png", "arrows-48-48-2.png", "arrows-48-48-3.png", "arrows-48-48-4.png",
														"arrows-72-72-0.png");
												foreach ($arrowimage_list as $arrowimage)
													echo '<option value="' . $arrowimage . '">' . $arrowimage . '</option>';
											?>
											</select>
										</label><br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wp-slider-arrowimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wp-slider-displayarrowimage").attr("src", jQuery('#wp-slider-customarrowimage').val());
												else
													jQuery("#wp-slider-displayarrowimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery('#wp-slider-arrowimage').val());
											});

											jQuery("#wp-slider-arrowimage").change(function(){
												if (jQuery("input:radio[name=wp-slider-arrowimagemode]:checked").val() == 'defined')
													jQuery("#wp-slider-displayarrowimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery(this).val());
												var arrowsize = jQuery(this).val().split("-");
												if (arrowsize.length > 2)
												{
													if (!isNaN(arrowsize[1]))
														jQuery("#wp-slider-arrowwidth").val(arrowsize[1]);
													if (!isNaN(arrowsize[2]))
														jQuery("#wp-slider-arrowheight").val(arrowsize[2]);
												}
													
											});
										});
										</script>
										<label><span style="display:inline-block;min-width:100px;">Width:</span> <input name='wp-slider-arrowwidth' type='number' class='small-text' id='wp-slider-arrowwidth' value='32' /></label>
										<label><span style="display:inline-block;min-width:100px;margin-left:36px;">Height:</span> <input name='wp-slider-arrowheight' type='number' class='small-text' id='wp-slider-arrowheight' value='32' /></label><br />
										<label><span style="display:inline-block;min-width:100px;">Left/right margin:</span> <input name='wp-slider-arrowmargin' type='number' class='small-text' id='wp-slider-arrowmargin' value='8' /></label>
										<label><span style="display:inline-block;min-width:100px;margin-left:36px;">Top (percent):</span> <input name='wp-slider-arrowtop' type='number' class='small-text' id='wp-slider-arrowtop' value='50' /></label>
										
									</td>
								</tr>
								
								<tr id="wp-slider-configplayvideoimage">
									<th>Play video button</th>
									<td>
										<img id="wp-slider-displayplayvideoimage" />
										<br />
										<label>
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wp-slider-playvideoimage' id='wp-slider-playvideoimage'>
											<?php 
												$playvideoimage_list = array("playvideo-32-32-0.png", "playvideo-64-64-0.png", "playvideo-64-64-1.png", "playvideo-64-64-2.png", "playvideo-64-64-3.png", "playvideo-64-64-4.png", "playvideo-64-64-5.png",
														"playvideo-72-72-0.png");
												foreach ($playvideoimage_list as $playvideoimage)
													echo '<option value="' . $playvideoimage . '">' . $playvideoimage . '</option>';
											?>
											</select>
										</label><br />
										<script language="JavaScript">
										jQuery(document).ready(function(){

											jQuery("#wp-slider-playvideoimage").change(function(){
												jQuery("#wp-slider-displayplayvideoimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery(this).val());
												var arrowsize = jQuery(this).val().split("-");
												if (arrowsize.length > 2)
												{
													if (!isNaN(arrowsize[1]))
														jQuery("#wp-slider-playvideoimagewidth").val(arrowsize[1]);
													if (!isNaN(arrowsize[2]))
														jQuery("#wp-slider-playvideoimageheight").val(arrowsize[2]);
												}							
											});
										});
										</script>
										<label><span style="display:inline-block;min-width:100px;">Width:</span> <input name='wp-slider-playvideoimagewidth' type='number' class='small-text' id='wp-slider-playvideoimagewidth' value='32' /></label>
										<label><span style="display:inline-block;min-width:100px;margin-left:36px;">Height:</span> <input name='wp-slider-playvideoimageheight' type='number' class='small-text' id='wp-slider-playvideoimageheight' value='32' /></label><br />										
									</td>
								</tr>
							</table>
						</div>
						
						<div class="wp-slider-options-tab">
							<table class="wp-form-table-noborder">
								<tr>
									<th>Navigation</th>
									<td>
									<label><input name='wp-slider-shownav' type='checkbox' id='wp-slider-shownav' value='' /> Show navigation</label>
									<p><label><input name='wp-slider-usethumbnailurl' type='checkbox' id='wp-slider-usethumbnailurl' value='' /> Use thumbnail URL for slider thumbnails</label></p>
									</td>
								</tr>
								<tr>
									<th>Position and Spacing</th>
									<td>
									<div id="wp-slider-confignavgeneral">
										<label style="margin-right:24px;"><span style="display:inline-block;">Position:</span> <select name='wp-slider-navposition' id='wp-slider-navposition'>
										  <option value="topright">Top right</option>
										  <option value="topleft">Top left</option>
										  <option value="bottomright">Bottom right</option>
										  <option value="bottomleft">Bottom left</option>
										  <option value="top">Top</option>
										  <option value="bottom">Bottom</option>
										  <option value="left">Left</option>
										  <option value="right">Right</option>
										</select>
										</label>
										<label style="margin-right:24px;"><span style="display:inline-block;">Margin X:</span> <input name='wp-slider-navmarginx' type='number' class="small-text" id='wp-slider-navmarginx' value='8' /></label>
										<label style="margin-right:24px;"><span style="display:inline-block;">Margin Y:</span> <input name='wp-slider-navmarginy' type='number' class="small-text" id='wp-slider-navmarginy' value='8' /></label>
										<label><span style="display:inline-block;">Spacing:</span> <input name='wp-slider-navspacing' type='number' class="small-text" id='wp-slider-navspacing' value='8' /></label>
									</div>
									</td>
								</tr>
								<tr id="wp-slider-confignavimage">
									<th>
									<span class="wp-slider-confignavbullets-title">Bullets</span>
									<span class="wp-slider-confignavthumbnails-title">Thumbnails</span>
									<td>									    
									    <div class="wp-slider-confignavbullets">
										<img id="wp-slider-displaynavimage" />
										<br />
										<label>
											<input type="radio" name="wp-slider-navimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wp-slider-customnavimage' type='text' class="regular-text" id='wp-slider-customnavimage' value='' />
										</label>
										<br />
										<label>
											<input type="radio" name="wp-slider-navimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wp-slider-navimage' id='wp-slider-navimage'>
											<?php 
												$navimage_list = array("bullet-6-6-0.png", "bullet-12-12-0.png",
														"bullet-16-16-0.png", "bullet-16-16-1.png", "bullet-16-16-2.png", "bullet-16-16-3.png", 
														"bullet-20-20-0.png", "bullet-20-20-1.png", "bullet-20-20-2.png", "bullet-20-20-3.png", "bullet-20-20-4.png", "bullet-20-20-5.png",
														"bullet-24-24-0.png", "bullet-24-24-1.png", "bullet-24-24-2.png", "bullet-24-24-3.png", "bullet-24-24-4.png", "bullet-24-24-5.png", "bullet-24-24-6.png");
												foreach ($navimage_list as $navimage)
													echo '<option value="' . $navimage . '">' . $navimage . '</option>';
											?>
											</select>
										</label><br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wp-slider-navimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wp-slider-displaynavimage").attr("src", jQuery('#wp-slider-customnavimage').val());
												else
													jQuery("#wp-slider-displaynavimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery('#wp-slider-navimage').val());
											});

											jQuery("#wp-slider-navimage").change(function(){
												if (jQuery("input:radio[name=wp-slider-navimagemode]:checked").val() == 'defined')
													jQuery("#wp-slider-displaynavimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery(this).val());
												var arrowsize = jQuery(this).val().split("-");
												if (arrowsize.length > 2)
												{
													if (!isNaN(arrowsize[1]))
														jQuery("#wp-slider-navwidth").val(arrowsize[1]);
													if (!isNaN(arrowsize[2]))
														jQuery("#wp-slider-navheight").val(arrowsize[2]);
												}
													
											});
										});
										</script>
										</div>
										
										<div class="wp-slider-confignavthumbnails">
										
										<label><span style="display:inline-block;">Thumbnail size mode:</span> <select name='wp-slider-navthumbresponsivemode' id='wp-slider-navthumbresponsivemode'>
										  <option value="samesize">Keep size</option>
										  <option value="samecolumn">Keep column</option>
										</select>
										</label><br>
										
										<script language="JavaScript">
										(function($) {
											$(document).ready(function() {
												$("#wp-slider-navthumbresponsivemode").change(function(){
													if ($(this).val() == 'samesize')
													{
														$('.wp-slider-navthumbnailsamesize').show();
														$('.wp-slider-navthumbnailsamecolumn').hide();
													}
													else
													{
														$('.wp-slider-navthumbnailsamesize').hide();
														$('.wp-slider-navthumbnailsamecolumn').show();
													}	
												});
											});
										})(jQuery);
										</script>
										</div>
										
										<div class="wp-slider-confignavthumbnailsandbullets">
										<p>
										<label class="wp-slider-navthumbnailsamesize" style="margin-right:24px;"><span style="display:inline-block;">Width:</span> <input name='wp-slider-navwidth' type='number' class='small-text' id='wp-slider-navwidth' value='32' /></label>
										<label class="wp-slider-navthumbnailsamesize"><span style="display:inline-block;">Height:</span> <input name='wp-slider-navheight' type='number' class='small-text' id='wp-slider-navheight' value='32' /></label>
										</p>
										</div>
										
										<div class="wp-slider-confignavthumbnails">
										<p>
										<label class="wp-slider-navthumbnailsamecolumn"><span style="display:inline-block;">Column number:</span> <input name='wp-slider-navthumbcolumn' type='number' class='small-text' id='wp-slider-navthumbcolumn' value='32' /></label>
										</p>
										<p>
										<label><span style="display:inline-block;">Style:</span> <select name='wp-slider-navthumbstyle' id='wp-slider-navthumbstyle'>
										  <option value="imageonly">Image only</option>
										  <option value="imageandtitle">Image and title</option>
										  <option value="imageandtitledescription">Image, title and description</option>
										  <option value="textonly">Text only</option>
										</select>
										</label>
										<label style="margin-right:24px;"><span style="display:inline-block;">Title width:</span> <input name='wp-slider-navthumbtitlewidth' type='number' class='small-text' id='wp-slider-navthumbtitlewidth' value='32' /></label>
										<label><span style="display:inline-block;">Title height:</span> <input name='wp-slider-navthumbtitleheight' type='number' class='small-text' id='wp-slider-navthumbtitleheight' value='32' /></label>
										</p>
										
										<p><label><input name='wp-slider-navthumbresponsive' type='checkbox' id='wp-slider-navthumbresponsive' value='' /> Responsive thumbnails</label></p>
										
										<ul style="list-style-type:square;margin-left:20px;">
											<li>When the screen width is less than <input name='wp-slider-navthumbmediumsize' type='number' class='small-text' id='wp-slider-navthumbmediumsize' value='900' /> px:
											
											<p>
											<label class="wp-slider-navthumbnailsamesize" style="margin-right:24px;"><span style="display:inline-block;">Width:</span> <input name='wp-slider-navthumbmediumwidth' type='number' class='small-text' id='wp-slider-navthumbmediumwidth' value='32' /></label>
											<label class="wp-slider-navthumbnailsamesize"><span style="display:inline-block;">Height:</span> <input name='wp-slider-navthumbmediumheight' type='number' class='small-text' id='wp-slider-navthumbmediumheight' value='32' /></label>
											
											<label class="wp-slider-navthumbnailsamecolumn"><span style="display:inline-block;">Column number:</span> <input name='wp-slider-navthumbmediumcolumn' type='number' class='small-text' id='wp-slider-navthumbmediumcolumn' value='32' /></label>
											
											<label style="margin-right:24px;"><span style="display:inline-block;">Title width:</span> <input name='wp-slider-navthumbmediumtitlewidth' type='number' class='small-text' id='wp-slider-navthumbmediumtitlewidth' value='32' /></label>
											<label><span style="display:inline-block;">Title height:</span> <input name='wp-slider-navthumbmediumtitleheight' type='number' class='small-text' id='wp-slider-navthumbmediumtitleheight' value='32' /></label>
											</p>
											
											</li>
											<li>When the screen width is less than <input name='wp-slider-navthumbsmallsize' type='number' class='small-text' id='wp-slider-navthumbsmallsize' value='600' /> px:
											
											<p>
											<label class="wp-slider-navthumbnailsamesize" style="margin-right:24px;"><span style="display:inline-block;">Width:</span> <input name='wp-slider-navthumbsmallwidth' type='number' class='small-text' id='wp-slider-navthumbsmallwidth' value='32' /></label>
											<label class="wp-slider-navthumbnailsamesize"><span style="display:inline-block;">Height:</span> <input name='wp-slider-navthumbsmallheight' type='number' class='small-text' id='wp-slider-navthumbsmallheight' value='32' /></label>
											
											<label class="wp-slider-navthumbnailsamecolumn"><span style="display:inline-block;">Column number:</span> <input name='wp-slider-navthumbsmallcolumn' type='number' class='small-text' id='wp-slider-navthumbsmallcolumn' value='32' /></label>
											
											<label style="margin-right:24px;"><span style="display:inline-block;">Title width:</span> <input name='wp-slider-navthumbsmalltitlewidth' type='number' class='small-text' id='wp-slider-navthumbsmalltitlewidth' value='32' /></label>
											<label><span style="display:inline-block;">Title height:</span> <input name='wp-slider-navthumbsmalltitleheight' type='number' class='small-text' id='wp-slider-navthumbsmalltitleheight' value='32' /></label>
											</p>
											
											</li>
										</ul>
										
										<p><label><input name='wp-slider-navshowfeaturedarrow' type='checkbox' id='wp-slider-navshowfeaturedarrow' value='' /> Show arrow on the highlighted thumbnail</label></p>
	
										</div>
									</td>
								</tr>
								
								<tr>
									<th>Carousel Arrows</th>
									<td>
									<label><span style="display:inline-block;">Arrow style:</span> <select name='wp-slider-navthumbnavigationstyle' id='wp-slider-navthumbnavigationstyle'>
										  <option value="auto">No arrow</option>
										  <option value="arrow">Arrow</option>
										  <option value="arrowinside">Arrow inside</option>
										  <option value="arrowoutside">Arrow outside</option>
										</select>
									</label>
									
									<div>
									<img id="wp-slider-displaynavthumbnavigationarrowimage" />
									<br />
									<label>
										<input type="radio" name="wp-slider-navthumbnavigationarrowimagemode" value="defined">
										<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
										<select name='wp-slider-navthumbnavigationarrowimage' id='wp-slider-navthumbnavigationarrowimage'>
										<?php 
											$navthumbnavigationarrowimage_list = array("carouselarrows-32-32-0.png", "carouselarrows-32-32-1.png", "carouselarrows-32-32-2.png", "carouselarrows-32-32-3.png", "carouselarrows-32-32-4.png", "carouselarrows-32-32-5.png");
											foreach ($navthumbnavigationarrowimage_list as $navthumbnavigationarrowimage)
												echo '<option value="' . $navthumbnavigationarrowimage . '">' . $navthumbnavigationarrowimage . '</option>';
										?>
										</select>
									</label>
									<br />
									<label>
										<input type="radio" name="wp-slider-navthumbnavigationarrowimagemode" value="custom">
										<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
										<input name='wp-slider-customnavthumbnavigationarrowimage' type='text' class="regular-text" id='wp-slider-customnavthumbnavigationarrowimage' value='' />
									</label>
									<br />
									<script language="JavaScript">
									jQuery(document).ready(function(){
										jQuery("input:radio[name=wp-slider-navthumbnavigationarrowimagemode]").click(function(){
											if (jQuery(this).val() == 'custom')
												jQuery("#wp-slider-displaynavthumbnavigationarrowimage").attr("src", jQuery('#wp-slider-customnavthumbnavigationarrowimage').val());
											else
												jQuery("#wp-slider-displaynavthumbnavigationarrowimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery('#wp-slider-navthumbnavigationarrowimage').val());
										});

										jQuery("#wp-slider-navthumbnavigationarrowimage").change(function(){
											if (jQuery("input:radio[name=wp-slider-navthumbnavigationarrowimagemode]:checked").val() == 'defined')
												jQuery("#wp-slider-displaynavthumbnavigationarrowimage").attr("src", "<?php echo WP_SLIDER_URL . 'engine/'; ?>" + jQuery(this).val());
											var arrowsize = jQuery(this).val().split("-");
											if (arrowsize.length > 2)
											{
												if (!isNaN(arrowsize[1]))
													jQuery("#wp-slider-navthumbnavigationarrowimagewidth").val(arrowsize[1]);
												if (!isNaN(arrowsize[2]))
													jQuery("#wp-slider-navthumbnavigationarrowimageheight").val(arrowsize[2]);
											}
												
										});
									});
									</script>
									<p>
										<label style="margin-right:24px;"><span style="display:inline-block;">Width:</span> <input name='wp-slider-navthumbnavigationarrowimagewidth' type='number' class='small-text' id='wp-slider-navthumbnavigationarrowimagewidth' value='32' /></label>
										<label><span style="display:inline-block;">Height:</span> <input name='wp-slider-navthumbnavigationarrowimageheight' type='number' class='small-text' id='wp-slider-navthumbnavigationarrowimageheight' value='32' /></label>
									</p>
									</div>	
									</td>
								</tr>
							</table>
						</div>
							
						<div class="wp-slider-options-tab">
							<table class="wp-form-table-noborder">
								<tr>
									<th>Select a pre-defined text effect</th>
									<td><label>
										<select name='wp-slider-textformat' id='wp-slider-textformat'>
										  <?php 
												$textformat_list = array(
													'Bottom bar', 
													'Bottom left', 
													'Center text', 
													'Left text', 
													'Center box', 
													'Left box', 
													'Color box', 
													'Color box right align', 
													'Blue box', 
													'Red box', 
													'Navy box', 
													'Pink box', 
													'Light box', 
													'Grey box', 
													'Red title', 
													'White title', 
													'Yellow title', 
													'Underneath center', 
													'Underneath left', 
													'None');
												foreach ($textformat_list as $textformat)
													echo '<option value="' . $textformat . '">' . $textformat . '</option>';
											?>
										</select>
									</label>
									<input class="button button-primary" type="button" id="save-current-text-effect" value="Save text effect">
									<input class="button button-primary" type="button" id="save-text-effect" value="Save as a new text effect">
									<input class="button button-primary" type="button" id="delete-current-text-effect" value="Delete text effect">
									<input type="hidden" id="custom-text-effect" value="">
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td>
									<p>* The following options will be restored to the default value if you change text effect in the above drop-down list.</p>
									<div class='wp-slider-texteffect-static'>
									<label><input name='wp-slider-textautohide' type='checkbox' id='wp-slider-textautohide' value='' /> Auto hide text</label>
									</div>
									
									</td>
								</tr>
								
								<tr>
									<th>Text box CSS</th>
									<td><label><textarea name="wp-slider-textcss" id="wp-slider-textcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Text background CSS</th>
									<td><label><textarea name="wp-slider-textbgcss" id="wp-slider-textbgcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Title CSS</th>
									<td><label><textarea name="wp-slider-titlecss" id="wp-slider-titlecss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Description CSS</th>
									<td><label><textarea name="wp-slider-descriptioncss" id="wp-slider-descriptioncss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Button box CSS</th>
									<td><label><textarea name="wp-slider-buttoncss" id="wp-slider-buttoncss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr>
									<th>Position</th>
									<td>
									<div class='wp-slider-texteffect-static'>
										<select name='wp-slider-textpositionstatic' id='wp-slider-textpositionstatic'>
										  <option value="top">top</option>
										  <option value="bottom">bottom</option>
										  <option value="left">left</option>
										  <option value="right">right</option>
										  <option value="topoutside">topoutside</option>
										  <option value="bottomoutside">bottomoutside</option>
										</select>
										&nbsp;&nbsp;Percentage of text area when the position is left or right: <input name='wp-slider-textleftrightpercentforstatic' type='number' id='wp-slider-textleftrightpercentforstatic' class="small-text" value='40' />
									</div>
									<div  class='wp-slider-texteffect-dynamic'>
										<table>
										<tr><td><label><input name='wp-slider-textpositiondynamic-topleft' type='checkbox' id='wp-slider-textpositiondynamic-topleft' value='topleft' /> top-left</label> 
										</td><td><label><input name='wp-slider-textpositiondynamic-topcenter' type='checkbox' id='wp-slider-textpositiondynamic-topcenter' value='topcenter' /> top-center</label>
										</td><td><label><input name='wp-slider-textpositiondynamic-topright' type='checkbox' id='wp-slider-textpositiondynamic-topright' value='topright' /> top-right</label> 
										</td></tr>
										<tr><td><label><input name='wp-slider-textpositiondynamic-centerleft' type='checkbox' id='wp-slider-textpositiondynamic-centerleft' value='centerleft' /> middle-left</label> 
										</td><td><label><input name='wp-slider-textpositiondynamic-centercenter' type='checkbox' id='wp-slider-textpositiondynamic-centercenter' value='centercenter' /> middle-center</label>
										</td><td><label><input name='wp-slider-textpositiondynamic-centerright' type='checkbox' id='wp-slider-textpositiondynamic-centerright' value='centerright' /> middle-right</label>
										</td></tr>
										<tr><td><label><input name='wp-slider-textpositiondynamic-bottomleft' type='checkbox' id='wp-slider-textpositiondynamic-bottomleft' value='bottomleft' /> bottom-left</label> 
										</td><td><label><input name='wp-slider-textpositiondynamic-bottomcenter' type='checkbox' id='wp-slider-textpositiondynamic-bottomcenter' value='bottomcenter' /> bottom-center</label>
										</td><td><label><input name='wp-slider-textpositiondynamic-bottomright' type='checkbox' id='wp-slider-textpositiondynamic-bottomright' value='bottomright' /> bottom-right</label>
										</td></tr>
										</table>
										<p>* To place the text to top-center, middle-center and bottom-center, you need to make sure "Text box CSS" includes <span style="font-style:italic;color:#990000;">text-align:center;</span> , 
										"Title CSS", "Description CSS" and "Button box CSS" include <span style="font-style:italic;color:#990000;">margin-left:auto; margin-right:auto;</span> .</p>
										</div>
									</td>
								</tr>
								
								<tr>
									<th>Responsive design</th>
									<td><label><input name='wp-slider-texteffectresponsive' type='checkbox' id='wp-slider-texteffectresponsive' value='' /> Apply the responsive CSS when the screen is smaller than (px): </label>
									<input name='wp-slider-texteffectresponsivesize' type='number' id='wp-slider-texteffectresponsivesize' class="small-text" value='600' />
									</td>
								</tr>
								
								<tr>
									<th>Responsive title CSS</th>
									<td><label><textarea name="wp-slider-titlecssresponsive" id="wp-slider-titlecssresponsive" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Responsive description CSS</th>
									<td><label><textarea name="wp-slider-descriptioncssresponsive" id="wp-slider-descriptioncssresponsive" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th>Responsive button box CSS</th>
									<td><label><textarea name="wp-slider-buttoncssresponsive" id="wp-slider-buttoncssresponsive" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
							</table>
						</div>
						
						<div class="wp-slider-options-tab" style="padding:24px;">
							<table class="wp-form-table-noborder">
								<tr>
									<th>Text SEO</th>
									<td>
									<label><input name="wp-slider-outputtext" type="checkbox" id="wp-slider-outputtext" /> Output slide title and description in HTML</label>
									<p><label>Use tag for slide title text: </label>
										<select name="wp-slider-titletag" id="wp-slider-titletag">
										  <option value="h1">h1</option>
										  <option value="h2">h2</option>
											<option value="h3">h3</option>
											<option value="h4">h4</option>
											<option value="h5">h5</option>
											<option value="h6">h6</option>
											<option value="div">div</option>
											<option value="p">p</option>
										</select></p>
									<p><label>Use tag for slide description text: </label>
										<select name="wp-slider-descriptiontag" id="wp-slider-descriptiontag">
										  <option value="h1">h1</option>
										  <option value="h2">h2</option>
											<option value="h3">h3</option>
											<option value="h4">h4</option>
											<option value="h5">h5</option>
											<option value="h6">h6</option>
											<option value="div">div</option>
											<option value="p">p</option>
										</select></p>
									</td>
								</tr>
							</table>
						</div>

						<div class="wp-slider-options-tab" style="padding:24px;">
						
						<ul class="wp-tab-buttons-horizontal" data-panelsid="wp-lightbox-panels">
							<li class="wp-tab-button-horizontal wp-tab-button-horizontal-selected"><?php _e( 'General', 'wp_slider' ); ?></li>
							<li class="wp-tab-button-horizontal"></span><?php _e( 'Video', 'wp_slider' ); ?></li>
							<li class="wp-tab-button-horizontal"></span><?php _e( 'Thumbnails', 'wp_slider' ); ?></li>
							<li class="wp-tab-button-horizontal"></span><?php _e( 'Text', 'wp_slider' ); ?></li>
							<li class="wp-tab-button-horizontal"></span><?php _e( 'Lightbox Advanced Options', 'wp_slider' ); ?></li>
							<div style="clear:both;"></div>
						</ul>
						
						<ul class="wp-tabs-horizontal" id="wp-lightbox-panels">
						
							<li class="wp-tab-horizontal wp-tab-horizontal-selected">
							<table class="wp-form-table-noborder">
								<tr>
									<th>General</th>
									<td><label><input name='wp-slider-lightboxresponsive' type='checkbox' id='wp-slider-lightboxresponsive'  /> Responsive</label>
									<br><label><input name="wp-slider-lightboxfullscreenmode" type="checkbox" id="wp-slider-lightboxfullscreenmode" /> Display in fullscreen mode (the close button on top right of the web browser)</label>
									<br><label><input name="wp-slider-lightboxcloseonoverlay" type="checkbox" id="wp-slider-lightboxcloseonoverlay" /> Close the lightbox when clicking on the overlay background</label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Slideshow</th>
									<td><label><input name="wp-slider-lightboxautoslide" type="checkbox" id="wp-slider-lightboxautoslide" /> Auto play slideshow</label>
									<br>Slideshow interval (ms): <input name="wp-slider-lightboxslideinterval" type="number" min=0 id="wp-slider-lightboxslideinterval" value="5000" class="small-text" />
									<br><label><input name="wp-slider-lightboxalwaysshownavarrows" type="checkbox" id="wp-slider-lightboxalwaysshownavarrows" /> Always show left and right navigation arrows</label>
									<br><label><input name="wp-slider-lightboxshowplaybutton" type="checkbox" id="wp-slider-lightboxshowplaybutton" /> Show play slideshow button</label>
									<br><label><input name="wp-slider-lightboxshowtimer" type="checkbox" id="wp-slider-lightboxshowtimer" /> Show line timer for image slideshow</label>
									<br>Timer position: <select name="wp-slider-lightboxtimerposition" id="wp-slider-lightboxtimerposition">
										  <option value="bottom">Bottom</option>
										  <option value="top">Top</option>
										</select>
									Timer color: <input name="wp-slider-lightboxtimercolor" type="text" id="wp-slider-lightboxtimercolor" value="#dc572e" class="medium-text" />
									Timer height: <input name="wp-slider-lightboxtimerheight" type="number" min=0 id="wp-slider-lightboxtimerheight" value="2" class="small-text" />
									Timer opacity: <input name="wp-slider-lightboxtimeropacity" type="number" min=0 max=1 step="0.1" id="wp-slider-lightboxtimeropacity" value="1" class="small-text" />
									<p style="font-style:italic;">* Video autoplay is not supported on mobile and tables. The limitation comes from iOS and Android.</p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Overlay</th>
									<td>Color: <input name="wp-slider-lightboxoverlaybgcolor" type="text" id="wp-slider-lightboxoverlaybgcolor" value="#333" class="medium-text" />
									Opacity: <input name="wp-slider-lightboxoverlayopacity" type="number" min=0 max=1 step="0.1" id="wp-slider-lightboxoverlayopacity" value="0.9" class="small-text" /></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Background color</th>
									<td><input name="wp-slider-lightboxbgcolor" type="text" id="wp-slider-lightboxbgcolor" value="#fff" class="medium-text" /></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Border</th>
									<td>Radius (px): <input name="wp-slider-lightboxborderradius" type="number" min=0 id="wp-slider-lightboxborderradius" value="0" class="small-text" />
									Size (px): <input name="wp-slider-lightboxbordersize" type="number" min=0 id="wp-slider-lightboxbordersize" value="8" class="small-text" />
									</td>
								</tr>
								
								<tr>
									<th>Group</th>
									<td><label><input name='wp-slider-lightboxnogroup' type='checkbox' id='wp-slider-lightboxnogroup'  /> Do not display lightboxes as a group</label>
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wp-tab-horizontal">
							<table class="wp-form-table-noborder">
								<tr valign="top">
									<th scope="row">Default volume of MP4/WebM videos</th>
									<td><label><input name="wp-slider-lightboxdefaultvideovolume" type="number" min=0 max=1 step="0.1" id="wp-slider-lightboxdefaultvideovolume" value="1" class="small-text" /> (0 - 1)</label></td>
								</tr>
		
								<tr>
									<th>Video</th>
									<td><label><input name='wp-slider-lightboxvideohidecontrols' type='checkbox' id='wp-slider-lightboxvideohidecontrols'  /> Hide MP4/WebM video play control bar</label>
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wp-tab-horizontal">
							<table class="wp-form-table-noborder">
								<tr>
									<th>Thumbnails</th>
									<td><label><input name='wp-slider-lightboxshownavigation' type='checkbox' id='wp-slider-lightboxshownavigation'  /> Show thumbnails</label>
									</td>
								</tr>
								<tr>
									<th></th>
									<td><label>Thumbnail size: <input name="wp-slider-lightboxthumbwidth" type="text" id="wp-slider-lightboxthumbwidth" value="96" class="small-text" /> x <input name="wp-slider-lightboxthumbheight" type="text" id="wp-slider-lightboxthumbheight" value="72" class="small-text" /></label> 
									<label>Top margin: <input name="wp-slider-lightboxthumbtopmargin" type="text" id="wp-slider-lightboxthumbtopmargin" value="12" class="small-text" /> Bottom margin: <input name="wp-slider-lightboxthumbbottommargin" type="text" id="wp-slider-lightboxthumbbottommargin" value="12" class="small-text" /></label>
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wp-tab-horizontal">
							<table class="wp-form-table-noborder">
								<tr valign="top">
									<th scope="row">Text position</th>
									<td>
										<select name="wp-slider-lightboxtitlestyle" id="wp-slider-lightboxtitlestyle">
										  <option value="bottom">Bottom</option>
										  <option value="inside">Inside</option>
										  <option value="right">Right</option>
										  <option value="left">Left</option>
										</select>
									</td>
								</tr>
								
								<tr>
									<th>Maximum text bar height when text position is bottom</th>
									<td><label><input name="wp-slider-lightboxbarheight" type="text" id="wp-slider-lightboxbarheight" value="64" class="small-text" /></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Image/video width percentage when text position is right or left</th>
									<td><input name="wp-slider-lightboximagepercentage" type="number" id="wp-slider-lightboximagepercentage" value="75" class="small-text" />%</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Title</th>
									<td><label><input name="wp-slider-lightboxshowtitle" type="checkbox" id="wp-slider-lightboxshowtitle" /> Show title</label></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Add the following prefix to title</th>
									<td><label><input name="wp-slider-lightboxshowtitleprefix" type="checkbox" id="wp-slider-lightboxshowtitleprefix" /> Add prefix:</label><input name="wp-slider-lightboxtitleprefix" type="text" id="wp-slider-lightboxtitleprefix" value="" class="regular-text" /></td>
								</tr>
								
								<tr>
									<th>Title CSS</th>
									<td><label><textarea name="wp-slider-lightboxtitlebottomcss" id="wp-slider-lightboxtitlebottomcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Title CSS when text position is inside</th>
									<td><textarea name="wp-slider-lightboxtitleinsidecss" id="wp-slider-lightboxtitleinsidecss" rows="2" class="large-text code"></textarea></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Description</th>
									<td><label><input name="wp-slider-lightboxshowdescription" type="checkbox" id="wp-slider-lightboxshowdescription" /> Show description</label></td>
								</tr>
								
								<tr>
									<th>Description CSS</th>
									<td><label><textarea name="wp-slider-lightboxdescriptionbottomcss" id="wp-slider-lightboxdescriptionbottomcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Description CSS when text position is inside</th>
									<td><textarea name="wp-slider-lightboxdescriptioninsidecss" id="wp-slider-lightboxdescriptioninsidecss" rows="2" class="large-text code"></textarea></td>
								</tr>
							</table>
							</li>
							
							<li class="wp-tab-horizontal">
							<table class="wp-form-table-noborder">
								<tr valign="top">
									<th scope="row">Data Options</th>
									<td><textarea name="wp-slider-lightboxadvancedoptions" id="wp-slider-lightboxadvancedoptions" rows="4" class="large-text code"></textarea></td>
								</tr>
							</table>
							</li>
						
						</ul>

						</div>						

						<div class="wp-slider-options-tab" style="padding:24px;">
							<ul class="wp-tab-buttons-horizontal" data-panelsid="wp-share-panels">
								<li class="wp-tab-button-horizontal wp-tab-button-horizontal-selected"><?php _e( 'Slideshow Share', 'wp_slider' ); ?></li>
								<li class="wp-tab-button-horizontal"></span><?php _e( 'Lightbox Share', 'wp_slider' ); ?></li>
								<div style="clear:both;"></div>
							</ul>
							
							<ul class="wp-tabs-horizontal" id="wp-share-panels">
								<li class="wp-tab-horizontal wp-tab-horizontal-selected">
									<table class="wp-form-table-noborder">
										<tr valign="top">
										<th scope="row">Social Media</th>
										<td><label for="wp-slider-showsocial"><input name="wp-slider-showsocial" type="checkbox" id="wp-slider-showsocial" /> Enable social media buttons on the slideshow</label>
										<p><label for="wp-slider-showfacebook"><input name="wp-slider-showfacebook" type="checkbox" id="wp-slider-showfacebook" /> Show Facebook button</label>
										<br><label for="wp-slider-showtwitter"><input name="wp-slider-showtwitter" type="checkbox" id="wp-slider-showtwitter" /> Show Twitter button</label>
										<br><label for="wp-slider-showpinterest"><input name="wp-slider-showpinterest" type="checkbox" id="wp-slider-showpinterest" /> Show Pinterest button</label></p>
										</td>
									</tr>
						        	
						        	<tr valign="top">
										<th scope="row">Position and Size</th>
										<td>
										Display mode:
										<select name="wp-slider-socialmode" id="wp-slider-socialmode">
										  <option value="mouseover" selected="selected">On mouse over</option>
										  <option value="always">Always</option>
										</select>
										<p>Position CSS: <input name="wp-slider-socialposition" type="text" id="wp-slider-socialposition" value="" class="regular-text" /></p>
		                				<p>Position CSS on small screen: <input name="wp-slider-socialpositionsmallscreen" type="text" id="wp-slider-socialpositionsmallscreen" value="" class="regular-text" /></p>
										<p>Button size: <input name="wp-slider-socialbuttonsize" type="number" id="wp-slider-socialbuttonsize" value="32" class="small-text" />
										Button font size: <input name="wp-slider-socialbuttonfontsize" type="number" id="wp-slider-socialbuttonfontsize" value="18" class="small-text" />
										Buttons direction:
										<select name="wp-slider-socialdirection" id="wp-slider-socialdirection">
										  <option value="horizontal" selected="selected">horizontal</option>
										  <option value="vertical">vertical</option>
										</select>
										</p>
										<p><label for="wp-slider-socialrotateeffect"><input name="wp-slider-socialrotateeffect" type="checkbox" id="wp-slider-socialrotateeffect" /> Enable button rotating effect on mouse hover</label></p>	
										</td>
									</tr>
									</table>
								</li>
								<li class="wp-tab-horizontal">
									<table class="wp-form-table-noborder">
										<tr valign="top">
										<th scope="row">Social Media</th>
										<td><label for="wp-slider-lightboxshowsocial"><input name="wp-slider-lightboxshowsocial" type="checkbox" id="wp-slider-lightboxshowsocial" /> Enable social media buttons on the lightbox popup</label>
										<p><label for="wp-slider-lightboxshowfacebook"><input name="wp-slider-lightboxshowfacebook" type="checkbox" id="wp-slider-lightboxshowfacebook" /> Show Facebook button</label>
										<br><label for="wp-slider-lightboxshowtwitter"><input name="wp-slider-lightboxshowtwitter" type="checkbox" id="wp-slider-lightboxshowtwitter" /> Show Twitter button</label>
										<br><label for="wp-slider-lightboxshowpinterest"><input name="wp-slider-lightboxshowpinterest" type="checkbox" id="wp-slider-lightboxshowpinterest" /> Show Pinterest button</label></p>
										</td>
									</tr>
						        	
						        	<tr valign="top">
										<th scope="row">Position and Size</th>
										<td>
										Position CSS: <input name="wp-slider-lightboxsocialposition" type="text" id="wp-slider-lightboxsocialposition" value="" class="regular-text" />
		                				<p>Position CSS on small screen: <input name="wp-slider-lightboxsocialpositionsmallscreen" type="text" id="wp-slider-lightboxsocialpositionsmallscreen" value="" class="regular-text" /></p>
										<p>Button size: <input name="wp-slider-lightboxsocialbuttonsize" type="number" id="wp-slider-lightboxsocialbuttonsize" value="32" class="small-text" />
										Button font size: <input name="wp-slider-lightboxsocialbuttonfontsize" type="number" id="wp-slider-lightboxsocialbuttonfontsize" value="18" class="small-text" />
										Buttons direction:
										<select name="wp-slider-lightboxsocialdirection" id="wp-slider-lightboxsocialdirection">
										  <option value="horizontal" selected="selected">horizontal</option>
										  <option value="vertical">>vertical</option>
										</select>
										</p>
										<p><label for="wp-slider-lightboxsocialrotateeffect"><input name="wp-slider-lightboxsocialrotateeffect" type="checkbox" id="wp-slider-lightboxsocialrotateeffect" /> Enable button rotating effect on mouse hover</label></p>	
										</td>
									</tr>
									</table>
								</li>
							</ul>
						</div>
						
						<div class="wp-slider-options-tab">
							<table class="wp-form-table-noborder">
								<tr>
									<th></th>
									<td><p><label><input name='wp-slider-donotinit' type='checkbox' id='wp-slider-donotinit'  /> Do not init the slider when the page is loaded. Check this option if you would like to manually init the slider with JavaScript API.</label></p>
									<p><label><input name='wp-slider-addinitscript' type='checkbox' id='wp-slider-addinitscript'  /> Add init scripts together with slider HTML code. Check this option if your WordPress site uses Ajax to load pages and posts.</label></p>
									<p><label><input name='wp-slider-triggerresize' type='checkbox' id='wp-slider-triggerresize'  /> Trigger window resize event after (ms): </label><input name="wp-slider-triggerresizedelay" type="number" min=0 id="wp-slider-triggerresizedelay" value="0" class="small-text" /></p>
									</td>
								</tr>
								<tr>
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wp-slider-custom-css' id='wp-slider-custom-css' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Data Options</th>
									<td><textarea name='wp-slider-data-options' id='wp-slider-data-options' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Custom JavaScript</th>
									<td><textarea name='wp-slider-customjs' id='wp-slider-customjs' value='' class='large-text' rows="10"></textarea><br />
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wp-tab">
				<div id="wp-slider-preview-tab">
					<div id="wp-slider-preview-message"></div>
					<div class="wpslider-container" id="wp-slider-preview-container">
					</div>
				</div>
			</li>
			<li class="wp-tab">
				<div id="wp-slider-publish-loading"></div>
				<div id="wp-slider-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}