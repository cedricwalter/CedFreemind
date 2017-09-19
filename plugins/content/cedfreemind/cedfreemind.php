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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.html.parameter');

require_once(dirname(__FILE__) . '/parser.php');

class plgContentCedFreemind extends JPlugin
{

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        //Do not run in admin area and non HTML  (rss, json, error)
        $app = JFactory::getApplication();
        if ($app->isClient('administrator') || JFactory::getDocument()->getType() !== 'html')
        {
            return;
        }

        $canProceed = $context == 'com_content.article';
        if (!$canProceed) {
            return;
        }

        $parser = new CedFreemindParser();
        $entries = $parser->getModel($row->text, intval($this->params->get('defaultWidth')), intval($this->params->get('defaultHeight')), JURI :: base());

        if (sizeof($entries) > 0) {
            $plugin = $this->params->get('plugin', "swfobject");
            foreach ($entries as $entry) {
                if ($plugin == 'swfobject') {
                    $content = $this->insertFlashObjectFreemind($this->params, $entry->uri, $entry->width, $entry->height);
                } else if ($plugin == 'flash') {
                    $content = $this->insertFlashFreemind($entry->uri, $entry->width, $entry->height);
                } else {
                    $content = $this->insertJavaFreemind($entry->url, $entry->width, $entry->height);
                }
                $row->text = str_replace($entry->match, $content, $row->text);
            }
        }

        return;
    }

    function insertFlashObjectFreemind($params, $map, $width, $height)
    {
        $document = JFactory::getDocument();
        $document->addScript("http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");
        $uuid = uniqid();
        $scale = 'exactFit';
        $quality = $params->get('quality', 'high');
        $bgcolor = $params->get('bgcolor');
        $wmode = $params->get('wmode');

        $flashVars = array(
            "wmode:'".$params->get('wmode')."'",
            "min_alpha_buttons:'".$params->get('min_alpha_buttons')."'",
            "max_alpha_buttons:'".$params->get('max_alpha_buttons')."'",
            "openUrl:'".$params->get('openUrl')."'",
            "defaultWordWrap:'".$params->get('defaultWordWrap')."'",
            "maxNodeWidth:'".$params->get('maxNodeWidth')."'",
            "defaultToolTipWordWrap:'".$params->get('defaultToolTipWordWrap')."'",
            "initLoadFile:'".JUri::root()."$map'",
            "startCollapsedToLevel:'".$params->get('startCollapsedToLevel')."'",
            "scaleTooltips:'".$params->get('scaleTooltips')."'",
            "unfoldAll:'".$params->get('unfoldAll')."'",
            "scaleTooltips:'".$params->get('scaleTooltips')."'",
            "buttonsPos:'".$params->get('buttonsPos')."'",
            "noElipseMode:'".$params->get('noElipseMode')."'",
            "justMap:'".$params->get('justMap')."'",
            "toolTipsBgColor:'".$params->get('toolTipsBgColor')."'",
            "mainNodeShape:'".$params->get('mainNodeShape')."'",
        );

        $flashVars = implode(",", $flashVars);

        $parameters = array(
            "quality:'$quality'",
            "bgcolor:'$bgcolor'",
            "allowScriptAccess:'true'"
        );
        $parameters = implode(",", $parameters);

        return '<!-- Copyright (C) 2013-2017 galaxiis.com All rights reserved. -->
                <div id="flashcontent-'.$uuid.'">
                    '. JText::_('PLG_CONTENT_CEDFREEMIND_WARNING') .'
                </div>

                <script type="text/javascript">
                    var flashvars = {'.$flashVars.'};
                    var params = {'.$parameters.'};
                    var attributes = {};

                    swfobject.embedSWF("'.JURI :: base().'/media/plg_content_cedfreemind/visorFreemind.swf", "flashcontent-'.$uuid.'", "'.$width.'", "'.$height.'", "9.0.0", "expressInstall.swf", flashvars, params, attributes);
                </script>';
    }


    function insertFlashFreemind($map, $width, $height)
    {
        $html = "<!-- Copyright (C) 2013-2017 galaxiis.com All rights reserved. -->
				     <div>
					<object type='application/x-shockwave-flash' data='" . JURI :: base() . "/media/plg_content_cedfreemind/visorFreemind.swf'
					     id='freemind' height='" . $height . "' width='" . $width . "'>
					<param name='movie' value='" . JURI :: base() . "/media/plg_content_cedfreemind/visorFreemind.swf'>
					<param name='quality' value='" . $this->params->get('quality', "high") . "'>
				    <param name='bgcolor' value='" . $this->params->get('bgcolor') . "'>
					<param name='scale' value='exactfit'>
					<param name='salign' value='LT'>
					<param name='wmode' value='" . $this->params->get('wmode') . "'>";

        $scale = 'exactFit';
        $mainNodeShape = 'ellipse';
        $html .= "<param name='FlashVars' value='scale=" . $scale . "&amp;min_alpha_buttons=" . $this->params->get('min_alpha_buttons') . "&amp;max_alpha_buttons=" . $this->params->get('max_alpha_buttons') . "&amp;openUrl=" . $this->params->get('openUrl') . "&amp;defaultWordWrap=" . $this->params->get('defaultWordWrap') . "&amp;maxNodeWidth=" . $this->params->get('maxNodeWidth') . "&amp;defaultToolTipWordWrap=" . $this->params->get('defaultToolTipWordWrap') . "&amp;initLoadFile=" . $map . "&amp;startCollapsedToLevel=" . $this->params->get('startCollapsedToLevel') . "&amp;mainNodeShape=" . $mainNodeShape . "&amp;scaleTooltips=" . $this->params->get('scaleTooltips') . "&amp;unfoldAll=" . $this->params->get('unfoldAll') . "&amp;buttonsPos=" . $this->params->get('buttonsPos') . "'>";
        $html .= "</object></div>";
        $html .= '<div style="text-align: center;">
                   <a href="//www.galaxiis.com/cedfreemind-showcase"
                    style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom: 0 none white; text-decoration: none; "
                    onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'"
                    target="_blank"><strong>cedsmugmug</strong></a>
                 </div>';
        return $html;
    }


    function insertJavaFreemind($map, $width, $height)
    {
        $output = "<!-- freemind plugin for joomla! by www.galaxiis.com -->
				    <div>
					<script type=\"text/javascript\">\n" .
            "<!--\n" .
            "    if(!navigator.javaEnabled()) {\n" .
            "        document.write('<div class=\"freeminderror\"> Please install a <a href=\"http://www.java.com\">Java Runtime Environment<\/a> on your computer.</div>');\n" .
            "    }\n" .
            "//-->\n" .
            "</script>\n" .
            "<applet code=\"freemind.main.FreeMindApplet.class\" archive=\"" . JURI :: base() . "/media/plg_content_cedfreemind/freemindbrowser.jar\" width='" . $width . "' height='" . $height . "'>\n" .
            "  <param name=\"type\" value=\"application/x-java-applet;version=1.4\" />\n" .
            "  <param name=\"scriptable\" value=\"false\" />\n" .
            "  <param name=\"modes\" value=\"freemind.modes.browsemode.BrowseMode\" />\n" .
            "  <param name=\"browsemode_initial_map\" value=\"" . $map . "\" />\n" .
            "  <param name=\"initial_mode\" value=\"Browse\" />\n" .
            "  <param value=\"true\" name=\"separate_jvm\" />" .
            "  <param name=\"selection_method\" value=\"selection_method_direct\" />\n" .
            "</applet>\n
            </div>";

        $output .= '<div style="text-align: center;">
                   <a href="//www.galaxiis.com/cedfreemind-showcase"
                    style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom: 0 none white; text-decoration: none; "
                    onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'"
                    target="_blank"><strong>cedsmugmug</strong></a>
                 </div>';


        return $output;
    }


}