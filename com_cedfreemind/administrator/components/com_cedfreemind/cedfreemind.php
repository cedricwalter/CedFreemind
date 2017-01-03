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

require_once dirname(__FILE__). '/liveupdate/liveupdate.php';
if( JFactory::getApplication()->input->get('view','') == 'liveupdate') {
    JToolBarHelper::preferences( 'com_cedfreemind' );
    LiveUpdate::handleRequest();
    return;
}

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . '/media/com_cedfreemind/css/cedfreemind.css');

$controller = JControllerLegacy::getInstance('Cedfreemind');
$task = JFactory::getApplication()->input->get('task', 'default', 'string');
$controller->execute($task);
$controller->redirect();
