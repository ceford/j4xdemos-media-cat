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

$app = Factory::getApplication();
$data = $app->input->post->getArray();

$media_type = $app->getUserState('com_mediacat.folders.media_type');
$activepath = $app->getUserState('com_mediacat.folders.activepath');

if (isset($data['media_type'])) {
	$media_type_selected = $data['media_type'];
	$activepath = '/images';
}
else if (isset($media_type))
{
	$media_type_selected = $media_type;
	$activepath = '/images';
}
else
{
	$media_type_selected = 'images';
	$activepath = '/images';
}

$app->setUserState('com_mediacat.folders.media_type', $media_type_selected);
$app->setUserState('com_mediacat.folders.activepath', $activepath);

$media_type_options = array('images' => 'Images', 'files' => 'Files');

?>

<form action="index.php?option=com_mediacat&view=folders" method="post" name="adminForm" id="adminForm">

<div class="control-group">
	<div class="control-label">
		<label id="jform_media_type-lbl" for="jform_media_type">
			Select Media Type
		</label>
	</div>
	<div class="controls" style="max-width: 10rem;">
		<select id="media_type" name="media_type" onchange="Joomla.submitbutton();" class="custom-select">
			<?php echo HTMLHelper::_('select.options', $media_type_options, 'value', 'text', $media_type_selected); ?>
		</select>
	</div>
</div>

<h3>List of Folders</h3>

<?php foreach ($this->model->getFolders($media_type_selected) as $i => $folder) : ?>
<label for="rb-<?php echo $i; ?>"><span class="sr-only"><?php echo Text::_('JSELECT'); ?> /files</span></label>
<input type="radio" name="rb[]" id="rb-<?php echo $i; ?>" onclick="mediacatSelectFolder(this);">
<span id="folder-<?php echo $i; ?>"><?php echo $folder; ?></span><br />
<?php endforeach; ?>
<input type="radio" name="rb[]" onclick="mediacatUnselectFolder(this);">
Select None
<input type="hidden" name="task" id="task" value="">
<input type="hidden" name="boxchecked" value="0">
<input type="hidden" name="newfoldername" id="newfoldername" value="">
<input type="hidden" name="activepath" id="activepath" value="<?php //echo $this->state->get('filter.activepath'); ?>">
<?php echo HTMLHelper::_('form.token'); ?>

</form>
