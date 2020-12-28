<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\View\Trash;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit an file.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $id;
	protected $items;
	protected $media;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   4.0.0
	 */
	public function display($tpl = null)
	{
		//$input = Factory::getApplication()->input;
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		//var_dump($this->items);
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the toolbar buttons
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function addToolbar()
	{
		$tmpl = Factory::getApplication()->input->getCmd('tmpl');

		ToolbarHelper::title(Text::_('COM_MEDIACAT_TRASH_CAN'), 'file mediacat');

		ToolbarHelper::link('index.php?option=com_mediacat&view=images', Text::_('COM_MEDIACAT_FIELDSET_IMAGES_LABEL'));

		ToolbarHelper::link('index.php?option=com_mediacat&view=files', Text::_('COM_MEDIACAT_FIELDSET_FILES_LABEL'));

		$nRecords = $this->pagination->total;
		ToolbarHelper::custom('','info', '', $nRecords . ' ' . Text::_('COM_MEDIACAT_TOOLBAR_BUTTON_RECORDS'), true);

		ToolbarHelper::divider();
		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('trash', true);
		}
	}
}
