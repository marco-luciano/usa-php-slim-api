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
     * List of customized messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * List of returned errors in case of a failing assertion
     *
     * @var array
     */
    protected $errors = [];

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
        $this->rules['password'] = v::alnum()->noWhitespace()->length(8, 20)->setName('password');
        $this->rules['passwordConfirmation'] = v::alnum()->noWhitespace()->length(8, 20)->setName('passwordConfirmation');
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

        v::equals($this->data['password'])->assert($this->data['passwordConfirmation']);

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}
