<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;

class SubpagesPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            "onPluginsInitialized" => ["onPluginsInitialized", 0],
            "onTwigSiteVariables" => ["onTwigSiteVariables", 0],
        ];
    }

    public function onPluginsInitialized()
    {
        // Check if we're in admin and plugin is enabled
        if (
            $this->isAdmin() &&
            $this->config->get("plugins.subpages.enabled")
        ) {
            $this->enable([
                "onTwigTemplatePaths" => ["onTwigTemplatePaths", 0],
                "onTwigSiteVariables" => ["onTwigSiteVariables", 0],
            ]);
        } else {
            $this->enable([
                "onTwigTemplatePaths" => ["onTwigTemplatePaths", 0],
                "onTwigSiteVariables" => ["onTwigSiteVariables", 0],
            ]);
        }
    }

    public function onTwigTemplatePaths()
    {
        $this->grav["twig"]->twig_paths[] = __DIR__ . "/templates";
    }

    public function onTwigSiteVariables()
    {
        if ($this->config->get("plugins.subpages.enabled")) {
            $page = $this->grav["page"];

            // Get visible subpages
            $subpages = $this->getVisibleSubpages($page);

            $parent = $this->getParentPage($page);

            $this->grav["twig"]->twig_vars["subpages"] = $subpages;
            $this->grav["twig"]->twig_vars["parent"] = $parent;
            $this->grav["twig"]->twig_vars[
                "subpages_config"
            ] = $this->config->get("plugins.subpages");
        }
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
        if ($children) {
            foreach ($children as $child) {
                if (!$child->routable()) {
                    continue;
                }
                if (!$child->visible()) {
                    continue;
                }
                $subpages[] = $child;
            }
        }

        return $subpages;
    }
    /**
     * Get the parent page, if it exists. Return null if it does not
     * @param Page $page
     *
     * @return Page|null
     */
    private function getParentPage(Page $page)
    {
        if ($page->parent() !== null && $page->route() !== "/") {
            return $page->parent();
        }
        return null;
    }
}
