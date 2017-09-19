<?php
/**
 * @package     CedFreemind
 * @subpackage  com_cedfreemind
 * http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL 3.0</license>
 * @copyright   Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// Don't allow direct access to the module.
defined('_JEXEC') or die('Restricted access');

class CedFreemindHelper {

    static function safe($text)
    {
        return htmlspecialchars(utf8_encode($text));
    }

    public static function getAllCategoriesModel($type = 'com_content')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('title')
            ->select('id')
            ->from($db->qn('#__categories'))
            ->where($db->qn('published') . '=' . $db->q('1'))
            ->where($db->qn('access') . '=' . $db->q('1'))
            ->where($db->qn('parent_id') . '!=' . $db->q('0'))
            ->where($db->qn('path') . '!=' . $db->q('uncategorised'))
            ->where($db->qn('extension') . '=' . $db->q($type))
            ->order($db->escape('title  ASC'));

        //$querySt = $query->dump();
        $db->setQuery($query);
        $categories = (array)$db->loadObjectList();

        foreach ($categories as $category) {
            $childCategories = CedFreemindHelper::getChildCategories($category->id);
            if ($childCategories != null) {
                $category->subCategories = $childCategories;
            }
        }

        return $categories;
    }

    public static function getChildCategories($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title')
            ->select('id')
            ->from($db->qn('#__categories'))
            ->where($db->qn('parent_id') . '=' . $db->q($id))
            ->where($db->qn('access') . '=' . $db->q('1'))
            ->where($db->qn('extension') . '=' . $db->q('com_content'))
            ->where($db->qn('published') . '=' . $db->q('1'));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        if ($list) {
            foreach ($list as $record) {
                $childCategories = CedFreemindHelper::getChildCategories($record->id);
                if (isset($childCategories)) {
                    $list = array_merge($list, $childCategories);
                }
            }
            return $list;
        }

        return;
    }

    /**
     * @param $category
     */
    public static function getAllArticlesFromCategories($category)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('title')
            ->select('introtext')
            ->select('id')
            ->select('catid')
            ->select('metadesc')

            ->from($db->qn('#__content'))
            ->where($db->qn('catid') . '=' . $db->q($category->id))
            ->where($db->qn('state') . '=' . $db->q('1'))
            ->where($db->qn('access') . '=' . $db->q('1'))
            ->order($db->escape('title  ASC'));

        $db->setQuery($query);
        $articles = (array)$db->loadObjectList();

        return $articles;
    }

    public static function getArticleLink($id, $catId)
    {
        $url = JRoute::_(ContentHelperRoute::getArticleRoute($id, $catId));

        $uri = JUri::getInstance();
        $prefix = $uri->toString(array('scheme', 'host', 'port'));
        $JConfig = new JConfig;

        return $JConfig->sef ? $prefix . JRoute::_($url) : $prefix . $url;
    }

    public static function getCategoryRoute($catId)
    {
        $url = JRoute::_(ContentHelperRoute::getCategoryRoute($catId));

        $uri = JUri::getInstance();
        $prefix = $uri->toString(array('scheme', 'host', 'port'));
        $JConfig = new JConfig;

        return $JConfig->sef ? $prefix . JRoute::_($url) : $prefix . $url;
    }

    /**
     * @param $file
     * @param $xml
     */
    public static function updateFileInCache($file, $xml)
    {
        $dirName = dirname($file);
        if (!JFolder::exists($dirName)) {
            JFolder::create($dirName);
        }

        file_put_contents($file, $xml);
    }

} 