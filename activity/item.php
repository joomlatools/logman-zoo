<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2015 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Item/Zoo Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanZooActivityItem extends ComLogmanModelEntityActivity
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(
            array(
                'object_table' => 'zoo_item',
                'format'       => '{actor} {action} {object.subtype} {object.type} title {object}'
            )
        );

        parent::_initialize($config);
    }

    protected function _objectConfig(KObjectConfig $config)
    {
        $metadata = $this->getMetadata();
        $url      = sprintf('option=com_zoo&controller=item&changeapp=%s&task=edit&cid=%s', $metadata->application, $this->row);

        $config->append(
            array(
                'subtype' => array('object' => true, 'objectName' => 'Zoo'),
                'type'    => array('object' => true, 'objectName' => $this->getMetadata()->type),
                'url'     => $url
            )
        );

        parent::_objectConfig($config);
    }
}