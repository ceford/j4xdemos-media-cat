<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use J4xdemos\Component\Mediacat\Administrator\Helper\JsHelper;

$params = ComponentHelper::getParams('com_mediacat');
$fileBaseUrl = Uri::root(true) . '/' . $params->get('trash_path');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_mediacat.mediacat')
	->registerAndUseStyle('com_mediacat.file-icon-vectors', 'media/com_mediacat/css/file-icon-vectors.min.css')
	->useScript('com_mediacat.mediacat');

// Populate the language
JsHelper::getJstext();

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_mediacat&view=trash'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">

				<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
				?>

				<div class="row">
					<div class="col-12">
					<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS') . Text::_('COM_MEDIACAT_SELECT_FOLDERS_INDEXER'); ?>
					</div>
					<?php else : ?>
					<table class="table" id="articleList">
						<thead>
							<tr>
								<td>
									<?php echo Text::_('COM_MEDIACAT_ICON'); ?>
								</td>
								<td>
									<?php echo Text::_('COM_MEDIACAT_MEDIA_TYPE'); ?>
								</td>
								<td>
									<?php echo Text::_('COM_MEDIACAT_STATE'); ?>
								</td>
								<td>
									<?php echo Text::_('COM_MEDIACAT_MEDIA_NAME'); ?>
								</td>
								<td>
									<?php echo Text::_('COM_MEDIACAT_MEDIA_EXTENSION'); ?>
								</td>

								<td>
									<?php echo Text::_('COM_MEDIACAT_MEDIA_DATE_TRASHED'); ?>
								</td>

								<td>
									<?php echo Text::_('COM_MEDIACAT_MEDIA_DATE_DELETED'); ?>
								</td>

								<td>
									Original ID
								</td>
								<td>
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.trash_id', $listDirn, $listOrder); ?>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->items as $i => $item) :
								$item->exists = 0; // to fix
							?>
								<tr>
									<td rowspan="2" class="">
										<?php if ($this->state->get('filter.state') == -2) : ?>
										<a href="<?php echo $fileBaseUrl . $item->folder_path . '/' . $item->id . '-' . $item->file_name; ?>">
											<span class="fiv-cla fiv-icon-<?php echo $item->extension; ?>"></span>
										</a>
										<?php else : ?>
											<span class="fiv-cla fiv-icon-<?php echo $item->extension; ?>"></span>
										<?php endif; ?>
									</td>

									<td>
										<?php echo $item->media_type; ?>
									</td>

									<td>
										<?php echo Text::_('COM_MEDIACAT_STATE_' . $item->state); ?>
									</td>

									<td class="break-word">
										<?php echo $item->file_name; ?>
									</td>

									<td>
										<?php echo $item->extension; ?>
									</td>

									<td>
										<?php echo $item->date_trashed; ?>
									</td>

									<td>
										<?php echo $item->date_deleted; ?>
									</td>

									<td>
										<?php echo $item->id; ?>
									</td>
									<td>
										<?php echo $item->trash_id; ?>
									</td>
								</tr>
								<tr>
									<td>
									</td>
									<td colspan="8">
										Path: <span id="alt-<?php //echo $item; ?>"><?php echo $item->folder_path; ?></span>
										<?php if ($item->exists) : ?>
										<br />
										<?php echo Text::_('COM_MEDIACAT_ACTIONS_RESTORE_DISABLED') . $item->file_path; ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<?php endif; ?>
					</div>
				</div>


				<input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $this->id; ?>">
				<input type="hidden" name="jform[media_type]" id="jform_media_type" value="<?php echo $this->media; ?>">
				<input type="hidden" name="jform[version]" id="jform_version" value="">
				<input type="hidden" name="task" id="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

<?php
$footer = '
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
';
	echo HTMLHelper::_(
	'bootstrap.renderModal',
	'collapseModal',
	[
		'title' => Text::_('COM_MEDIACAT_IMAGE_ZOOM'),
		'footer' => $footer
	],
	'<h3>Transient Modal Stuff</h3><div id="modal-content">Transient Modal Content</div>'
	//$this->loadTemplate('batch_body')
); ?>
