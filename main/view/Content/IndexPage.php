<?php

namespace View\Content;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array(
                array(
                    'name' => 'Помощь пользователю',
                    'url' => null,
                    ),
            );


            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    public function slotContent() {
        $return = $this->render('content/page-index', $this->params);
        if ( in_array($this->getParam('token'), ['enter-friends'] )) {
            // Используется также на стр /be-friends, /view/Friendship/IndexPage
            $return .= $this->render('partner-counter/_flocktory_popup', $this->params);
        }
        return $return;
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


}
