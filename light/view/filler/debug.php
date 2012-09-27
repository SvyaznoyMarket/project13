<?php

namespace light;

class FillerDebug implements IFiller
{
    public function run()
    {
        $renderer = App::getHtmlRenderer();
        $renderer->addParameter('memoryUsage', memory_get_usage($realUsage = True));

        $extensionList = get_loaded_extensions();
        $systemName = php_uname();
        $phpVersion = phpversion();
        $phpIniFile = php_ini_loaded_file();
        $phpSAPIName = php_sapi_name();

        $renderer->addParameter('extensionList', $extensionList);
        $renderer->addParameter('phpVersion', $phpVersion);
        $renderer->addParameter('systemName', $systemName);
        $renderer->addParameter('phpIniFile', $phpIniFile);
        $renderer->addParameter('phpSAPIName', $phpSAPIName);


        $requestHeaderList = apache_request_headers();
        $requestParameterList = $_REQUEST;

        $renderer->addParameter('requestHeaderList', $requestHeaderList);
        $renderer->addParameter('requestParameterList', $requestParameterList);

        $responseHeaderList = apache_response_headers();
        $renderer->addParameter('responseHeaderList', $responseHeaderList);

        $settingParameterList = $this->formatParameterList(Config::get());
        $renderer->addParameter('settingParameterList', $settingParameterList);

        $timeList = TimeDebug::getAll();
        $renderer->addParameter('timeList', $timeList);

        $totalTime = 0;
        foreach($timeList as $time)
        {
            $totalTime += $time[0]['delta'];
        }
        $renderer->addParameter('totalTime', $totalTime);

        $appenderList = \Logger::getRootLogger()->getAllAppenders();
        $bufferAppender = $appenderList[\LoggerAppenderBuffer::name];
        $messageList = $bufferAppender->getMessageList();
        $renderer->addParameter('messageList', $messageList);

        $appenderList = \Logger::getLogger('CoreClient')->getAllAppenders();
        $bufferAppender = $appenderList[\LoggerAppenderBuffer::name];
        $messageList = $bufferAppender->getMessageList();

        $renderer->addParameter('coreClientMessageList', $messageList);
        $renderer->addParameter('coreRequestCount', count($messageList));

    }

    private function formatParameterList($parameterList)
    {
        $result = array();

        foreach($parameterList as $index => $value)
        {
            if(is_array($value))
            {
                $subParameterList = $this->formatParameterList($value);
                foreach($subParameterList as $subIndex => $subValue)
                {
                    $result[$index . '.' . $subIndex] = $subValue;
                }
            }
            else
            {
                $result[$index] = $value;
            }

        }

        return $result;
    }
}