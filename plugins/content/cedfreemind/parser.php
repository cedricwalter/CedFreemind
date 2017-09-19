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

class CedFreemindParser {

    const _PLG_FREEMIND_REGEX = "~{freemind\s*uri=(.*)\s*width=(.*)\s*height=(.*)}~iU";
    const _PLG_FREEMIND_REGEX2 = "#{freemind(.*?)}#s";

    function __construct()
    {
    }

    public function getModel($text, $defaultWidth, $defaultHeight, $base)
    {
        if (JString::strpos($text, '{freemind') === false) {
            return array();
        }

        preg_match_all(self::_PLG_FREEMIND_REGEX2, $text, $matches);

        // Number of matches
        $count = count($matches[0]);

        // plugin only processes if there are any instances of the plugin in the text
        $entries = array();
        if ($count) {
            for ($i = 0; $i < $count; $i++) {
                if (@$matches[1][$i]) {
                    $inline_params = $matches[1][$i];
                    $result = array();
                    $pairs = explode(' ', trim($inline_params));
                    foreach ($pairs as $pair) {
                        $pos = strpos($pair, "=");
                        $key = substr($pair, 0, $pos);
                        $value = substr($pair, $pos + 1);
                        $result[$key] = $value;
                    }

                    $entry = new stdClass();
                    if (isset($result['uri'])) {
                        $entry->uri = trim($result['uri']);
                        if (JFile::exists($entry->uri) == false && strpos($entry->uri, 'http') === false) {
                            $entry->uri = $base . $entry->uri;
                        }
                        if (JFile::exists($entry->uri) == false) {
                            $entry->uri = JUri::base().'/media/plg_content_cedfreemind/fileNotFound.mm';
                        }
                    } else {
                        $entry->uri = JUri::base().'/media/plg_content_cedfreemind/error.mm';
                    }

                    if (isset($result['width'])) {
                        $entry->width = intval(trim($result['width']));
                    } else {
                        $entry->width = $defaultWidth;
                    }
                    if (isset($result['height'])) {
                        $entry->height = intval(trim($result['height']));
                    } else {
                        $entry->height = $defaultHeight;
                    }

                    $entry->match = "{freemind" . @$matches[1][$i] . "}";

                    $entries[] = $entry;
                }
            }
        }

        return $entries;
    }


} 