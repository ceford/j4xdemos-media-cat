<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Text::script('COM_mediacat_imagesER_MESSAGE_COMPLETE', true);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
//$wa = $this->document->getWebAssetManager();
//$wa->useScript('keepalive')
//	->useStyle('com_finder.indexer')
//	->useScript('com_finder.indexer');

?>

<div class="text-center">
	<h1 id="finder-progress-header" class="m-t-2" aria-live="assertive"><?php echo Text::_('COM_mediacat_imagesER_HEADER_INIT'); ?></h1>
	<p id="finder-progress-message" aria-live="polite"><?php echo Text::_('COM_mediacat_imagesER_MESSAGE_INIT'); ?></p>
	<div id="progress" class="progress">
		<div id="progress-bar" class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<?php if (JDEBUG) : ?>
	<dl id="finder-debug-data" class="row">
	</dl>
	<?php endif; ?>
	<input id="finder-indexer-token" type="hidden" name="<?php echo Factory::getSession()->getFormToken(); ?>" value="1">
</div>

<?php if (false) : ?>
<?php
$path = JPATH_SITE . "/images";

// Construct the iterator
$it = new RecursiveDirectoryIterator($path);
$db = Factory::getDbo();

$path_start = strlen(JPATH_SITE);
$start_time = hrtime(true);
$nfiles = 0;
$new_files = 0;
$old_files = 0;
$params = ComponentHelper::getParams('com_mediacat');
$allowed_extensions = explode(',', $params->get('upload_extensions', ''));

// Script start
$rustart = getrusage();

echo "<p>\n";

// Loop through files
foreach(new RecursiveIteratorIterator($it) as $file) {
	// /Users/ceford/Sites/j4xdemos/images/joomla_black.png
	if (is_dir($file)) {
		continue;
	}
	// remove JPATH_SITE from path
	$path = substr($file, $path_start);
	$subs = explode('/', $path);
	$filename = array_pop($subs);
	$extension = substr($filename, strrpos($filename, '.') + 1);
	// is it a valid file type
	if (!in_array($extension, $allowed_extensions)) {
		continue;
	}
	list($width, $height, $type, $wandhstring) = getimagesize($file);
	$size = filesize($file);
	// takes 5x longer to hash - needs time saving ploy if file has not changed
	$hash = hash('md5', $file);

	$nfiles++;

	//echo $path. "<br />\n";

	$query = $db->getQuery(true);
	// does the record exist
	$query->select('id');
	$query->from('#__mediacat_images');
	$query->where('file_path = ' . $db->quote($path));
	$db->setQuery($query);
	$id = $db->loadResult();
	// if yes then update it
	$query = $db->getQuery(true);
	if ($id) {
		$query->update('#__mediacat_images');
		$query->where('id = ' . $id);
		$old_files++;
	} else {
		$query->insert('#__mediacat_images');
		$new_files++;
	}
	$query->set('file_path = ' . $db->quote($path));
	$query->set('file_name = ' . $db->quote($filename));
	$query->set('extension = ' . $db->quote($extension));
	$query->set('width = ' . $db->quote($width));
	$query->set('height = ' . $db->quote($height));
	$query->set('size = ' . $db->quote($size));
	$query->set('hash = ' . $db->quote($hash));

	$db->setQuery($query);
	try
	{
		$db->execute();
	}
	catch (\RuntimeException $e)
	{
		// skip this one
		var_dump($query->__tostring()); die;
	}
}
echo "</p>\n";

$end_time = hrtime(true);

$eta = $end_time - $start_time;

echo '<p>Number of files = ' . $nfiles;
echo '. New files = ' . $new_files;
echo '. Existing files = ' . $old_files;
echo '. Time taken in microseconds = ' . $eta/1e+6 . "</p>\n"; //nanoseconds to milliseconds

// Script end
function rutime($ru, $rus, $index) {
	return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
	-  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "<p>This process used " . rutime($ru, $rustart, "utime") .
" ms for its computations</p>\n";
echo "<p>It spent " . rutime($ru, $rustart, "stime") .
" ms in system calls</p>\n";

?>

<?php endif; ?>