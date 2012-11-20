<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Include this element on page type templates that display frequently-changed attributes.
 * It will cause the page to reload after the user saves page properties,
 * so that changes to the attributes will be immediately visible on the page.
 *
 * Tested on 5.5 and 5.6.
 *
 * INSTALLATION:
 * 1) Save this file to your site's top-level "elements" directory.
 * 2) Add the following line of code to the desired page type templates:
 *
 *        Loader::element('reload_on_attribute_change');
 *
 * (A good place to add that is under the "$this->inc('elements/header.php')" line.)
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

/*
 * DEV NOTES...
 * Overrides the ccmAlert.hud function (which is called by C5 after properties are saved).
 * See concrete/js/ccm_app/legacy_dialog.js for ccmAlert.hud definition.
 * See concrete/elements/collection_metadata.php for where it's called (look for '$("#ccmMetadataForm").ajaxForm({').
 */
