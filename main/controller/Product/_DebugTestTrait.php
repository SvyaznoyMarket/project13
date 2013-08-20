<?php

namespace Controller\Product;

trait _DebugTestTrait
{

    public function debug($productId, \Http\Request $request)
    {
        $res = $this->execute($productId, $request);

        /* @var $res \Http\JsonResponse */
        if ($res instanceof \Http\JsonResponse) {
            $content = $res->getContent();

            $json = json_decode($content);

            if (isset($json->success) && $json->success) {
                self::echlog([], 'SUCCESS!');
                print_r($json->content);
                return true;
            } else {
                self::echlog([], 'ERROR! Not Success!');
                print_r($json);
                return false;
            }
        } else {
            self::echlog([], 'ERROR! Not Object!');
            print_r($res);
        }

    }


    private function echlog($var = [], $name = '')
    {
        print '<pre>';
        if ($name) print '### ' . $name;
        if (!empty($var)) print_r($var);
        print '</pre>';
    }

}

