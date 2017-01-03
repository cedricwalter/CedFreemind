<?php
/**
 * @package     CedFreemind
 * @subpackage  com_cedfreemind
 * http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL 3.0</license>
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// Don't allow direct access to the module.
defined('_JEXEC') or die('Restricted access');

$script = 'function insertCedFreemind() {' . "\n\t";

$script .= 'var uri = document.getElementById("uri").value;' . "\n\t";
$script .= 'var width = document.getElementById("width").value;' . "\n\t";
$script .= 'var height = document.getElementById("height").value;' . "\n\t";
$script .= 'var tag = "{freemind uri="+uri+" width="+width+" height="+height+"}";' . "\n\t";
$script .= 'window.parent.jInsertEditorText(tag, \'' . $this->eName . '\');' . "\n\t";
$script .= 'window.parent.SqueezeBox.close();' . "\n\t";
$script .= 'return false;' . "\n";
$script .= '}' . "\n";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form>

    <h4>Insert Easily any Freemind map into your Joomla articles.</h4>

    <h4>uri of the map</h4>
    Freemind map must be saved into your joomla instance, Some valid path examples
    <ol>
        <li>media/mymap.mm</li>
        <li>http://www.mysite.com/media/mymap.mm</li>
        <li>http://www.anyothersite.com/x/y/z/a3rdpartymap.mm</li>
    </ol>

    <h4>Width (optionnal)</h4>
    Desired width of the map in your article. If none is specified, the default width will be taken from the administrator settings. Default is 200.

    <h4>Height (optionnal)</h4>
    Desired height of the map in your article. If none is specified, the default height will be taken from the administrator settings. Default is 200.

    <br/>
    <hr/>
    <br/>

    <table>
        <tr>
            <td class="key" align="right">
                <label for="uri">
                    <?php echo JText::_('uri'); ?>
                </label>
            </td>
            <td>
                <input type="text" id="uri" name="uri" size="50" value="media/mymap.mm"/>
            </td>
        </tr>
        <tr>
            <td class="key" align="right">
                <label for="width">
                    <?php echo JText::_('width'); ?>
                </label>
            </td>
            <td>
                <input type="text" id="width" name="width" value="200"/>
            </td>
        </tr>
        <tr>
            <td class="height" align="right">
                <label for="height">
                    <?php echo JText::_('height'); ?>
                </label>
            </td>
            <td>
                <input type="text" id="height" name="height" value="200"/>
            </td>
        </tr>

    </table>
</form>
<button onclick="insertCedFreemind();"><?php echo JText::_('Insert Freemind'); ?></button>