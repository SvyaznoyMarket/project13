<?php

namespace controller\MainMenu;

class Get {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $templating = \App::templating();

        $regionId = $request->query->get('regionId');
        $region = new \Model\Region\Entity(['id' => $regionId]);

        $menu = (new \View\Menu())->generate($region);

        $content = (new \View\DefaultLayout())->render('common/_navigation', ['menu' => $menu]);

        return new \Http\Response($content);
    }

} 