<?php
declare(strict_types=1);


namespace App\Infrastructure\Validation;

use JsonSchema\Validator;

class JsonSchemaValidator
{
    const string SCHEMA_DIR = __DIR__.'/Schemas/';
    private Validator $validator;
    private string $jsonValidatorPath;
    private array $dataObject;
    private array $errors;

    public function __construct(string $schemaFile)
    {
        $this->validator = new Validator();
        $this->jsonValidatorPath = self::SCHEMA_DIR.$schemaFile;
        $this->dataObject = [];
        $this->errors = [];
    }

    /**
     * @throws \Exception
     */
    public function validate(string $data): bool
    {
        try {
            $decodedData = \json_decode($data, false);
            if (\json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            $this->validator->coerce($decodedData, (object)['$ref' => 'file://' . realpath($this->jsonValidatorPath)]);

            if (!$this->validator->isValid()) {
//                Manage or Log errors as needed... Example:
                foreach ($this->validator->getErrors() as $error) {
                   //\printf("[%s] %s\n", $error['property'], $error['message']);
                    $this->errors[$error['property']] = $error['message'];
                }
                return false;
            }

            $this->dataObject = (array) $decodedData;

            return true;
        } catch (\Exception $e) {
            throw new \Exception(\sprintf("Failed to process validation: %s", $e->getMessage()));
        }
    }

    /**
     * @return array
     */
    public function getDataObject(): array
    {
        return $this->dataObject;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}