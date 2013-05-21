<?php
/**
 * A simple plugin that preloads the previous and next image invisibly on the single image image.php page 
 * to speed up image browsing.
 *
 * Supports both maxspace images or standard images.
 * You need to do this in your theme yourself. Visit the {@link http://colorpowered.com/colorbox/ colorbox} site for information.
 *
 * @author Malte Müller (acrylian)
 *
 * @package plugins
 */

$plugin_is_filter = 9|THEME_PLUGIN;
$plugin_description = gettext('Preloads invisibly the previous and next image the single image image.php page to speed up image browsing.');
$plugin_author = 'Malte Müller (acrylian)';
$option_interface = 'preloadImage';

zp_register_filter('theme_body_close','preloadImage::preloader');

class preloadImage {

	function __construct() {
		//	These are best set by the theme itself!
		setOptionDefault('preloadimage_maxspace',0);
	}

	function getOptionsSupported() {
		$opts  = array(gettext('Use maxspace') => array('key' => 'preloadimage_maxspace', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext("Enable if your theme uses maxspace sizes for the sized image. If not checked the default size as set on the options is used.")),
										gettext('Max width') => array('key' => 'preloadimage_maxwidth', 'type' => OPTION_TYPE_TEXTBOX,
										'desc' => gettext("Enter the maximum width value used on your theme (if maxspace is enabled).")),
										gettext('Max height') => array('key' => 'preloadimage_maxheight', 'type' => OPTION_TYPE_TEXTBOX,
										'desc' => gettext("Enter the maximum height value for the image (if maxspace is enabled)."))
									);

		return $opts;
	}

	function handleOption($option, $currentValue) {
	}

	static function preloader() {
		global $_zp_gallery, $_zp_current_image, $_zp_gallery_page;
		if($_zp_gallery_page == 'image.php') {
			if(getOption('preloadimage_maxspace')) {
				$width = getOption('preloadimage_maxwidth');
				$height = getOption('preloadimage_maxheight');
			}
			$imgurl = '';
			$preload = '
				<!-- PRELOAD IMAGES PREV + NEXT START-->
				<div id="imagepreloader" style="display:none; overflow:hidden;" />	
				';
				if(hasPrevImage()) { 
					if(getOption('preloadimage_maxspace')) {
						$maxwidth = $width;
						$maxheight = $height;
					}
					$previmg = $_zp_current_image->getPrevImage();
					if(getOption('preloadimage_maxspace')) {
						getMaxSpaceContainer($maxwidth, $maxheight, $previmg,false);
						$imgurl = $previmg->getCustomImage(NULL, $maxwidth, $maxheight,NULL, NULL,NULL,NULL,false,NULL);
					} else {
						$imgurl = $previmg->getSizedImage(getOption('image_size'));
					}
					$preload .= '<img src="'.html_encode($imgurl).'" />';
				} 
				if(hasNextImage()) { 
					if(getOption('preloadimage_maxspace')) {
						$maxwidth = $width;
						$maxheight = $height;
					}
					$nextimg = $_zp_current_image->getNextImage();
					if(getOption('preloadimage_maxspace')) {
						getMaxSpaceContainer($maxwidth, $maxheight, $nextimg,false);
						$imgurl =	$nextimg->getCustomImage(NULL, $maxwidth, $maxheight,NULL, NULL,NULL,NULL,false,NULL);
					} else {
						$imgurl = $nextimg->getSizedImage(getOption('image_size'));	
					}
					$preload .= '<img src="'.html_encode($imgurl).'" />';
				}
				$preload .= '
			 </div>
			 <!-- PRELOAD IMAGES PREV + NEXT END-->
			 ';
		}
		echo $preload;
	}
}
?>