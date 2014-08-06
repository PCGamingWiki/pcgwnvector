<?php
/*
 * Largely based on Vector.
 * Made to fit the needs of PCGamingWiki
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

class SkinPCGWNVector extends SkinVector {

	protected static $bodyClasses = array( 'vector-animateLayout' );

	var $skinname = 'pcgwnvector', $stylename = 'pcgwnvector',
		$template = 'PCGWNVectorTemplate', $useheadelement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath;

		parent::initPage( $out );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS file since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/vector/csshover{$min}.htc\")}</style><![endif]-->"
		);

		$out->addModules( array( 'skins.vector.js', 'skins.vector.collapsibleNav' ) );
	}

	/**
	 * Loads skin and user CSS files.
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$styles = array( 'mediawiki.skinning.interface', 'skins.vector.styles' );
		wfRunHooks( 'SkinVectorStyleModules', array( $this, &$styles ) );
		$out->addModuleStyles( $styles );
	}

	/**
	 * Adds classes to the body element.
	 *
	 * @param $out OutputPage object
	 * @param &$bodyAttrs Array of attributes that will be set on the body element
	 */
	function addToBodyAttributes( $out, &$bodyAttrs ) {
		if ( isset( $bodyAttrs['class'] ) && strlen( $bodyAttrs['class'] ) > 0 ) {
			$bodyAttrs['class'] .= ' ' . implode( ' ', static::$bodyClasses );
		} else {
			$bodyAttrs['class'] = implode( ' ', static::$bodyClasses );
		}
	}
}

class PCGWNVectorTemplate extends BaseTemplate {

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgVectorUseIconWatch;

		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() ) ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

		$user = $this->getSkin()->getUser();
		$toggleGoogleAds = $user->getOption( 'pcgwnvector-googleads' );

		// Output HTML Page
		$this->html( 'headelement' );
?>

	<!-- DoubleClick code -->
	<script type='text/javascript'>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	(function() {
	var gads = document.createElement('script');
	gads.async = true;
	gads.type = 'text/javascript';
	var useSSL = 'https:' == document.location.protocol;
	gads.src = (useSSL ? 'https:' : 'http:') + 
	'//www.googletagservices.com/tag/js/gpt.js';
	var node = document.getElementsByTagName('script')[0];
	node.parentNode.insertBefore(gads, node);
	})();
	</script>

	<script type='text/javascript'>
	googletag.cmd.push(function() {
	googletag.defineSlot('/6928793/PCGW_leader1', [728, 90], 'div-gpt-ad-1382563418767-0').addService(googletag.pubads());
	googletag.defineSlot('/6928793/PCGW_leader2', [728, 90], 'div-gpt-ad-1382563418767-1').addService(googletag.pubads());
	googletag.defineSlot('/6928793/PCGW_MPU', [300, 250], 'div-gpt-ad-1382563418767-2').addService(googletag.pubads());
	googletag.pubads().enableSingleRequest();
	googletag.enableServices();
	});
	</script>
	<!-- /DoubleClick code -->

		<div id="mw-page-base" class="noprint"></div>
		<div id="mw-head-base" class="noprint"></div>
		<div id="content" class="mw-body" role="main">
			<a id="top"></a>
			<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
			<?php if ( $this->data['sitenotice'] ) { ?>
			<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
			<?php } ?>
			<!-- Brought to you by... -->
			<div id="PCGamingWiki-notice">
				<span class="plainlinks">Brought to you by <a href="http://www.pcgamingwiki.com/wiki/FTL:_Faster_Than_Light">PCGamingWiki</a></span>
			</div>
			<!-- /Brought to you by... -->
			<h1 id="firstHeading" class="firstHeading" lang="<?php
				$this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();
				$this->text( 'pageLanguage' );
			?>">
			<span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
	<div class="clearfix"></div>
			<?php $this->html( 'prebodyhtml' ) ?>
			<div id="bodyContent">
				<?php if ( $this->data['isarticle'] ) { ?>
				<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
				<?php } ?>
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
				<?php if ( $this->data['undelete'] ) { ?>
				<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<?php } ?>
				<?php if ( $this->data['newtalk'] ) { ?>
				<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
				<?php } ?>
				<div id="jump-to-nav" class="mw-jump">
					<?php $this->msg( 'jumpto' ) ?>
					<a href="#mw-navigation"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?>
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<!-- ad horizontal banner -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>
					<div style="height:90px; width:728px; margin-left:auto; margin-right:auto; margin-top:0px; margin-bottom:16px; background-image: url(http://pcgamingwiki.com/images/8/86/Message.gif); background-repeat:no-repeat">
						<div>

						<!-- ad google -->
							<script type="text/javascript"><!--
							google_ad_client = "ca-pub-0027458528988311";
							/* PCGamingWiki banner */
							google_ad_slot = "2657560885";
							google_ad_width = 728;
							google_ad_height = 90;
							//-->
							</script>
							
						<!-- PCGW_leader1 -->
							<div id='div-gpt-ad-1382563418767-0' style='width:728px; height:90px;'>
							<script type='text/javascript'>
							googletag.cmd.push(function() { googletag.display('div-gpt-ad-1382563418767-0'); });
							</script>
							</div>
							
						</div>
						
						<!-- hp -->
						<a href="http://www.northamericanweather.net/hpcnawx.php?sid=3798">
							<div style="height: 0px; width: 0px;"> </div>
						</a>
					</div>
				<?php } ?>
				<!-- /ad horizontal banner -->
				<?php $this->html( 'bodycontent' ) ?>
				<?php if ( $this->data['printfooter'] ) { ?>
				<div class="printfooter">
				<?php $this->html( 'printfooter' ); ?>
				</div>
				<?php } ?>
				<?php if ( $this->data['catlinks'] ) { ?>
				<?php $this->html( 'catlinks' ); ?>
				<?php } ?>
				<!-- ad footer banner -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>			
					<div style="width: 728px; height:90px; margin:0 auto 0 auto; padding:14px 0 0 0; clear:both">
						<!-- PCGW_leader2 -->
						<div id='div-gpt-ad-1382563418767-1' style='width:728px; height:90px;'>
						<script type='text/javascript'>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-1382563418767-1'); });
						</script>
						</div>
					</div>
				<?php } ?>
				<!-- /ad footer banner -->
				<?php if ( $this->data['dataAfterContent'] ) { ?>
				<?php $this->html( 'dataAfterContent' ); ?>
				<?php } ?>
				<div class="visualClear"></div>
				<?php $this->html( 'debughtml' ); ?>
			</div>
		</div>
		<div id="mw-navigation">
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<div id="mw-head">
				<?php $this->renderNavigation( 'PERSONAL' ); ?>
				<div id="left-navigation">
					<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS' ) ); ?>
				</div>
				<div id="right-navigation">
					<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS', 'SEARCH' ) ); ?>
				</div>
			</div>
			<div id="mw-panel">
				<div id="p-logo" role="banner"><a style="background-image: url(<?php $this->text( 'logopath' ) ?>);" href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>></a></div>
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
				<!-- ad sidebar -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>				
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAAABCAMAAAA7MLYKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAEtQTFRF29vb2tra4ODg6urq5OTk4uLi6+vr7e3t7Ozs8PDw5+fn4+Pj4eHh3d3d39/f6Ojo5eXl6enp8fHx8/Pz8vLy7+/v3Nzc2dnZ2NjYnErj7QAAAD1JREFUeNq0wQUBACAMALDj7hf6JyUFGxzEnYhC9GaNPG1xVffGDErk/iCigLl1XV2xM49lfAxEaSM+AQYA9HMKuv4liFQAAAAASUVORK5CYII=" />
					<div style="margin-top:8px; margin-left:0px; padding-bottom:3px">
						<!-- ad google -->
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
							<!-- PCGamingWiki wide skyscraper link -->
							<ins class="adsbygoogle"
								 style="display:inline-block;width:160px;height:90px"
								 data-ad-client="ca-pub-0027458528988311"
								 data-ad-slot="8567157073"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
					</div>
				<?php } ?>
				<!-- /ad sidebar -->
			</div>
		</div>
		<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->getFooterLinks() as $category => $links ) { ?>
				<ul id="footer-<?php echo $category ?>">
					<?php foreach ( $links as $link ) { ?>
						<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
			<?php $footericons = $this->getFooterIcons( "icononly" );
			if ( count( $footericons ) > 0 ) { ?>
				<ul id="footer-icons" class="noprint">
<?php			foreach ( $footericons as $blockName => $footerIcons ) { ?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
<?php				foreach ( $footerIcons as $icon ) { ?>
						<?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

<?php				} ?>
					</li>
<?php			} ?>
				</ul>
			<?php } ?>
			<div style="clear:both"></div>
		<!-- custom footer -->
		<div style="margin-top:20px; line-height:1.5em; font-size:12px; overflow:auto">
			<div style="float:left">
				<div style="margin-bottom:4px; font-weight:bold">PCGamingWiki</div>
				<a href="http://pcgamingwiki.com">Wiki</a><br/>
				<a href="http://community.pcgamingwiki.com/index">Forums</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About">About us</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About#Contact">Contact us</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About#Advertising">Advertising</a>
			</div>
			<div style="float:left; margin-left:30px">
				<div style="margin-bottom:4px; font-weight:bold">Network</div>
				<a href="http://ftlwiki.com">FTL Wiki</a><br/>
				<a href="http://gunpointwiki.net">Gunpoint Wiki</a><br/>
				<a href="http://prisonarchitectwiki.com">Prison Architect Wiki</a><br/>
				<a href="http://siryouarebeinghuntedwiki.com">Sir, You Are Being Hunted Wiki</a><br/>
				</div>
			<div style="float:left; margin-left:30px">
				<div style="margin-bottom:4px; font-weight:bold">Friends</div>
				<a href="http://cheapshark.com">CheapShark</a><br/>
				<a href="http://gamingonlinux.com">GamingOnLinux</a><br/>
				<a href="http://pcgamesn.com">PCGamesN</a><br/>
				<a href="http://rockpapershotgun.com">Rock Paper Shotgun</a><br/>
			</div>
			<div style="float:left; margin-left:30px">
				<div style="height:22px"></div>
				<a href="http://truepcgaming.com">True PC Gaming</a><br/>
				<a href="http://wsgf.org">Widescreen Gaming Forum</a><br/>
				<a href="http://andrewtsai.co.uk">Andrew Tsai</a><br/>
			</div>
		</div>
		<!-- /custom footer -->
		</div>
		<?php $this->printTrail(); ?>

	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
		}
	}

	/**
	 * @param $name string
	 * @param $content array
	 * @param $msg null|string
	 * @param $hook null|string|array
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = wfMessage( $msg );
		?>
<div class="portal" role="navigation" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?> aria-labelledby='<?php echo Sanitizer::escapeId( "p-$name-label" ) ?>'>
	<h3<?php $this->html( 'userlangattributes' ) ?> id='<?php echo Sanitizer::escapeId( "p-$name-label" ) ?>'><?php echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h3>
	<div class="body">
<?php
		if ( is_array( $content ) ) { ?>
		<ul>
<?php
			foreach ( $content as $key => $val ) { ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			}
			if ( $hook !== null ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		} else { ?>
		<?php
			echo $content; /* Allow raw HTML block to be defined by extensions */
		}

		$this->renderAfterPortlet( $name );
		?>
	</div>
</div>
<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param $elements array
	 */
	protected function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $this->data['rtl'] ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
?>
<div id="p-namespaces" role="navigation" class="vectorTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-namespaces-label">
	<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ) { ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php } ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" role="navigation" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-variants-label">
	<h3 id="mw-vector-current-variant">
	<?php foreach ( $this->data['variant_urls'] as $link ) { ?>
		<?php if ( stripos( $link['attributes'], 'selected' ) !== false ) { ?>
			<?php echo htmlspecialchars( $link['text'] ) ?>
		<?php } ?>
	<?php } ?>
	</h3>
	<h3 id="p-variants-label"><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h3>
	<div class="menu">
		<ul>
			<?php foreach ( $this->data['variant_urls'] as $link ) { ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" lang="<?php echo htmlspecialchars( $link['lang'] ) ?>" hreflang="<?php echo htmlspecialchars( $link['hreflang'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
?>
<div id="p-views" role="navigation" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-views-label">
	<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['view_urls'] as $link ) { ?>
			<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ? '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></span></li>
		<?php } ?>
	</ul>
</div>
<?php
				break;
				case 'ACTIONS':
?>
<div id="p-cactions" role="navigation" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-cactions-label">
	<h3 id="p-cactions-label"><span><?php $this->msg( 'actions' ) ?></span><a href="#"></a></h3>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ) { ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'PERSONAL':
?>
<div id="p-personal" role="navigation" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-personal-label">
	<h3 id="p-personal-label"><?php $this->msg( 'personaltools' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
<?php
					$personalTools = $this->getPersonalTools();
					foreach ( $personalTools as $key => $item ) {
						echo $this->makeListItem( $key, $item );
					}
?>
	</ul>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search" role="search">
	<h3<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h3>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<?php if ( $wgVectorUseSimpleSearch ) { ?>
			<div id="simpleSearch">
		<?php } else { ?>
			<div>
		<?php } ?>
			<?php
			echo $this->makeSearchInput( array( 'id' => 'searchInput' ) );
			echo Html::hidden( 'title', $this->get( 'searchtitle' ) );
			// We construct two buttons (for 'go' and 'fulltext' search modes), but only one will be
			// visible and actionable at a time (they are overlaid on top of each other in CSS).
			// * Browsers will use the 'fulltext' one by default (as it's the first in tree-order), which
			//   is desirable when they are unable to show search suggestions (either due to being broken
			//   or having JavaScript turned off).
			// * The mediawiki.searchSuggest module, after doing tests for the broken browsers, removes
			//   the 'fulltext' button and handles 'fulltext' search itself; this will reveal the 'go'
			//   button and cause it to be used.
			echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ) );
			echo $this->makeSearchButton( 'go', array( 'id' => 'searchButton', 'class' => 'searchButton' ) );
			?>
		</div>
	</form>
</div>
<?php

				break;
			}
		}
	}
}
