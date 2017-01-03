<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_cedfreemind';
	//var $_versionStrategy		= 'different';

	public function __construct() {
		$this->_extensionTitle = 'CedFreemind';
        $this->_cacerts = __DIR__ . '/cacert2.pem';


            $this->_requiresAuthorization = 0;
			$this->_updateURL = 'https://www.galaxiis.com/index.php?option=com_ars&view=update&format=ini&id=11';

		// populate downloadID as liveupdate cannot find the download id in the unknown for it scope
		$this->_downloadID = JComponentHelper::getParams('com_cedfreemind')->get('downloadid');

		parent::__construct();
	}

}