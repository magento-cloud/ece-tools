<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MagentoCloud\Config\Validator\Build;

use Magento\MagentoCloud\Config\Environment\Reader as EnvironmentReader;
use Magento\MagentoCloud\Config\Schema;
use Magento\MagentoCloud\Config\StageConfigInterface;
use Magento\MagentoCloud\Config\SystemConfigInterface;
use Magento\MagentoCloud\Config\Validator;
use Magento\MagentoCloud\Config\ValidatorFactory;
use Magento\MagentoCloud\Config\ValidatorInterface;
use Magento\MagentoCloud\Config\Validator\Result\Error;
use Magento\MagentoCloud\Config\Validator\Variable;

/**
 * Validates 'stage' section of environment configuration.
 */
class StageConfig implements ValidatorInterface
{
    /**
     * @var EnvironmentReader
     */
    private $environmentReader;

    /**
     * @var Validator\ResultFactory
     */
    private $resultFactory;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var ValidatorFactory
     */
    private $validatorFactory;

    /**
     * @param EnvironmentReader $environmentReader
     * @param Validator\ResultFactory $resultFactory
     * @param ValidatorFactory $validatorFactory
     * @param Schema $schema
     */
    public function __construct(
        EnvironmentReader $environmentReader,
        Validator\ResultFactory $resultFactory,
        ValidatorFactory $validatorFactory,
        Schema $schema
    ) {
        $this->environmentReader = $environmentReader;
        $this->resultFactory = $resultFactory;
        $this->validatorFactory = $validatorFactory;
        $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    public function validate(): Validator\ResultInterface
    {
        $config = $this->environmentReader->read()[StageConfigInterface::SECTION_STAGE] ?? [];
        $errors = [];

        foreach ($config as $stage => $stageConfig) {
            if (!is_array($stageConfig)) {
                continue;
            }

            $errors = array_merge($errors, $this->validateSection($stage, $stageConfig));
        }

        $variables = $this->environmentReader
            ->read()[SystemConfigInterface::SYSTEM_VARIABLES][SystemConfigInterface::SYSTEM_VARIABLES] ?? [];

        $errors = array_merge($errors, $this->validateSection(SystemConfigInterface::SYSTEM_VARIABLES, $variables));

        if ($errors) {
            return $this->resultFactory->error(
                'Environment configuration is not valid. Correct the following items in your .magento.env.yaml file:',
                implode(PHP_EOL, $errors)
            );
        }

        return $this->resultFactory->success();
    }

    private function validateSection(string $section, array $config): array
    {
        /** @var Validator\ResultInterface[] */
        $errors = [];

        foreach ($config as $key => $value) {
            try {
                $schema = $this->schema->get($key);
            } catch (\RuntimeException $e) {
                $errors[] = $this->resultFactory
                    ->error(sprintf('No schema is defined for %s. Check the spelling of this variable.', $key));

                continue;
            }

            $errors[] = $this->validatorFactory->createVariableValidator(Variable\Section::class)
                ->validate($key, $schema, $section);
            $errors[] = $this->validatorFactory->createVariableValidator(Variable\Type::class)
                ->validate($key, $schema, $value);
            $errors[] = $this->validatorFactory->createVariableValidator(Variable\Values::class)
                ->validate($key, $schema, $value);
            $errors[] = $this->validatorFactory->createVariableValidator(Variable\MagentoVersion::class)
                ->validate($key, $schema, $value);

            foreach ($schema->getValidators() as $validator) {
                $errors[] = $this->validatorFactory->createVariableValidator($validator)
                    ->validate($key, $schema, $value);
            }
        }

        return array_map(
            function (Error $result) {
                return $result->getError();
            },
            array_filter($errors, function (Validator\ResultInterface $result) {
                return $result instanceof Error;
            })
        );
    }
}
