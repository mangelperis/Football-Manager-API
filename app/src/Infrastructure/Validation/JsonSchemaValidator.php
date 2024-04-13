<?php
declare(strict_types=1);


namespace App\Infrastructure\Validation;

use JsonSchema\Validator;

class JsonSchemaValidator
{
    private Validator $validator;
    private string $jsonValidatorPath;
    private array $dataObject;

    public function __construct(string $schemaPath)
    {
        $this->validator = new Validator();
        $this->jsonValidatorPath = $schemaPath;
        $this->dataObject = [];
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
//                foreach ($this->validator->getErrors() as $error) {
//                   \printf("[%s] %s\n", $error['property'], $error['message']);
//                }
                return false;
            }

            $this->dataObject = $decodedData;

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
}