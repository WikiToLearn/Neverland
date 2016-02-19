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

$wgValidSkinNames['neverland'] = 'Neverland';

/**
 * SkinTemplate class for Neverland skin
 * @ingroup Skins
 */
class SkinNeverland extends SkinTemplate {

  var $skinname = 'Neverland', $stylename = 'Neverland',
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
    $out->addMeta( "viewport", "width=device-width, initial-scale=1.0" );
  }

  /**
   * Load skin and user CSS files in the correct order
   * @param $out OutputPage object
   */
  function setupSkinUserCss( OutputPage $out ){
    parent::setupSkinUserCss( $out );
    $out->addStyle( $this->stylename.'/css/bootstrap.min.css', 'screen' );
    $out->addStyle( $this->stylename.'/css/bootstrap-social.css', 'screen' );
    $out->addStyle( $this->stylename.'/css/bootstrap-mediawiki.css', 'screen' );
    $out->addStyle( $this->stylename.'/css/font-awesome-4.4.0/css/font-awesome.css', 'screen' );
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

    $skin = $this->getSkin();
    $user = $skin->getUser();
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
    
    $bigTitle = "";
    $subpages = "";
    
    if ( $wgOut->isArticle() ) { // && MWNamespace::hasSubpages( $wgOut->getTitle()->getNamespace() ) ) {
        $ptext = $wgOut->getTitle()->getText(); //->getPrefixedText();
        if ( preg_match( '/\//', $ptext ) ) {
            $links = explode( '/', $ptext );
//             array_pop( $links );
            $c = 0;
            $growinglink = '';
            $display = '';

            $subpages .= '<ul class="breadcrumb">';

            foreach ( $links as $link ) {
                $subpages .= "<li>";
                $growinglink .= $link;
                $display .= $link;
                $linkObj = Title::newFromText( $growinglink );

                if ( is_object( $linkObj ) && $linkObj->isKnown() ) {
                    $getlink = Linker::linkKnown(
                            $linkObj,
                            htmlspecialchars( $display )
                    );

                    $c++;

                    if ( $c < count($links) ) {
                        $subpages .= $getlink;
                        $subpages .= '<span class="divider">/</span>';
                    } else {
                        $subpages .= '<li class="active">';
                        $subpages .= $display;
                        $subpages .= '</li>';
                        $bigTitle = $display;
                    }

                    $display = '';
                } else {
                        $display .= '/';
                        
                }
                $growinglink .= '/';
                
                $subpages .= "</li>";
            }
            $subpages .= '</ul>';
        }
    }
    
    if ($bigTitle == "") {
        $bigTitle = $wgOut->getTitle()->getText();
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
<nav class="navbar navbar-default navbar-inverse navbar-static-top Neverland noprint">
  <div class="container">
      <div class="row">
          <div class="col-sm-6">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a id="header-title" href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" class="navbar-brand" style="color:#999;    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);font-size:141%;">
                    <?php echo $wgSitename; ?>
                </a>
              </div>
          </div>
          <div class="col-sm-6">
              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse row" id="bs-example-navbar-collapse-1">
                <form class="navbar-form navbar-right" role="search">
                  <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                          <input type="text" class="form-control" placeholder="Search" style="max-width: 178%;">
                        </div>
                    </div>
                  </div>
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown col-xs-6 col-lg-10 col-md-10 col-sm-10">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <?php echo $user->getName(); ?> <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <?php
                                // generate user tools (and notifications item in user tools if needed)
                                $personalToolsCount = 0;
                                foreach ( $this->getPersonalTools() as $key => $tool ) {
                                    $tool['class'] = 'header-dropdown-item'; // add the "header-dropdown-item" class to each li element
                                    echo $this->makeListItem( $key, $tool );
                                    if ( class_exists( 'EchoHooks' ) && $this->data['loggedin'] && $personalToolsCount == 2 ) { // if Echo is installed, user is logged in, and the first two tools have been generated (user and user talk)...
                        ?>
                                            <li id="pt-notifications-personaltools" class="header-dropdown-item">
                        <?php
                                                echo Linker::link(
                                                    SpecialPage::getTitleFor( 'Notifications' ),
                                                    $this->getMsg( 'notifications' )->plain(),
                                                    Linker::tooltipAndAccesskeyAttribs( 'pt-notifications' )
                                                )
                        ?>
                                            </li>
                        <?php
                                    } else {
                                      //echo '<div><a href="javascript:void(0)" class="mw-echo-notifications-badge" style="color:rgba(0, 0, 0, 0);">0</a></div>';
                                    }
                                    $personalToolsCount++;
                                }
                        ?>
                      </ul>
                    </li>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="list-style:none;float:right;padding-top:15px;padding-left:8px;color:rgba(0, 0, 0, 0);cursor:default;" id="echo" >0</div>
                    <div class="col-xs-12 visible-xs">
                      <ul class="wtl-menu-mobile list-group list-unstyled">
                        <?php 
                          $this->renderPortals( $this->data['sidebar'] );
                        ?>
                      </ul>
                    </div>
                </ul>
              </div><!-- /.navbar-collapse -->
          </div>
      </div>
  </div><!-- /.container-fluid -->
</nav>
<!-- /header -->

  <div id="top" class="container">
    <!-- content -->
    <div class="row">
        <!-- panel -->
        <div id="p-side" class="col-sm-3 wikimenu noprint">

          <!-- logo -->
            <a href="//www.wikitolearn.org">
              <img id="wfm-logo" src="<?php echo $wgLogo; ?>" alt="WikiToLearn Logo" />
            </a>
          <!-- /logo -->

          <div class="wtl-menu">        
            <ul>
              <?php
                $this->renderNavigation( 'VARIANTS' );
                $this->renderPortals( $this->data['sidebar'] );
//                $this->renderNavigation( 'PERSONAL' );
              ?>
            </ul>
          </div>

        </div>

        <div class="col-sm-9">
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

        <div id="content row">
          <div class="revisionbadge col-xs-12" id="siteSub">
            <?php $this->html( 'subtitle' ) ?>
          </div>
            
          <!-- firstHeading -->
          <header>
            <h1 id="firstHeading">
              <?php print $bigTitle; ?>
              <?php /*$this->html( 'title' )*/ ?>
            </h1>
          </header>
          
          <!-- /firstHeading -->

          <!-- bodyContent -->
          <article>
          
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
            
          <div id="bodyContent">
          
            <!-- subtitle -->
            <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>>
              <?php print $subpages; ?>
            </div>
            <!-- /subtitle -->  

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
          
          </div>
          
          </article>
        </div> <!--content-->
          <!-- /bodyContent -->
          <div class="minchiatina">
            <div class="a"></div>
            <div class="b"></div>
            <div class="c"></div>
            <div class="d"></div>
            <div class="e"></div>
          </div>
          
        </section>
        </div>

        <!-- /panel -->
      </div>



    <!-- /content -->
  </div>

  <!-- footer -->
  <div class="footer noprint">
    <!-- pagestats -->
    <div class="container">
      <div class="row text-left footer-wtl">
        <div class="col-sm-1 hidden-xs"></div>
        <div class="col-sm-3 hidden-xs">
          <a href="wikitolearn.org?page=main_page">
            <img class="img-responsive" style="max-width:88px;" src="/skins/Neverland/images/logos/en.png" alt="">
          </a>
        </div>
        <div class="col-sm-3 col-xs-6">
          <h3>
            Contacts
          </h3>
          <ul class="list-unstyled">
            <li>
            Mail: <a href="mailto:info@wikitolearn.org">info@wikitlearn.org</a>
            </li>
            <li>
                <a href="wikitolearn.org?page=mailing_list">Mailing list</a>
            </li>
            <li>
                <a href="wikitloearn.org?page=communication_channel">Communication channels</a>
            </li>
          </ul>
        </div>
        <div class="col-sm-3 col-xs-6">
          <h3>
            Learn More
          </h3>
          <ul class="list-unstyled">
            <li>
              <a href="wikitolearn.org?page=about_us">About us</a>
            </li>
            <li>
              <a href="wikitolearn.org?page=subscribe">Subscribe to our newsletter!</a>
            </li>
          </ul>
        </div>
        <div class="col-xs-12 visible-xs" style="height: 20px;"></div>
        <div class="col-xs-6 visible-xs">
          <a href="wikitolearn.org?page=main_page">
            <img class="img-responsive center-block" style="max-width:88px;" src="/skins/Neverland/images/logos/en.png" alt="">
          </a>
        </div>
        <div class="col-sm-2 col-xs-6">
          <h3>
            Social
          </h3>
          <a class="btn btn-social-icon btn-twitter" href="https://twitter.com/WikiToLearn">
            <span class="fa fa-twitter"></span>
          </a>
          <a class="btn btn-social-icon btn-facebook" href="https://www.facebook.com/WikiToLearn">
            <span class="fa fa-facebook"></span>
          </a>
        </div>
      </div>
      <hr>
      <div class="row">
        <?php
        foreach( $this->getFooterLinks() as $category => $links ):
          if ( $category == 'info' ):
           foreach( $links as $link ): ?>
         <p><?php $this->html( $link ) ?></p>
       <?php endforeach; 
       endif;
       endforeach;
       ?>
       <!-- /pagestats -->
       <?php
       foreach( $this->getFooterLinks() as $category => $links ) {
        if ( $category == 'legals' ) {
          foreach( $links as $link ) {
            $this->html( $link );
          }
        }
      }
      ?>
    </div>
    </div>
  </div>
  <!-- /footer -->

            <!-- Divs to detect breakpoint using javascript -->
            <div class="device-xs hidden-xs breakpoint-xs"></div>
            <div class="device-sm hidden-sm breakpoint-sm"></div>

    <?php $this->printTrail(); ?>
  
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $wgStylePath; ?>/Neverland/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        /* Fix for Echo in Refreshed */
        if ( document.getElementById( 'echo' ) ) {
                $( '#pt-notifications' ).prependTo( '#echo' );
        }

        if ( $( '.mw-echo-notifications-badge' ).hasClass( 'mw-echo-unread-notifications' ) ) {
                $( '#pt-notifications-personaltools a' ).addClass( 'pt-notifications-personaltools-unread' );
        }

        function isBreakpoint( alias ) {
          return $('.device-' + alias).is(':visible');
        }
          
        $( document ).ready(function() {

        $('form[name=userlogin]').addClass("col-xs-12");
        $('#userloginForm').addClass("row");
        $('#userlogin2').addClass("col-xs-12"); 

        if ($('body').hasClass('page-Pagina_principale')) {
          $('.nav.nav-tabs').hide();
          $('.btn-group.pull-right.page-actions').hide();
          $('#firstHeading').hide();
        };

        if( $('.breakpoint-xs').is(':hidden') ) {
          $('.wtl-menu').hide();
          $('.footer-wtl').addClass(" text-center ").removeClass(" text-left ");
        }
        if( $('.breakpoint-sm').is(':hidden') ) {
          $('.wtl-menu-mobile').hide();
        }
        });

        
        
    </script>
    <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript">
    window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience on our website","dismiss":"Got it!","learnMore":"More info","link":null,"theme":"dark-bottom"};
    </script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
    <!-- End Cookie Consent plugin -->

    <!--<script type="text/javascript" src="//cdn.kde.org/js/bootstrap-neverland.js"></script>
    <script type="text/javascript" src="//cdn.kde.org/nav/global-nav.js"></script>-->
    <!--<script type="text/javascript">
      $(document).ready(function(){
        $('span.mailme').mailme();
        $("[rel='tipsy-north']").tipsy({'gravity':'n'});
        $("[rel='tipsy-east']").tipsy({'gravity':'e'});
        $("[rel='tipsy-west']").tipsy({'gravity':'w'});
        $("[rel='tipsy-south']").tipsy({'gravity':'s'});
        $("#ca-nstab-main img").click(function(event){
          $(this).toggleClass("down");
        });
        $("#ca-talk img").click(function(event){
          $(this).toggleClass("down");
        });
        $("#ca-special img").click(function(event){
          $(this).toggleClass("down");
        });
        $("#ca-history img").click(function(event){
          $(this).toggleClass("down");
        });
        $("#ca-latex img").click(function(event){
          $(this).toggleClass("down");
        });
        $("#ca-viewsource img").click(function(event){
          $(this).toggleClass("down");
        });
        setSidebar();
        maxEntries=25;
        hideLongToc();
      });
      $(window).resize(function(){
        fixLayout();
      });
    </script>-->
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
            <ul class="nav nav-tabs noprint">
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
          <div class="btn-group pull-right page-actions noprint"> <!-- Is closed later in the 'actions' section -->
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
<!--             <li class="list-header"> -->
<!--               <?php $this->msg( 'personaltools' ) ?> -->
<!--             </li> -->

            <?php foreach( $this->getPersonalTools() as $key => $item ) { ?>
              <?php echo $this->makeListItem( $key, $item ); ?>
            <?php } ?>
          <?php
        }
        break;

        case 'SEARCH':
        ?>
            <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="navbar-search pull-right">
<!--             <div class="input-append"> -->
              <input id="searchInput" name="search" type="search" placeholder="<?php $this->msg( 'search' ) ?>"
                   class="search" autocomplete="off"
              <?php if( isset( $this->data['search'] ) ): ?>
                value="<?php $this->text( 'search' ) ?>"
              <?php endif; ?> />
<!--               <button class="btn"><i class="icon-search"></i></button> -->
<!--             </div> -->

              <input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
            </form>
        <?php
        break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }
}
