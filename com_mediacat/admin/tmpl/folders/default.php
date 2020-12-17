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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

$params = ComponentHelper::getParams('com_mediacat');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
//	->useStyle('com_mediacat.mediamanager')
	->useScript('com_mediacat.mediacat');

$media_type_options = array('image' => 'Images', 'file' => 'Files');

?>

<div class="row">
<div class="col-12 col-sm-6">
<form action="index.php?option=com_mediacat&view=folders" method="post" name="adminForm" id="adminForm">

<div class="control-group">
	<div class="control-label">
		<label id="jform_media_type-lbl" for="jform_media_type">
			Select Media Type
		</label>
	</div>
	<div class="controls" style="max-width: 10rem;">
		<select id="jform_media_type" name="jform[media_type]" onchange="Joomla.submitbutton();" class="custom-select">
			<?php echo HTMLHelper::_('select.options', $media_type_options, 'value', 'text', $this->media_type, true); ?>
		</select>
	</div>
</div>

<h3>List of Folders</h3>

<?php foreach ($this->folders as $i => $folder) : ?>
<label for="rb-<?php echo $i; ?>"><span class="sr-only"><?php echo Text::_('JSELECT'); ?> /files</span></label>
<input type="radio" name="rb[]" id="rb-<?php echo $i; ?>" onclick="mediacatSelectFolder(this);">
<span id="folder-<?php echo $i; ?>"><?php echo $folder; ?></span><br />
<?php endforeach; ?>
<input type="radio" name="rb[]" onclick="mediacatUnselectFolder(this);">
Select None
<input type="hidden" name="task" id="task" value="">
<input type="hidden" name="boxchecked" value="0">
<input type="hidden" name="newfoldername" id="newfoldername" value="">
<input type="hidden" name="jform[activepath]" id="jform_activepath" value="<?php echo $this->activepath; ?>">
<?php echo HTMLHelper::_('form.token'); ?>

</form>
</div>
<div class="col-12 col-sm-5" id="results"
	style="max-height: 75vh; overflow-y:scroll; border: 1px solid black;"></div>
</div>
