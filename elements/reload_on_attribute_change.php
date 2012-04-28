<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Reload page when user updates custom attributes / properties
 * by overriding the ccmAlert.hud function (which is called by C5 after properties are saved).
 * See concrete/js/ccm_app/legacy_dialog.js for ccmAlert.hud definition.
 * See concrete/elements/collection_metadata.php for where it's called (look for '$("#ccmMetadataForm").ajaxForm({').
 */

$c = Page::getCurrentPage();
$cp = new Permissions($c);

if (is_object($cp) && $cp->canAdminPage()) {
	$js = <<<EOT
	$(window).load(function() { //window.load happens after document.ready, so do this to ensure we're firing after ccmAlert is originally defined by C5
		if (typeof ccmAlert !== 'undefined') {
			ccmAlert._hud = ccmAlert.hud;
			ccmAlert.hud = function(message, time, icon, title) {
				message = (typeof message !== 'undefined') ? message : null;
				time = (typeof time !== 'undefined') ? time : null;
				icon = (typeof icon !== 'undefined') ? icon : null;
				title = (typeof title !== 'undefined') ? title : null;

				//Check for signature indicating that properties were just saved
				if ((message == ccmi18n.savePropertiesMsg) && (time == 2000) && (icon == 'success') && (title == ccmi18n.properties)) {
					window.location.reload(true); //pass true to force get (reload from server, not browser cache)
				//Not a property save -- pass args through to the original hud() function
				} else {
					ccmAlert._hud(message, title, icon, title);
				}
			}
		}
	});
EOT;

	$this->addFooterItem(Loader::helper('html')->script($js));
}