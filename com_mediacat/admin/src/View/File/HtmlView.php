<?php
/**
 * @package     Mediacat.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\View\File;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a file.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{

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
		$input = Factory::getApplication()->input;		// Initialise variables.
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

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

		Factory::getApplication()->input->set('hidemainmenu', true);

		$user       = Factory::getUser();
		$userId     = $user->id;
		$isNew      = ($this->item->id == 0);

		ToolbarHelper::title($isNew ? Text::_('COM_MEDIACAT_FILE_NEW') : Text::_('COM_MEDIACAT_FILE_EDIT'), 'file mediacat');

		ToolbarHelper::apply('file.apply');
		ToolbarHelper::save('file.save');
		if (empty($isNew))
		{
			ToolbarHelper::cancel('file.cancel', 'JTOOLBAR_CLOSE');
		}
		else
		{
			ToolbarHelper::cancel('file.cancel');
		}

		ToolbarHelper::divider();

		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('file', true);
		}
	}
}
