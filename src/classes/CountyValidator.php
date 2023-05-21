<?php

use \Respect\Validation\Validator as v;

class CountyValidator
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
        $this->rules['name'] = v::alpha(' ')->length(3, 100)->setName('name');
        $this->rules['population'] = v::intType()->min(0)->setName('population');
        $this->rules['state_id'] = v::intType()->between(1,51)->setName('state_id');
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
