<?php

namespace View\Content;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('content/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotHeadJavascript() {
        return parent::slotHeadJavascript() . 
        '<script type="text/javascript">
            window.ENTER = window.ENTER || {};
            window.ENTER.config = {
            "regionId": '. \App::user()->getRegion()->getId() .'
            }
        </script>';
    }

    public function slotMetaOg() {
        $imageUrl = $this->getParam('imageUrl');
        if (!$imageUrl) {
            $imageUrl = 'http://' . \App::config()->mainHost . '/images/logo.png';
        }

        $description = $this->getParam('description');
        if (!$description) {
            $description = \App::config()->description;
        }

        return '
            <meta property="og:title" content="' . $this->escape($this->getTitle()) . '"/>
            <meta property="og:description" content="' . $this->escape($description) . '"/>
            <meta property="og:image" content="' . $this->escape($imageUrl) . '"/>
            <meta property="og:site_name" content="ENTER"/>
            <meta property="og:type" content="website"/>
            <link rel="image_src" href="' . $this->escape($imageUrl) . '" />
        ';
    }
}
