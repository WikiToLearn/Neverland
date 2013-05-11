<?php
/**
 * Neverland - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
  die( -1 );
}

/**
 * SkinTemplate class for Neverland skin
 * @ingroup Skins
 */
class SkinNeverland extends SkinTemplate {

  var $skinname = 'neverland', $stylename = 'neverland',
    $template = 'NeverlandTemplate', $useHeadElement = true;

  /**
   * Initializes output page and sets up skin-specific parameters
   * @param $out OutputPage object to initialize
   */
  public function initPage( OutputPage $out ) {
    global $wgLocalStylePath;

    parent::initPage( $out );

    // Append CSS which includes IE only behavior fixes for hover support -
    // this is better than including this in a CSS fille since it doesn't
    // wait for the CSS file to load before fetching the HTC file.
    $min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
    $out->addModuleScripts( 'skins.neverland' );
  }

  /**
   * Load skin and user CSS files in the correct order
   * @param $out OutputPage object
   */
  function setupSkinUserCss( OutputPage $out ){
    parent::setupSkinUserCss( $out );
    $out->addStyle( '//cdn.kde.org/css/bootstrap.css', 'screen' );
    $out->addStyle( '//cdn.kde.org/css/bootstrap-responsive.css', 'screen' );
    $out->addStyle( '//cdn.kde.org/css/bootstrap-mediawiki.css', 'screen' );
  }
}

/**
 * QuickTemplate class for Neverland skin
 * @ingroup Skins
 */
class NeverlandTemplate extends BaseTemplate {

  /* Functions */

  /**
   * Outputs the entire contents of the (X)HTML page
   */
  public function execute() {
    global $wgRequest, $wgOut, $wgCanonicalNamespace, $wgContLang, $wgSitename,
         $wgLogo, $wgStylePath, $wgNeverlandUseIconWatch;

    // Build additional attributes for navigation urls
    $nav = $this->data['content_navigation'];

    if ( $wgNeverlandUseIconWatch ) {
      $mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
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
        $nav[$section][$key]['attributes'] = ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
        
        if ( $link['class'] ) {
          $nav[$section][$key]['attributes'] = $nav[$section][$key]['attributes'] .
            ' class="' . htmlspecialchars( $link['class'] ) . '"';
          $nav[$section][$key]['class'] = '';
        }
        
        if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
          $nav[$section][$key]['key'] = Linker::tooltip( $xmlID );
        } else {
          $nav[$section][$key]['key'] = Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
        }
      }
    }
    
    $this->data['namespace_urls'] = $nav['namespaces'];
    $this->data['view_urls'] = $nav['views'];
    $this->data['action_urls'] = $nav['actions'];
    $this->data['variant_urls'] = $nav['variants'];

    // Reverse horizontally rendered navigation elements
    if ( $this->data['rtl'] ) {
      $this->data['view_urls'] = array_reverse( $this->data['view_urls'] );
      $this->data['namespace_urls'] = array_reverse( $this->data['namespace_urls'] );
      $this->data['personal_urls'] = array_reverse( $this->data['personal_urls'] );
    }
    
    // Output HTML Page
    $this->html( 'headelement' );
  ?>

  <!-- header -->
  <div id="top-small" class="navbar navbar-static-top Neverland noprint">
    <div class="navbar-inner">
      <div class="container">
        <div class="pull-right">
          <?php $this->renderNavigation( 'SEARCH' ); ?>
        </div>

        <a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" class="brand">
          <img src="//cdn.kde.org/img/logo.plain.small.png" alt="" />
          <?php echo $wgSitename; ?>
        </a>
      </div>
    </div>
  </div>
  <!-- /header -->
  
  <div id="top" class="container">
    <!-- content -->
    <div class="row">
      <div class="span9">
        <section>
          <div id="mw-js-message" class="alert alert-info" style="display:none;"
            <?php $this->html( 'userlangattributes' ) ?>>
          </div>

          <?php if ( $this->data['sitenotice'] ): ?>
            <!-- sitenotice -->
            <div id="siteNotice">
              <?php $this->html( 'sitenotice' ) ?>
            </div>
            <!-- /sitenotice -->
          <?php endif; ?>

          <!-- page-actions -->
          <?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS' ) ); ?>
          <!-- /page-actions -->

          <!-- top-navigation -->
          <?php $this->renderNavigation( 'NAMESPACES' ); ?>
          <!-- /top-navigation -->

          <!-- firstHeading -->
          <header>
            <h1 id="firstHeading">
              <?php $this->html( 'title' ) ?>
            </h1>
          </header>
          
          <!-- /firstHeading -->

          <!-- bodyContent -->
          <article id="bodyContent">
            <!-- subtitle -->
            <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
            <!-- /subtitle -->

            <?php if ( $this->data['undelete'] ): ?>
              <!-- undelete -->
              <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
              <!-- /undelete -->
            <?php endif; ?>

            <?php if( $this->data['newtalk'] ): ?>
              <!-- newtalk -->
              <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
              <!-- /newtalk -->
            <?php endif; ?>

            <?php if ( $this->data['showjumplinks'] ): ?>
              <!-- jumpto -->
              <div id="jump-to-nav" class="mw-jump">
                <?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
                <a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
              </div>
              <!-- /jumpto -->
            <?php endif; ?>

            <!-- bodycontent -->
            <?php $this->html( 'bodycontent' ) ?>
            <!-- /bodycontent -->

            <?php if ( $this->data['printfooter'] ): ?>
              <!-- printfooter -->
              <div class="printfooter">
                <?php $this->html( 'printfooter' ); ?>
              </div>
            <!-- /printfooter -->
            <?php endif; ?>

            <?php if ( $this->data['catlinks'] ): ?>
              <!-- catlinks -->
              <?php $this->html( 'catlinks' ); ?>
              <!-- /catlinks -->
            <?php endif; ?>

            <?php if ( $this->data['dataAfterContent'] ): ?>
              <!-- dataAfterContent -->
              <?php $this->html( 'dataAfterContent' ); ?>
              <!-- /dataAfterContent -->
            <?php endif; ?>

            <div class="visualClear"></div>

            <!-- debughtml -->
            <?php $this->html( 'debughtml' ); ?>
            <!-- /debughtml -->

            <!-- pagestats -->
            <?php
              foreach( $this->getFooterLinks() as $category => $links ):
                if ( $category == 'info' ):
                  ?>
                    <br />
                    <div class="page-info">
                      <?php foreach( $links as $link ): ?>
                        <?php $this->html( $link ) ?>
                      <?php endforeach; ?>
                    </div>
                  <?php
                endif;
              endforeach;
            ?>
            <!-- /pagestats -->
          </article>
          <!-- /bodyContent -->
        </section>
        </div>

        <!-- panel -->
        <div class="span3 sidebar noprint" valign="top">
          <div class="well">
            <ul class="unstyled">
              <!-- logo -->
                <img src="<?php echo $wgStylePath; ?>/neverland/images/sidebar-logo.png" alt="" />
              <!-- /logo -->

              <?php
                $this->renderNavigation( 'VARIANTS' );
                $this->renderPortals( $this->data['sidebar'] );
                $this->renderNavigation( 'PERSONAL' );
              ?>
            </ul>
          </div>
        </div>
        <!-- /panel -->
      </div>

    <!-- /content -->

    <!-- footer -->
    <div id="footerRow">
      <div class="navbar navbar-bottom Neverland" <?php $this->html( 'userlangattributes' ) ?>>
        <div class="navbar-inner">
          <div class="container">
            <?php
              foreach( $this->getFooterLinks() as $category => $links ):
                if ( $category == 'places' ):
                  ?>
                    <ul id="footer-<?php echo $category ?>" class="nav">
                      <?php foreach( $links as $link ): ?>
                        <li>
                          <i class="icon-<?php echo $link ?> icon-white"></i>
                          <?php $this->html( $link ) ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php
                endif;
              endforeach;
            ?>

            <ul class="nav pull-right">
              <li id="global-nav-links" class="dropdown dropdown-hover">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-target="#global-nav-links">
                  <i class="icon-list icon-white"></i>
                  KDE Links
                  <b class="caret-up"></b>
                </a>

                <ul id="global-nav" class="dropdown-menu bottom-up"></ul>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <footer class="Neverland">
        <?php
          foreach( $this->getFooterLinks() as $category => $links ) {
            if ( $category == 'legals' ) {
              foreach( $links as $link ) {
                $this->html( $link );
              }
            }
          }
        ?>
      </footer>
    </div>
    <!-- /footer -->
  </div>
    <?php $this->printTrail(); ?>
  
    <script type="text/javascript" src="//cdn.kde.org/js/bootstrap.js"></script>
    <script type="text/javascript" src="//cdn.kde.org/js/bootstrap-neverland.js"></script>
    <script type="text/javascript" src="//cdn.kde.org/nav/global-nav.js"></script>
  </body>
</html>

<?php
  }

  /**
   * Render a series of portals
   *
   * @param $portals array
   */
  private function renderPortals( $portals ) {
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
      if ( $content === false )
        continue;

      echo "\n<!-- {$name} -->\n";
      switch( $name ) {
        case 'SEARCH':
          break;
        case 'TOOLBOX':
          $this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
          break;
        case 'LANGUAGES':
          if ( $this->data['language_urls'] ) {
            $this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
          }
          break;
        default:
          $this->renderPortal( $name, $content );
        break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }

  private function renderPortal( $name, $content, $msg = null, $hook = null ) {
    if ( $msg === null ) {
      $msg = $name;
    }
    
    ?>
      <li class="list-header" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>' <?php echo Linker::tooltip( 'p-' . $name ) ?>>
        <?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?>
      </li>
    <?php
    if ( is_array( $content ) ) {
      foreach( $content as $key => $val ) {
        echo $this->makeListItem( $key, $val );
      }

      if ( $hook !== null ) {
        wfRunHooks( $hook, array( &$this, true ) );
      }
    } else {
      echo $content; /* Allow raw HTML block to be defined by extensions */
    }
  }

  /**
   * Render one or more navigations elements by name, automatically reveresed
   * when UI is in RTL mode
   *
   * @param $elements array
   */
  private function renderNavigation( $elements ) {
    global $wgNeverlandUseSimpleSearch;

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
      echo "\n<!-- {$name} -->\n";
      switch ( $element ) {
        case 'NAMESPACES':
        if ( count( $this->data['namespace_urls'] ) > 0 ) {
          ?>
            <ul class="nav nav-tabs">
              <?php
                foreach ( $this->data['namespace_urls'] as $link ):
                  if ( stripos( $link['attributes'], 'selected' ) === false ): ?>
                  <li>
                    <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                    <?php echo htmlspecialchars( $link['text'] ) ?>
                    </a>
                  </li>
              <?php else: ?>
                <li class="active">
                  <a href="#" <?php echo $link['key'] ?>>
                    <?php echo htmlspecialchars( $link['text'] ) ?>
                  </a>
                </li>
              <?php
                endif;
                endforeach;
              ?>
            </ul>
          <?php
        }
        break;
        
        case 'VARIANTS':
        if ( count( $this->data['variant_urls'] ) > 0 ) {
          ?>
            <li class="list-header">
              <?php $this->msg( 'variants' ) ?>
            </li>
            
            <?php foreach ( $this->data['variant_urls'] as $link ): ?>
              <li <?php echo $link['attributes'] ?>>
                <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                  <?php echo htmlspecialchars( $link['text'] ) ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php
        }
        break;

        case 'VIEWS':
        ?>
          <div class="btn-group pull-right page-actions"> <!-- Is closed later in the 'actions' section -->
        <?php
        
        if ( count( $this->data['view_urls'] ) > 0 ) {
          ?>
            <?php foreach ( $this->data['view_urls'] as $link ): ?>
              <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-mini
                <?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
                  btn-primary
                <?php endif ?>"
                <?php echo $link['key'] ?>>

                <?php if ( array_key_exists( 'text', $link ) ): ?>
                  <i class="icon-<?php echo $link['id'] ?>
                      <?php if ( stripos( $link['attributes'], 'selected' ) === false ): ?>
                        icon-black
                      <?php else: ?>
                        icon-white
                      <?php endif; ?>"></i>
                  <?php
                    if ( strlen($link['text']) > 1 )
                      echo htmlspecialchars( $link['text'] )
                  ?>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          <?php
        }
        break;

        case 'ACTIONS':
        if ( count( $this->data['action_urls'] ) > 0 ) {
          ?>
            <a href="#" class="btn btn-mini dropdown-toggle" data-toggle="dropdown"
              title="<?php $this->msg( 'actions' ) ?>">
              <i class="icon-cog icon-black"></i>
              <span class="caret"></span>
            </a>

            <ul class="dropdown-menu">
              <?php foreach ( $this->data['action_urls'] as $link ): ?>
                <li <?php echo $link['attributes'] ?>>
                  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                    <i class="icon-<?php echo $link['id'] ?> icon-black"></i>
                    <?php echo htmlspecialchars( $link['text'] ) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php
        }
        
        ?>
          </div> <!-- Opened in the 'views' section -->
        <?php
        break;
        
        case 'PERSONAL':
        if ( count( $this->data['personal_urls'] ) > 0 ) {
          ?>
            <li class="list-header">
              <?php $this->msg( 'personaltools' ) ?>
            </li>

            <?php foreach( $this->getPersonalTools() as $key => $item ) { ?>
              <?php echo $this->makeListItem( $key, $item ); ?>
            <?php } ?>
          <?php
        }
        break;

        case 'SEARCH':
        ?>
          <div id="p-search">
            <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="form-inline">
              <input id="searchInput" name="search" type="search" placeholder="<?php $this->msg( 'search' ) ?>"
                   class="input-large" autocomplete="off"
              <?php if( isset( $this->data['search'] ) ): ?>
                value="<?php $this->text( 'search' ) ?>"
              <?php endif; ?> />

              <input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
            </form>
          </div>
        <?php
        break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }
}
