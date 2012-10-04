<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LastModifiedHandler
 *
 */
class LastModifiedHandler {
 
    static public function setLastModified($datestamp='')
    {
        if (!$datestamp) $datestamp = time();
        
        $response = \light\App::getResponse();

        if(is_array($datestamp))
        {
          rsort($datestamp, SORT_NUMERIC);
          $datestamp = $datestamp[0];
        }

        if(!$response->hasHttpHeader('Last-Modified'))
        {
          $response->setHttpHeader('Last-Modified', $response->getDate($datestamp));
        }
        else
        {
          $origLastModified = strtotime($response->getHttpHeader('Last-Modified'));
          if($origLastModified < $datestamp)
            $response->setHttpHeader('Last-Modified', $response->getDate($datestamp));
        }
    }    
}

?>
