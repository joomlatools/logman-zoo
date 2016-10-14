<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Zoo LOGman Plugin.
 *
 * Provides event handlers for dealing with com_zoo events.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanZoo extends ComLogmanPluginJoomla
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('resources' => array('category', 'item', 'comment')));
        parent::_initialize($config);
    }

    protected function _getItemObjectData($data, $event)
    {
        return array(
            'id'       => $data->id,
            'name'     => $data->name,
            'metadata' => array('type' => $data->type, 'application' => $data->application_id)
        );
    }

    protected function _getCategoryObjectData($data, $event)
    {
        $object = array(
            'id'       => $data->id,
            'name'     => $data->name,
            'metadata' => array('application' => array('id' => $data->application_id))
        );

        $config = new KObjectConfig(array('type' => 'Application'));
        $items  = $this->_getItems($data->application_id, $config);
        $item   = current($items);

        if ($item) {
            $object['metadata']['application']['type'] = $item->application_group;
        }

        return $object;
    }

    protected function _getCommentObjectData($data, $event)
    {
        $object = array(
            'id'       => $data->id,
            'name'     => 'comment',
            'metadata' => array(
                'author' => array(
                    'name'  => $data->author,
                    'id'    => $data->user_id,
                    'type'  => $data->user_type,
                    'email' => $data->email,
                    'url'   => $data->url,
                ),
                'item'   => array('id' => $data->item_id)
            )
        );

        $config = new KObjectConfig(array('type' => 'Item'));
        $items  = $this->_getItems($data->item_id, $config);
        $item   = current($items);

        // Store the item's application id.
        if ($item) {
            $object['metadata']['item']['application'] = $item->application_id;
            $object['metadata']['item']['type'] = $item->type;
        }

        return $object;
    }

    public function onFinderAfterSave($context, $item, $isNew)
    {
        $this->onContentAfterSave($context, $item, $isNew); // Forward event.
    }

    public function onContentAfterSave($context, $content, $isNew)
    {
        $tasks = array('publish', 'unpublish', 'spam', 'approve', 'unapprove');

        // Bypass save events that originate on some actions.
        if (!in_array($this->getObject('request')->getData()->task, $tasks)) {
            parent::onContentAfterSave($context, $content, $isNew);
        }
    }

    public function onFinderAfterDelete($context, $item)
    {
        $this->onContentAfterDelete($context, $item); // Forward event.
    }

    protected function _getItems($ids, KObjectConfig $config)
    {
        $config->append(array('type' => $this->_name));

        $items = array();
        $class = ucfirst($config->type) . 'Table';

        $path = sprintf('%s/tables/%s.php', JPATH_COMPONENT_ADMINISTRATOR, strtolower($config->type));

        if (file_exists($path)) {
            require_once $path;
        }

        if (class_exists($class))
        {
            $table = new $class(App::getInstance('zoo'));
            $ids   = (array) $ids;

            foreach ($ids as $id)
            {
                if ($item = $table->get($id)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }
}