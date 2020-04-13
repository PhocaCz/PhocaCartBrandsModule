<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;

echo '<div class="ph-brands-module-box'.$moduleclass_sfx .'">';

echo '<div class="swiper-container ph-mod-brands-swiper-container">
    <div class="swiper-wrapper ph-mod-brands-swiper-wrapper">';


$linkI = PhocacartRoute::getItemsRoute();
$pC 						= PhocacartUtils::getComponentParameters();
$manufacturer_alias			= $pC->get( 'manufacturer_alias', 'manufacturer');
$manufacturer_alias			= $manufacturer_alias != '' ? trim(PhocacartText::filterValue($manufacturer_alias, 'alphanumeric'))  : 'manufacturer';

if (!empty($brands)) {
	foreach ($brands as $k => $v) {
		$link = $linkI . PhocacartRoute::getItemsRouteSuffix('manufacturer', $v->id, $v->alias);

		echo '<div class="swiper-slide ph-mod-brands-swiper-slide">';
		if ($v->image != '') {

			echo '<div class="ph-brand-name">';
			echo $p['display_link'] == 1 ? '<a href="'.$link.'">' : '';
			echo '<img src="'.JURI::base(true).'/' . $v->image.'" alt="'.htmlspecialchars($v->title).'" />';
			echo $p['display_link'] == 1 ? '</a>' : '';
			echo '</div>';

		} else {
			echo '<div class="ph-brand-name">';
			echo $p['display_link'] == 1 ? '<a href="'.$link.'">' : '';
			echo $v->title;
			echo $p['display_link'] == 1 ? '</a>' : '';
			echo '</div>';
		}
		echo '</div>';
	}
	//echo '<div class="ph-cb"></div>';
}

echo '</div>';// end wrapper

// ARROWS
//echo '<div class="swiper-button-next ph-mod-brands-swiper-button-next"></div>';
//echo '<div class="swiper-button-prev ph-mod-brands-swiper-button-prev"></div>';
// PAGINATION
if ($p['display_pagination'] == 1) {
	echo '<div class="swiper-pagination ph-mod-brands-swiper-pagination"></div>';
}
echo '</div>';// end container

// ARROWS MOVED OUTSIDE CONTAINER
if ($p['display_navigation'] == 1) {
	echo '<div class="swiper-button-next ph-mod-brands-swiper-button-next"></div>';
	echo '<div class="swiper-button-prev ph-mod-brands-swiper-button-prev"></div>';
	echo '<div class="clear-fix"></div>';
}
echo '</div>';// end module box
?>


