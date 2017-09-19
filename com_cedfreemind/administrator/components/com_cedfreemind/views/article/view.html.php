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

jimport('joomla.application.component.view');

class CedfreemindViewArticle extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $state;

    /**
   	 * Display the view
   	 *
   	 * @return	mixed	False on error, null otherwise.
   	 */
   	function display($tpl = null)
    {
        if ($this->getLayout() == 'cedfreemind' ) {
            $eName = JFactory::getApplication()->input->get('e_name', null, 'string');
            $eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);
            $document = JFactory::getDocument();
            $document->setTitle(JText::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
            $this->eName = $eName;
            parent::display($tpl);
            return;
        }


    }

}
