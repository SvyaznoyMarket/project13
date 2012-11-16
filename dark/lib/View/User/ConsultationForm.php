<?php

namespace View\User;

class ConsultationForm {
    /** @var string */
    private $name;
    /** @var string */
    private $email;
    /** @var string */
    private $subject;
    /** @var string */
    private $message;
    /** @var array */
    private $errors = array(
        'global'  => null,
        'name'    => null,
        'email'   => null,
        'subject' => null,
        'message' => null,
    );

    public function __construct(array $data = array()) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('subject', $data)) $this->setSubject($data['subject']);
        if (array_key_exists('message', $data)) $this->setMessage($data['message']);
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = trim((string)$email);
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = trim((string)$message);
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = trim((string)$name);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->subject = trim((string)$subject);
    }

    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}