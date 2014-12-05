<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
    $TLT_TARGET_VERSION = "1.2";
    $TLT_TARGET_RESP_CONTENT_TYPE = "text/plain";

    header("Content-Type: ".$TLT_TARGET_RESP_CONTENT_TYPE);

    /*
     * Utility function to return the timestamp in milliseconds.
     */
    function MSTimestamp()
    {
      $ms = microtime(true) * 1000;
      return $ms;
    }

    /*
     * Read the raw POST data to ensure the request is read before the response is generated.
     */
    function ProcessPost()
    {
      if (!strcasecmp($_SERVER['REQUEST_METHOD'], "POST")) {
        $start = MSTimestamp();
        $actualReadLength= 0;
        $maxReadLength = 0;
        $html = "";

        try {
          // Limit the read size to at most 12K bytes.
          $reqLength = isset($_SERVER['HTTP_CONTENT_LENGTH']) ? $_SERVER['HTTP_CONTENT_LENGTH'] : 0;
          $maxReadLength = (!$reqLength || $reqLength > 12288) ? 12288 : $reqLength;

          // Open the input stream for access to raw POST data
          $postFileHandle = fopen("php://input", 'rb');
          if ($postFileHandle) {
            $postData = fread($postFileHandle, $maxReadLength);
            if ($postData) {
              $actualReadLength = strlen($postData);
            }
            else {
              $html .= "<br />\r\nFailed to read the raw POST data. Read of the input stream failed.\r\n<br />\r\n";
            }
            $postData = null;
            fclose($postFileHandle);
          }
          else {
            $html .= "<br />\r\nFailed to read the raw POST data. Open of the input stream failed.\r\n<br />\r\n";
          }
        }
        catch (Exception $e) {
          $html .= "<br />\r\nException when reading request data!\r\n<br />\r\n".$e->getMessage()."\r\n<br />\r\n";
        }

        $end = MSTimestamp();

        $html .= "<hr>\r\n\r\nRead $actualReadLength bytes in ".round($end-$start, 2)."ms\r\n\r\n";
        return $html;
      }

      return "";
    }

    /*
     * Check for URL arguments to send optional debugging information back to the client
     * 
     * Supported arguments:
     * "server": sends a hash value of the server name and IP. Useful to identify the specific
     *           system when the target is deployed on multiple webservers.
     */
    function ProcessArguments()
    {
      $html = "";
      $includeServer = isset($_GET['server']);

      if ($includeServer) {
        $serverName = isset($_SERVER['SERVER_NAME']) ? md5($_SERVER['SERVER_NAME']) : 'unknown';
        $serverIP = isset($_SERVER['SERVER_ADDR']) ? md5($_SERVER['SERVER_ADDR']) : (isset($_SERVER['LOCAL_ADDR']) ? md5($_SERVER['LOCAL_ADDR']) : 'unknown');
        // On some web servers (IIS) SERVER_ADDR does not exist. Instead use LOCAL_ADDR.
        
        $html .= "<br />\r\n$serverName\r\n";
        $html .= "<br />\r\n$serverIP\r\n";
      }

      return $html;
    }
?>

<html>
<head><title>Tealeaf Target</title></head>
<body>
<?php
      echo "\r\nTealeaf Target $TLT_TARGET_VERSION\r\n<br />\r\n";
      echo ProcessPost();
      echo ProcessArguments();
?>
</body>
</html>
