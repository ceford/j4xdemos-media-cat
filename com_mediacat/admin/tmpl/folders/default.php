<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
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
use J4xdemos\Component\Mediacat\Administrator\Helper\JsHelper;

$params = ComponentHelper::getParams('com_mediacat');
$active_path = '/' . $params->get($this->media_type . '_path');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_mediacat.mediacat');

// Populate the language
//$this->loadTemplate('texts');
JsHelper::getJstext();

$media_type_options = array('image' => Text::_('COM_MEDIACAT_FIELDSET_IMAGES_LABEL') , 'file' => Text::_('COM_MEDIACAT_FIELDSET_FILES_LABEL'));

?>

<div class="row">
	<div class="col-12 col-sm-6">
		<form action="index.php?option=com_mediacat&view=folders" method="post" name="adminForm" id="adminForm">

		<div class="control-group">
			<div class="control-label">
				<label id="jform_media_type-lbl" for="jform_media_type">
					<?php echo Text::_('COM_MEDIACAT_SELECT_MEDIA_TYPE'); ?>
				</label>
			</div>
			<div class="controls" style="max-width: 10rem;">
				<select id="jform_media_type" name="jform[media_type]" onchange="Joomla.submitbutton();" class="custom-select">
					<?php echo HTMLHelper::_('select.options', $media_type_options, 'value', 'text', $this->media_type, true); ?>
				</select>
			</div>
		</div>

		<h3><?php echo Text::_('COM_MEDIACAT_FOLDER_TREE'); ?></h3>

		<?php foreach ($this->folders as $i => $folder) : ?>
			<label for="rb-<?php echo $i; ?>"><span class="sr-only"><?php echo Text::_('JSELECT'); ?> /files</span></label>
			<input type="radio" name="rb[]" id="rb-<?php echo $i; ?>" onclick="mediacatSelectFolder(this);"<?php echo $i == 0 ? ' checked="checked"' : ''; ?>>
			<span id="folder-<?php echo $i; ?>"><?php echo $folder; ?></span><br />
		<?php endforeach; ?>

		<br />

		<input type="hidden" name="task" id="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter[activepath]" id="filter_activepath" value="<?php echo $this->activepath; ?>">
		<input type="hidden" name="jform[newfoldername]" id="jform_newfoldername" value="">
		<?php echo HTMLHelper::_('form.token'); ?>

		</form>
	</div>

	<div class="col-12 col-sm-6"
		style="max-height: 75vh; min-height: 75vh; overflow-y:scroll; border: 1px solid black;">
		<h3><?php echo Text::_('COM_MEDIACAT_ACTION_RESULTS'); ?></h3>
		<p>Folders may be processed in random order! Please wait for the job to complete!</p>

		<div id="results">

		</div>
	</div>
</div>
