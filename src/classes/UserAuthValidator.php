<?php

use \Respect\Validation\Validator as v;

class UserAuthValidator extends UserValidator
{
    public function initRules()
    {
        $this->rules['username'] = v::alnum('_')->noWhitespace()->length(4, 20)->setName('username');
        $this->rules['password'] = v::alnum('_')->noWhitespace()->length(8, 20)->setName('password');
    }
}