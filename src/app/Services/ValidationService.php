<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ValidationService
{
    /** @var Validator */
    private $validator;

    /**
     * ValidationService constructor.
     */
    public function __construct()
    {
        $this->validator = Validator::make([], []);
    }

    /**
     * Validate data. Returns true if data is valid
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return bool
     */
    public function validate(array $data, array $rules, array $messages = []): bool
    {
        $this->validator = Validator::make($data, $rules, $messages);
        return !$this->validator->fails();
    }

    /**
     * Returns false if validation has been succeeded
     * @return bool
     */
    public function fails(): bool
    {
        return $this->validator->fails();
    }

    /**
     * Returns array with errors
     * @return array
     */
    public function getMessages(): array
    {
        return $this->validator
            ->getMessageBag()
            ->getMessages();
    }

    /**
     * Transformation array errors to string
     * @param array $errors
     * @param string $separator
     * @return string
     */
    public function getErrorsAsString(array $errors = [], string $separator = "\n"): string
    {
        if (empty($errors)) {
            $errors = $this->getMessages();
        }
        return $this->parseArray($errors, $separator);
    }

    /**
     * Walk on array and get errors string
     * @param array|string $error
     * @param string $separator
     * @return string
     */
    private function parseArray($error, string $separator): string
    {
        $errStr = '';
        if (is_array($error)) {
            foreach ($error as $item) {
                $errStr .= $this->parseArray($item, $separator);
            }
        } elseif (is_string($error)) {
            $errStr .= $separator;
            $errStr .= $error;
        }
        return $errStr;
    }
}
