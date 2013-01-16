<?php

namespace View\Tag;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        /** @var $tag \Model\Tag\Entity */
        $tag = $this->getParam('tag') instanceof \Model\Tag\Entity ? $this->getParam('tag') : null;
        if (!$tag) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Теги &rsaquo; ' . $tag->getName(),
                'url'  => \App::router()->generate('tag', array('tagToken' => $tag->getToken())),
            );

            /** @var $category \Model\Product\Category\Entity */
            $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
            if ($category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => null, // потому что последний элемент ;)
                );
            }

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $this->setTitle(\Util\String::ucfirst_utf8($tag->getName()));
        if (!$this->hasParam('title')) {
            $this->setParam('title', \Util\String::ucfirst_utf8($tag->getName()));
        }
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tag/page-index', $this->params);
    }

    public function slotSidebar() {
        if (!(bool)$this->getParam('categories')) {
            return  '';
        }

        return $this->render('tag/_sidebar', array_merge($this->params, array(
            'selectedCategory' => $this->getParam('category'),
            'limit'            => 8,
        )));
    }
}