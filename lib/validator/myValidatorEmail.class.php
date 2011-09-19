<?php

class myValidatorEmail extends sfValidatorEmail
{
  const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
}
