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

require_once __DIR__ . '/helper.php';

class CedFreemindContentHelper
{

    public function writeContent($params, $id)
    {

        $file = JPath::clean(JPATH_SITE . "/cache/mod_cedfreemindcontent/content-$id.mm");
        $JConfig = new JConfig;

        $useArticlesIcon = $params->get('use-articles-node', '0');
        $useCategoryIcon = $params->get('use-categories-node', '0');

        $articleIcon = $useArticlesIcon ? '<icon BUILTIN="' . $params->get('articles-node', 'list') . '"/>' : '';
        $categoryIcon = $useCategoryIcon ? '<icon BUILTIN="' . $params->get('categories-node') . '"/>' : '';

        $useArticlesLink = $params->get('add-articles-link', '1');
        $useCategoryLink = $params->get('add-categories-link', '1');

        $xml = array();
        $xml[] = '<map version="0.9.0">';
        //$generated = 'Generated ' . date("d F Y H:i:s", filemtime(dirname(__FILE__) . '/content.mm'));
        $xml[] = '<node TEXT="' . $JConfig->sitename . '" >';
//        $xml .= '<hook>';
//        $xml .= '<text>All content entries (articles, news, ...)</text>';
//        $xml .= '</hook>';

        $position = 'left';
        $categories = CedFreemindHelper::getAllCategoriesModel();

        $this->doCategoryRecursively($categories, $categoryIcon, $articleIcon, $useCategoryLink, $useArticlesLink, $xml, $position);


        $xml[] = "</node>";
        $xml[] = "</map>";

        CedFreemindHelper::updateFileInCache($file, implode("\n", $xml));

        return "cache/mod_cedfreemindcontent/content-$id.mm";
    }

    private function doCategory($category, $categoryIcon, $articleIcon, $useCategoryLink, $useArticlesLink, &$position, &$xml)
    {
        $categoryLink = $useCategoryLink ? ' LINK="' . CedFreemindHelper::safe(CedFreemindHelper::getCategoryRoute($category->id)) . '" ' : '';
        $categoryText = CedFreemindHelper::safe($category->title);

        $articles = CedFreemindHelper::getAllArticlesFromCategories($category);
        if ($position == 'left') {
            $position = 'right';
        } else {
            $position = 'left';
        }

        $xml[] = '<node CREATED="' . time() . '" ID="ID_' . time() . '" MODIFIED="' . time() . '" ' . $categoryLink . ' TEXT="' . $categoryText . '" POSITION="' . $position . '" >';
        $xml[] = $categoryIcon;

        foreach ($articles as $article) {
            $articleLink = $useArticlesLink ? ' LINK="' . CedFreemindHelper::safe(CedFreemindHelper::getArticleLink($article->id, $article->catid)) . '" ' : '';
            $articleText = CedFreemindHelper::safe($article->title);
            $xml[] = '<node CREATED="' . time() . '" ID="ID_' . time() . '" MODIFIED="' . time() . '" ' . $articleLink . ' TEXT="' . $articleText . '" >';
            $xml[] = $articleIcon;
            $xml[] = '</node>'; # end $article
        }
        $xml[] = '</node>'; # end categories

    }

    private function doCategoryRecursively($categories, $categoryIcon, $articleIcon, $useCategoryLink, $useArticlesLink, &$xml, &$position)
    {
        foreach ($categories as $category) {
            if (isset($category->subCategories)) {
                $categoryLink = $useCategoryLink ? ' LINK="' . CedFreemindHelper::safe(CedFreemindHelper::getCategoryRoute($category->id)) . '" ' : '';
                $categoryText = CedFreemindHelper::safe($category->title);
                $xml[] = '<node CREATED="' . time() . '" ID="ID_' . time() . '" MODIFIED="' . time() . '" ' . $categoryLink . ' TEXT="' . $categoryText . '" POSITION="' . $position . '" >';
                $xml[] = $categoryIcon;
                $this->doCategoryRecursively($category->subCategories, $categoryIcon, $articleIcon, $useCategoryLink, $useArticlesLink, $xml, $position);
                $xml[] = '</node>'; # end categories
            } else {
                $this->doCategory($category, $categoryIcon, $articleIcon, $useCategoryLink, $useArticlesLink, $position, $xml);
            }
        }
    }

}