<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2015 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Category/Zoo Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanZooActivityCategory extends ComLogmanModelEntityActivity
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(
            array(
                'object_table' => 'zoo_category',
                'format'       => '{actor} {action} {object.subtype} {object.application} {object.type} title {object}'
            )
        );

        parent::_initialize($config);
    }

    protected function _objectConfig(KObjectConfig $config)
    {
        $metadata = $this->getMetadata();
        $url      = sprintf('option=com_zoo&controller=category&changeapp=%s&task=edit&cid=%s', $metadata->application->id, $this->row);

        $config->append(
            array(
                'subtype' => array('object' => true, 'objectName' => 'Zoo'),
                'url'     => $url
            )
        );

        if ($type = $metadata->application->type) {
            $config->append(array('application' => array('object' => true, 'objectName' => $type)));
        }

        parent::_objectConfig($config);
    }
}