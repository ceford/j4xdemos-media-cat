<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

$params = ComponentHelper::getParams('com_mediacat');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
//	->useStyle('com_mediacat.mediamanager')
	->useScript('com_mediacat.mediacat');

// Populate the language
$this->loadTemplate('texts');

$tmpl = Factory::getApplication()->input->getCmd('tmpl');

// Load the toolbar when we are in an iframe
if ($tmpl === 'component')
{
	echo '<div class="subhead noshadow">';
	echo Toolbar::getInstance('toolbar')->render();
	echo '</div>';
}

// Populate the media config
$config = array(
	'apiBaseUrl'              => Uri::base() . 'index.php?option=com_mediacat&format=json',
	'csrfToken'               => Session::getFormToken(),
	'filePath'                => $params->get('file_path', 'images'),
	'fileBaseUrl'             => Uri::root() . $params->get('file_path', 'images'),
	'fileBaseRelativeUrl'     => $params->get('file_path', 'images'),
	'editViewUrl'             => Uri::base() . 'index.php?option=com_mediacat&view=file' . ($tmpl ? '&tmpl=' . $tmpl : ''),
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'imagesExtensions'        => $params->get('image_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
	'providers'               => (array) $this->providers,
	'currentPath'             => $this->currentPath,
	'isModal'                 => $tmpl === 'component',
);
$this->document->addScriptOptions('com_mediacat', $config);

function dirToArray($dir, $current, $level, &$files) {
	
	$result = array();
	$path_start = strlen(JPATH_SITE);
	$path = substr($dir, $path_start);
	
	$cdir = scandir($dir);
	foreach ($cdir as $key => $value)
	{
		if (!in_array($value,array(".","..")))
		{
			$is_dir = is_dir($dir . DIRECTORY_SEPARATOR . $value);
			if ($is_dir || ($level === 1))
			{
				if (mb_strpos($current, $value) !== false) {
					$result[$value . '|' . $path . '/' . $value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value, $current, $level+1, $files);
				} else {
					if ($is_dir) {
						$result[] = $value . '|' . $path . '/' . $value;
					}
				}
			}
			else
			{
				//var_dump($current, $dir);
				if (mb_strpos($dir, $current) !== false) {
					$files[] = $value;
				}
			}
		}
	}
	
	return $result;
}

$root = JPATH_SITE . '/images';

$current = 'images/sampledata/parks/animals';

$files = [];

$html = '';

$result =  dirToArray ( JPATH_SITE . '/images', $current, 1, $files);

function listIt($input, &$id) {
	foreach ($input as $key => $item) {
		if (is_array($item)) {
			$dir_data = explode('|', $key);
			echo '<li role="treeitem" aria-expanded="false" class="dir-link" dir-data="' . $dir_data[1] . '">' . $dir_data[0] . '</li>' . "\n"  .'<ul role="group" class="group">' . "\n";
			listIt($item, $id);
			echo "</ul>\n";
		} else {
			//var_dump($key, $item);
			$dir_data = explode('|', $item);
			echo '<li role="treeitem" class="dir-link" dir-data="' . $dir_data[1]  . '">' . $dir_data[0] . "</li>\n";
		}
	}
}

?>
<div id="com-media"></div>

<h3 id="tree_label">File Viewer</h3>

<div class="row">
<div class="col-3 folder-panel">

<?php 

echo '<ul role="tree" aria-labelledby="tree_label">' . "\n" .
  '<li role="treeitem" aria-expanded="false">images' ."\n<ul>\n";
$id = 1;
listIt($result, $id);

echo "</ul></ul>\n";
?>
</div>
<div class="col-9 file-panel">
<ul>
<?php foreach ($files as $key => $file) : ?>
<li>
<?php echo $file; ?>
</li>
<?php endforeach; ?>
</ul>
</div>
</div>

