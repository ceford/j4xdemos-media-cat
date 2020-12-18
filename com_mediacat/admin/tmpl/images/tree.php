<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2020 Clifford E Ford
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$root = JPATH_SITE;
$path = '';
//$current = 'images/sampledata/parks/animals';
$current = $this->state->get('filter.activepath');
$subs = [];
$dirs = explode('/', $current);

foreach ($dirs as $dir) {
	if (empty($dir)) {
		continue;
	}
	$path .= '/' . $dir;
	foreach (new DirectoryIterator($root . $path) as $fileInfo) {
		if($fileInfo->isDot()) continue;
		if ($fileInfo->isDir()) {
			$subs[] = $path . '/' . $fileInfo->getFilename();
		}
	}
}

asort($subs);

/*
 * /images/banners
 * /images/headers
 * /images/sampledata
 * /images/sampledata/cassiopeia
 * /images/sampledata/fruitshop
 * /images/sampledata/parks
 * /images/sampledata/parks/animals
 * /images/sampledata/parks/landscape
 * /images/tests
 */

// so puch the image path onto the fron of the list
array_unshift($subs, '/' . $params->get('image_path'));

foreach ($subs as $sub)
{
	// make an array
	$members = explode('/', substr($sub, 1));
	$space = count($members) -1;
	$active = ($sub == $current) ? ' active' : '';
	echo '<div class="cat-folder indent-' . $space . $active . '" data-link="'. $sub .'">';
	if (!$active)
	{
		echo '<a href="#" onclick="setFolder(\''.$sub.'\');return false;">';
		echo '<span class="icon-folder"></span> ';
	}
	else
	{
		echo '<span class="icon-folder-open"></span> ';
	}
	echo array_pop($members);
	if (!$active)
	{
		echo '</a>';
	}
	echo '</div>' . "\n";
}
