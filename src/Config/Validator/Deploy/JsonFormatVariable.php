<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Config\Validator\Deploy;

use Magento\MagentoCloud\Config\Schema;
use Magento\MagentoCloud\Config\Stage\Deploy\MergedConfig;
use Magento\MagentoCloud\Config\StageConfigInterface;
use Magento\MagentoCloud\Config\Validator;
use Magento\MagentoCloud\Config\ValidatorInterface;
use Magento\MagentoCloud\Config\VariableSchema;

/**
 * Checks that array-type variables given as json string can be decoded into array.
 */
class JsonFormatVariable implements ValidatorInterface
{
    /**
     * @var MergedConfig
     */
    private $mergedConfig;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var Validator\ResultFactory
     */
    private $resultFactory;

    /**
     * @param Validator\ResultFactory $resultFactory
     * @param MergedConfig $mergedConfig
     * @param Schema $schema
     */
    public function __construct(
        Validator\ResultFactory $resultFactory,
        MergedConfig $mergedConfig,
        Schema $schema
    ) {
        $this->resultFactory = $resultFactory;
        $this->mergedConfig = $mergedConfig;
        $this->schema = $schema;
    }

    /**
     * Checks that array-type variables given as json string can be decoded into array.
     *
     * {@inheritdoc}
     */
    public function validate(): Validator\ResultInterface
    {
        try {
            $errors = [];

            foreach ($this->mergedConfig->get() as $optionName => $optionValue) {
                if (!is_string($optionValue) ||
                    $this->schema->get($optionName)->getType() !== VariableSchema::TYPE_ARRAY
                ) {
                    continue;
                }

                json_decode($optionValue, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = sprintf('%s (%s)', $optionName, json_last_error_msg());
                }
            }

            if ($errors) {
                return $this->resultFactory->error('Next variables can\'t be decoded: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            return $this->resultFactory->error('Can\'t read merged configuration: ' . $e->getMessage());
        }

        return $this->resultFactory->success();
    }
}
