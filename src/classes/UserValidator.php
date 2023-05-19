<?php

use \Respect\Validation\Validator as v;

class UserValidator
{
    /**
     * List of constraints
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Payload to validate
     * @var array
     */
    protected $data = [];

    public function __construct($data)
    {
        $this->data = $data;
        $this->initRules();
    }

    /**
     * Set the user subscription constraints
     *
     * @return void
     */
    public function initRules()
    {
        $this->rules['username'] = v::alnum('_')->noWhitespace()->length(4, 20)->setName('username');
        $this->rules['password'] = v::alnum('_')->noWhitespace()->length(8, 20)->setName('password');

        if (isset($this->data['password'])) {
            $this->rules['passwordConfirmation'] = v::equals($this->data['password'])->setName('passwordConfirmation');
        }
    }

    /**
     * Assert validation rules.
     *
     * @param array $inputs
     *   The inputs to validate.
     * @return boolean
     *   True on success; otherwise, false.
     */
    public function assert()
    {
        foreach ($this->rules as $rule => $validator) {
            $this->data[$rule] = $this->data[$rule] ?? "";
            $validator->assert($this->data[$rule]);
        }

        return true;
    }
}
