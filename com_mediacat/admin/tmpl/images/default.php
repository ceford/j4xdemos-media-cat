<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2020 Clifford E Ford
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

$params = ComponentHelper::getParams('com_mediacat');
$fileBaseUrl = Uri::root(true);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useStyle('com_mediacat.mediacat')
	->useScript('com_mediacat.mediacat');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_mediacat&view=images'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
				?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
				<div class="row">
					<div class="col-12 col-md-3">
					<h3><?php echo Text::_('COM_MEDIACAT_FOLDER_TREE'); ?></h3>
					<?php require 'tree.php'; ?>
					</div>
					<div class="col-12 col-md-9">
					<table class="table" id="articleList">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_BANNERS_BANNERS_TABLE_CAPTION'); ?>,
							<span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
							<span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
						</caption>
						<thead>
							<tr>
								<td><?php echo Text::_('COM_MEDIACAT_PREVIEW'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_NAME'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_EXTENSION'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_DATE_CREATED'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_WIDTH'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_HEIGHT'); ?></td>
								<td><?php echo Text::_('COM_MEDIACAT_MEDIA_SIZE'); ?></td>
								<th scope="col" class="w-5 d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->items as $i => $item) :
								$imageurl = substr($item->file_path, 1);
							?>
								<tr>
									<td rowspan="2" class="image-cropped preview cursor-zoom" style="background-image: url('<?php echo $fileBaseUrl . $item->file_path; ?>');"
									onclick="mediacatAction('zoom',<?php echo "'{$imageurl}'";?>)"></td>
									<td class="break-word">
										<a href="index.php?option=com_mediacat&view=image&layout=edit&id=<?php echo $item->id; ?>">
											<?php echo $item->file_name; ?>
										</a>
									</td>
									<td><?php echo $item->extension; ?></td>
									<td><?php echo $item->date_created; ?></td>
									<td id="width-<?php echo $item->id; ?>"><?php echo $item->width; ?></td>
									<td id="height-<?php echo $item->id; ?>"><?php echo $item->height; ?></td>
									<td><?php echo $item->size; ?></td>
									<td class="d-none d-md-table-cell">
										<?php echo $item->id; ?>
									</td>
								</tr>
								<tr>
									<td>
										<select id="actionlist_<?php echo $item->id; ?>" class="custom-select"
											onChange="mediacatAction(this, '<?php echo $imageurl; ?>')">
											<option value=""><?php echo Text::_('COM_MEDIACAT_ACTIONS'); ?></option>
											<option value="share"><?php echo Text::_('COM_MEDIACAT_ACTIONS_SHARE_URL'); ?></option>
											<option value="image"><?php echo Text::_('COM_MEDIACAT_ACTIONS_IMAGE_TAG'); ?></option>
											<option value="figure"><?php echo Text::_('COM_MEDIACAT_ACTIONS_FIGURE_TAG'); ?></option>
											<option value="picture"><?php echo Text::_('COM_MEDIACAT_ACTIONS_PICTURE_TAG'); ?></option>
											<option value="edit"><?php echo Text::_('COM_MEDIACAT_ACTIONS_EDIT'); ?></option>
											<option value="trash"><?php echo Text::_('JTRASH'); ?></option>
										</select>
									</td>
									<td id="id="alt-<?php echo $item->id; ?>" colspan="6">
										Alt = <span id="alt-<?php echo $item->id; ?>"><?php echo $item->alt; ?></span><br>
										Caption = <span id="caption-<?php echo $item->id; ?>"><?php echo $item->caption; ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<?php // Load the pagination. ?>
					<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</div>

				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<input type="hidden" name="filter[activepath]" id="filter_activepath" value="<?php echo $this->state->get('filter.activepath')?>">
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