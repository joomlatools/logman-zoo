<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Comment/Zoo Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanZooActivityComment extends ComLogmanModelEntityActivity
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(
            array(
                'object_table' => 'zoo_comment',
                'format'       => '{actor} {action} {object} {target.subtype} {target}'
            )
        );

        parent::_initialize($config);
    }

    public function getPropertyImage()
    {
        if ($this->verb == 'add') {
            $image = 'icon-comment';
        } else {
            $image = parent::getPropertyImage();
        }

        return $image;
    }

    protected function _objectConfig(KObjectConfig $config)
    {
        $metadata = $this->getMetadata();
        $url      = null;

        $state_map = array('unpublish' => 0, 'publish' => 1, 'archive' => 2);
        $state     = '';

        if (isset($state_map[$this->verb])) {
            $state = $state_map[$this->verb];
        }

        if ($application = $metadata->item->application) {
            $url = sprintf('option=com_zoo&controller=comment&changeapp=%s&filter-item=%s&filter-state=%s', $application, $metadata->item->id, $state);
        }

        $config->append(array('url' => array('admin' => $url)));

        parent::_objectConfig($config);
    }

    public function getPropertyTarget()
    {
        $url      = null;
        $metadata = $this->getMetadata();
        $name     = 'item';

        if ($application = $metadata->item->application) {
            $url = sprintf('option=com_zoo&controller=item&changeapp=%s&task=edit&cid=%s', $application, $metadata->item->id);
        }

        if ($item = $metadata->item->type) {
            $name = $item;
        }

        return $this->_getObject(
            array(
                'find'       => 'target',
                'subtype'    => array('object' => true, 'objectName' => 'Zoo'),
                'objectName' => $name,
                'url'        => array('admin' => $url),
            )
        );
    }

    protected function _findObjectTarget()
    {
        $signature = 'target.zoo.comment.' . $this->row;

        if (!isset(self::$_found_objects[$signature]))
        {
            $query = $this->getObject('lib:database.query.select')
                          ->table('zoo_item')
                          ->columns('COUNT(*)')
                          ->where('id = :id')
                          ->bind(array('id' => $this->getMetadata()->item->id));

            self::$_found_objects[$signature] = (bool) $this->getTable()
                                                            ->getAdapter()
                                                            ->select($query, KDatabase::FETCH_FIELD);
        }

        return self::$_found_objects[$signature];
    }

    protected function _actionConfig(KObjectConfig $config)
    {
        switch($this->verb)
        {
            case 'publish':
                $name = 'approved';
                break;
            case 'unpublish':
                $name = 'unapproved';
                break;
            case 'add':
                $name = 'added';
                break;
            default:
                $name = $this->status;
                break;
        }

        $config->append(array('objectName' => $name));

        parent::_actionConfig($config);
    }
}