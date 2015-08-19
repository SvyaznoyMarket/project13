<?php


namespace View;


class SwitchView extends DefaultLayout {

    protected $layout = 'layout-clear';

    public function slotContent()
    {
        return $this->render('debug/_abtests', ['tests' => $this->getParam('tests')]);
    }

    public function slotHeadJavascript()
    {
        return '
        <script src="//yastatic.net/jquery/1.11.1/jquery.min.js" type="text/javascript" ></script>
        <script src="//yastatic.net/jquery/cookie/1.0/jquery.cookie.min.js" type="text/javascript" ></script>
        ';
    }


} 