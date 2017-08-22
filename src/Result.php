<?php

namespace Imjoehaines\Flowder\Validator;

final class Result
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $errors
     */
    private function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return Result
     */
    public static function valid()
    {
        return new static();
    }

    /**
     * @param array $errors
     * @return Result
     */
    public static function invalid(array $errors)
    {
        return new static($errors);
    }
}
