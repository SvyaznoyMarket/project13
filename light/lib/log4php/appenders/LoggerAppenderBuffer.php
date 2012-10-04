<?php
class LoggerAppenderBuffer extends LoggerAppender
{
    const name = 'bufferAppender';

    private $messageList;

	public function append(LoggerLoggingEvent $event)
    {
	    $this->messageList[] = $this->layout->format($event);
	}

    public function getMessageList()
    {
        return $this->messageList;
    }

    public function getName()
    {
        return self::name;
    }

    public function setThreshold($treshold)
    {
        parent::setThreshold($treshold);
    }
}

