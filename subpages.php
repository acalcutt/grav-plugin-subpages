<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;

/**
 * Subpages Plugin
 *
 * Displays a list of direct sub-pages of the current page.
 *
 * @author Andrew Calcutt <acalcutt@techidiots.net>
 * @license MIT
 */
class SubpagesPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
           'onPluginsInitialized' => ['onPluginsInitialized', 0],
           'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ];
    }

    public function onPluginsInitialized()
    {
        $this->enable([
           'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0]
        ]);
    }

    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        $page = $this->grav['page'];
        // Get visible subpages
        $subpages = $this->getVisibleSubpages($page);

        $parent = $page->parent();

        $this->grav['twig']->twig_vars['subpages'] = $subpages;
        $this->grav['twig']->twig_vars['parent'] = $parent;
        $this->grav['twig']->twig_vars['subpages_config'] = $this->config->get('plugins.subpages');

    }


     /**
     * Get visible subpages
     *
     * @param Page $page
     *
     * @return array
     */
    private function getVisibleSubpages(Page $page)
    {
         $subpages = [];
         $children = $page->children();
         if( $children ){
             foreach ($children as $child) {
                  if( !$child->routable() ) continue;
                  if( !$child->visible() ) continue;
                  $subpages[] = $child;
             }
         }

         return $subpages;
    }
}
