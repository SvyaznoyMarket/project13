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

    public function slotHeadJavascript()
    {
        return parent::slotHeadJavascript() . 
        '<script type="text/javascript">
            window.ENTER = window.ENTER || {};
            window.ENTER.config = {
            "regionId": '. \App::user()->getRegion()->getId() .'
            }
        </script>';
    }

    public function slotMetaOg()
    {
        $result = '';
        $image_url = null;
        $image_pattern = <<<EOF
/<img\s+src\s*=\s*(["'][^"']+["']|[^>]+)>/
EOF;

        /* Выдергиваем первую картинку из контента */
        if ($this->params['htmlContent']) {
            preg_match($image_pattern, $this->params['htmlContent'], $image_matches);
        }

        if (isset($image_matches) && isset($image_matches[1])) $image_url = $image_matches[1];

        if ($image_url) {
            $result .=  "<meta property=\"og:image\" content=" . $image_url . " />\r\n".
                        "<link rel=\"image_src\" href=". $image_url . " />\r\n";
        }

        return $result;
    }


}
