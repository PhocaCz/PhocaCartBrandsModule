<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
defined('_JEXEC') or die;// no direct access

if (!JComponentHelper::isEnabled('com_phocacart', true)) {
	$app = JFactory::getApplication();
	$app->enqueueMessage(JText::_('Phoca Cart Error'), JText::_('Phoca Cart is not installed on your system'), 'error');
	return;
}

JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');

$lang = JFactory::getLanguage();
//$lang->load('com_phocacart.sys');
$lang->load('com_phocacart');

$document = JFactory::getDocument();
$media = new PhocacartRenderMedia();
$media->loadSwiper();


$p['brands_ordering']		= $params->get( 'brands_ordering', 1 );

$p['slides_per_view']		= $params->get( 'slides_per_view', 5 );
$p['transition_speed']		= $params->get( 'transition_speed', 1500 );
$p['display_pagination']	= $params->get( 'display_pagination', 1 );
$p['display_navigation']	= $params->get( 'display_navigation', 1 );
$p['autoplay_delay']		= $params->get( 'autoplay_delay', 0 );
$p['navigation_top']		= $params->get( 'navigation_top', 0 );
$p['display_link']			= $params->get( 'display_link', 1 );
$p['load_swiper_library']	= $params->get( 'load_swiper_library', 1 );
$moduleclass_sfx 			= htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$p['slides_per_view_576']		= $params->get( 'slides_per_view_576', 1 );
$p['slides_per_view_768']		= $params->get( 'slides_per_view_768', 2 );
$p['slides_per_view_992']		= $params->get( 'slides_per_view_992', 4 );

if ($p['load_swiper_library'] == 1) {
	$media->loadSwiper();	
}

$i	= 'ph-mod-brands';
$k	= str_replace('-', '', $i);
$c	= '.'.$i.'-swiper-container';
$bn	= '.'.$i.'-swiper-button-next';
$bp	= '.'.$i.'-swiper-button-prev';
$pg	= '.'.$i.'-swiper-pagination';
$p['navigation_top'] = $p['navigation_top'];
$mt	= 22 + ($p['display_pagination'] == 1 ? 15 : 0) + (int)$p['navigation_top'];// Minus Margin Top for arrows (22 is half of height of the arrow)
$s   = array();
//$s[] = 'jQuery(document).ready(function(){';
$s[] = ' ';
$s[] = 'jQuery(window).load(function(){';
$s[] = '   jQuery("'.$c.'").each(function( i ) {';

$s[] = '      var swiper = new Swiper(jQuery("'.$c.'")[i], {';
$s[] = '         slidesPerView: '.(int)$p['slides_per_view'].',';

if ($p['autoplay_delay'] > 0) {
	$s[] = '         autoplay: {';
	$s[] = '             delay: '.(int)$p['autoplay_delay'].',';
	$s[] = '           },';
}

$s[] = '         speed: '.(int)$p['transition_speed'].',';
$s[] = '         spaceBetween: 30,';
$s[] = '         autoHeight: false,';
$s[] = '         freeMode: true,';

if ($p['display_navigation'] == 1) {
	$s[] = '         navigation: {';
	$s[] = '            nextEl: jQuery(".swiper-button-next'.$bn.'")[i],';
	$s[] = '            prevEl: jQuery(".swiper-button-prev'.$bp.'")[i],';
	$s[] = '         },';
}

if ($p['display_pagination'] == 1) {
	$s[] = '         pagination: {';
	$s[] = '            el: "'.$pg.'",';
	$s[] = '            clickable: true,';
	$s[] = '         },';
}

if ((int)$p['slides_per_view_576'] > 0 || (int)$p['slides_per_view_768'] > 0 || (int)$p['slides_per_view_992'] > 0) {
	
	$comma = 0;
	$s[] = '		breakpoints: {';
	
	if ((int)$p['slides_per_view_576'] > 0) {
		$s[] = '			576: {';
		$s[] = '				slidesPerView: '.(int)$p['slides_per_view_576'].',';
		$s[] = '				spaceBetween: 10';
		$s[] = '			}';
		$comma = 1;
    }
	if ((int)$p['slides_per_view_768'] > 0) {
		if ($comma) { $s[] = '			,';}
		$s[] = '			768: {';
		$s[] = '				slidesPerView: '.(int)$p['slides_per_view_768'].',';
		$s[] = '				spaceBetween: 15';
		$s[] = '			}';
		$comma = 1;
    }
	if ((int)$p['slides_per_view_992'] > 0) {
		if ($comma) { $s[] = '			,';}
		$s[] = '			992: {';
		$s[] = '				slidesPerView: '.(int)$p['slides_per_view_992'].',';
		$s[] = '				spaceBetween: 20';
		$s[] = '			}';
		//$comma = 1;
    }
	
	$s[] = '		}';	
}

$s[] = '      });';
$s[] = '   });';// each

if ($p['display_navigation'] == 1) {
	$s[] = '   var height'.$k.' = jQuery("'.$c.'").height();';
	$s[] = '   var height'.$k.'h = (height'.$k.' / 2) + '.$mt.';';
	$s[] = '   jQuery("'.$bn.'").css("margin-top", "-"+height'.$k.'h+"px");';
	$s[] = '   jQuery("'.$bp.'").css("margin-top", "-"+height'.$k.'h+"px");';
}

$s[] = '})';
$s[] = ' ';
$document->addScriptDeclaration(implode("\n", $s));



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
require(JModuleHelper::getLayoutPath('mod_phocacart_brands'));
?>