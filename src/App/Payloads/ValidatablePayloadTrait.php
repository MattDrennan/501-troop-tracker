<?php

namespace App\Payloads;

use App\Utilities\PayloadableTrait;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validatable;
use \Exception;

trait ValidatablePayloadTrait
{
    /**
     * @var array|null Stores the validation result: ['isValid' => bool, 'errors' => array]
     */
    protected ?array $validationResult = null;

    /**
     * Get the validator instance for the request.
     *
     * @return Validatable The validator instance.
     */
    abstract protected function getValidator(): Validatable;

    /**
     * Whether or not the request data is valid
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        $this->ValidatePayloadTrait();

        // Check if the result is already cached (lazy loading)
        if ($this->validationResult !== null) {
            return $this->validationResult['isValid'];
        }

        $validator = $this->getValidator();
        $data = $this->getDataPayload();
        $errors = [];

        try {
            $validator->assert($data);
            $isValid = true;
        } catch (NestedValidationException $e) {
            // Flatten errors into an easy-to-use array
            $errors = $e->getMessages();
            $isValid = false;
        }

        // Cache the result for future calls
        $this->validationResult = [
            'isValid' => $isValid,
            'errors' => $errors
        ];

        return $isValid;
    }

    /**
     * Ensures the PayloadableTrait is present in the using class.
     * @throws Exception
     */
    private function ValidatePayloadTrait(): void
    {
        $required_trait = PayloadableTrait::class;

        $used_traits = class_uses($this);

        if (!isset($used_traits[$required_trait])) {
            $class_name = get_class($this);
            $msg = "The class '{$class_name}' uses ValidatableTrait but is missing the required trait: {$required_trait}.";

            throw new Exception($msg);
        }
    }
    /**
     * Returns the validation errors. Automatically calls isValid() if validation hasn't run.
     * 
     * @return array
     */
    public function getErrors(): array
    {
        // Ensure validation has run (this populates $this->validationResult)
        if ($this->validationResult === null) {
            $this->isValid();
        }

        return $this->validationResult['errors'] ?? [];
    }
}