<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;

/**
 * Methods supporting a list of banner records.
 *
 * @since  1.6
 */
class TrashModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'state', 'a.state',
					'file_name', 'a.file_name',
					'extension', 'a.extension',
					'date_created', 'a.date_created',
					'date_trashed', 'a.date_trashed',
					'date_deleted', 'a.date_deleted',
					'size', 'a.size',
					'trash_id', 'a.trash_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__mediacat_trash AS a');

		$state = $this->getState('filter.state');
		if (!empty($state))
		{
			$query->where('a.state = ' . $state);
		}
		else
		{
			$query->where('a.state = -2');
		}

		$media_type = $this->getState('filter.media_type');
		if (!empty($media_type))
		{
			$query->where('media_type = ' . $db->quote($media_type));
		}

		$extension = $this->getState('filter.extension');
		if (!empty($extension))
		{
			$query->where('extension = ' . $db->quote($extension));
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$ordering = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);
		$query->order($ordering);
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.depth');
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.media_type');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Mediacat', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		$mediatype = $this->getUserStateFromRequest($this->context . '.filter.media_type', 'filter_media_type', '');
		$this->setState('filter.mediatype', $mediatype);

		$extension = $this->getUserStateFromRequest($this->context . '.filter.extension', 'filter_extension', '');
		$this->setState('filter.extension', $extension);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '-2');
		$this->setState('filter.state', $state);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);
	}
}
