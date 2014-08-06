<?php
/* 
 * @file
 * @ingroup Skins
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

$wgExtensionCredits['skin'][] = array (
	'path'				=> __FILE__,
	'name'				=> 'PCGamingWiki Network Vector',
	'descriptionmsg'	=> 'pcgwnvector-desc',
	'author'			=> array( 'Stanisław Gackowski' ),
	'url'				=> "https://github.com/PCGamingWiki/pcgwnvector",
	'license-name'		=> 'GPL-2.0+',
);

$wgValidSkinNames['pcgwnvector'] = 'PCGWNVector';

$wgHooks['GetPreferences'][] = 'wfPrefHook';

function wfPrefHook( $user, &$preferences ) {
	$preferences['pcgwnvector-googleads'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-googleads',
		'section' => 'rendering/skin',
	);

	return true;
}

$wgDefaultUserOptions['pcgwnvector-googleads'] = 0;

$wgAutoloadClasses['SkinPCGWNVector'] = dirname(__FILE__).'/PCGWNVector.skin.php';
$wgMessagesDirs['PCGWNVector'] = __DIR__ . '/i18n';

$wgResourceModules['skins.pcgwnvector'] = array(
	'styles' => array(
		'pcgwnvector/css/screen.css' => array( 'media' => 'screen' ),
	),
	'remoteBasePath' => &$GLOBALS['wgStylePath'],
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);