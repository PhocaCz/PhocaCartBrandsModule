<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;// no direct access

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;

$app = Factory::getApplication();
$document 	= Factory::getDocument();

if (!ComponentHelper::isEnabled('com_phocacart', true)) {
	$app->enqueueMessage(Text::_('Phoca Cart Error'), Text::_('Phoca Cart is not installed on your system'), 'error');
	return;
}
if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php')) {
	// Joomla 5 and newer
	require_once(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php');
} else {
	// Joomla 4
	JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');
}

$lang 		= Factory::getLanguage();
//$lang->load('com_phocacart.sys');
$lang->load('com_phocacart');
$wa = $document->getWebAssetManager();

$p 							= array();
$p['brands_ordering']		= $params->get( 'brands_ordering', 1 );
$p['slides_per_view']		= $params->get( 'slides_per_view', 5 );
$p['transition_speed']		= $params->get( 'transition_speed', 1500 );
$p['display_pagination']	= $params->get( 'display_pagination', 1 );
$p['display_navigation']	= $params->get( 'display_navigation', 1 );
$p['autoplay_delay']		= $params->get( 'autoplay_delay', 0 );
$p['navigation_top']		= $params->get( 'navigation_top', 0 );
$p['display_link']			= $params->get( 'display_link', 1 );
$p['load_swiper_library']	= $params->get( 'load_swiper_library', 1 );
$p['slides_per_view_576']	= $params->get( 'slides_per_view_576', 1 );
$p['slides_per_view_768']	= $params->get( 'slides_per_view_768', 2 );
$p['slides_per_view_992']	= $params->get( 'slides_per_view_992', 4 );


$moduleclass_sfx 			= htmlspecialchars((string)$params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

$media = PhocacartRenderMedia::getInstance('main');
$media->loadBase();
$media->loadBootstrap();
if ($p['load_swiper_library'] == 1) {
	$media->loadSwiper();
}
$media->loadSpec();
$s = PhocacartRenderStyle::getStyles();


//$i	= 'ph-mod-brands';
//$k	= str_replace('-', '', $i);
$uniqueId = 'phBrandsModuleSwiperContainer'.$module->id;

$c	= '.'.$uniqueId.' .swiper';
$bn	= '.'.$uniqueId.' .swiper-button-next';
$bp	= '.'.$uniqueId.' .swiper-button-prev';
$pg	= '.'.$uniqueId.' .swiper-pagination';
$mt	= 22 + ($p['display_pagination'] == 1 ? 15 : 0) + (int)$p['navigation_top'];// Minus Margin Top for arrows (22 is half of height of the arrow)
$sa   = array();

//$sa[] = 'jQuery(document).ready(function(){';
$sa[] = ' ';
//$sa[] = 'jQuery(window).load(function(){';
$sa[] = 'jQuery(window).on(\'load\', function(){';
$sa[] = '   jQuery("'.$c.'").each(function( i ) {';

$sa[] = '      const '.$uniqueId.' = new Swiper(jQuery("'.$c.'")[i], {';
$sa[] = '         slidesPerView: '.(int)$p['slides_per_view'].',';

if ($p['autoplay_delay'] > 0) {
	$sa[] = '         autoplay: {';
	$sa[] = '             delay: '.(int)$p['autoplay_delay'].',';
	$sa[] = '           },';
}

$sa[] = '         speed: '.(int)$p['transition_speed'].',';
$sa[] = '         spaceBetween: 30,';
$sa[] = '         autoHeight: false,';
$sa[] = '         freeMode: true,';

if ((int)$p['display_navigation'] > 0) {
	$sa[] = '         navigation: {';
	$sa[] = '            nextEl: jQuery("'.$bn.'")[i],';
	$sa[] = '            prevEl: jQuery("'.$bp.'")[i],';
	$sa[] = '         },';
}

if ($p['display_pagination'] == 1) {
	$sa[] = '         pagination: {';
	$sa[] = '            el: "'.$pg.'",';
	$sa[] = '            clickable: true,';
	$sa[] = '         },';
}

if ((int)$p['slides_per_view_576'] > 0 || (int)$p['slides_per_view_768'] > 0 || (int)$p['slides_per_view_992'] > 0) {

	$comma = 0;
	$sa[] = '		breakpoints: {';

	if ((int)$p['slides_per_view_576'] > 0) {
		$sa[] = '			576: {';
		$sa[] = '				slidesPerView: '.(int)$p['slides_per_view_576'].',';
		$sa[] = '				spaceBetween: 10';
		$sa[] = '			}';
		$comma = 1;
    }
	if ((int)$p['slides_per_view_768'] > 0) {
		if ($comma) { $sa[] = '			,';}
		$sa[] = '			768: {';
		$sa[] = '				slidesPerView: '.(int)$p['slides_per_view_768'].',';
		$sa[] = '				spaceBetween: 15';
		$sa[] = '			}';
		$comma = 1;
    }
	if ((int)$p['slides_per_view_992'] > 0) {
		if ($comma) { $sa[] = '			,';}
		$sa[] = '			992: {';
		$sa[] = '				slidesPerView: '.(int)$p['slides_per_view_992'].',';
		$sa[] = '				spaceBetween: 20';
		$sa[] = '			}';
		//$comma = 1;
    }

	$sa[] = '		}';
}


$sa[] = '      });';
$sa[] = '   });';// each

if ($p['display_navigation'] == 1) {
	/*$sa[] = '   var height'.$k.' = jQuery("'.$c.'").height();';
	$sa[] = '   var height'.$k.'h = (height'.$k.' / 2) + '.$mt.';';
	$sa[] = '   jQuery("'.$bn.'").css("margin-top", "-"+height'.$k.'h+"px");';
	$sa[] = '   jQuery("'.$bp.'").css("margin-top", "-"+height'.$k.'h+"px");';
	*/
}

$sa[] = '})';
$sa[] = ' ';
//$document->addScriptDeclaration(implode("\n", $sa));
$wa->addInlineScript(implode("\n", $sa), ['version' => 'auto'], ['type' => 'module']);//, ['defer' => true]



/*
$display_categories = $params->get('display_categories', '');
$hide_categories 	= $params->get('hide_categories', '');

if (!empty($display_categories)) {
	$display_categories = implode(',', $display_categories);
}
if (!empty($hide_categories)) {
	$hide_categories = implode(',', $hide_categories);
}
*/
$brands = PhocacartManufacturer::getAllManufacturers($p['brands_ordering']);
require(ModuleHelper::getLayoutPath('mod_phocacart_brands', $params->get('layout', 'default')));
?>
