<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MagentoCloud\Config\Validator\Variable;

use Magento\MagentoCloud\Config\VariableSchema;
use Magento\MagentoCloud\Config\Validator\ResultFactory;
use Magento\MagentoCloud\Config\Validator\ResultInterface;
use Magento\MagentoCloud\Config\VariableValidatorInterface;

/**
 * Validate that a variable is one of the valid values.
 */
class Values implements VariableValidatorInterface
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     */
    public function __construct(ResultFactory $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $name, VariableSchema $schema, $value): ResultInterface
    {
        $values = $schema->getValues();

        if ($values !== [] && !in_array($value, $values, true)) {
            return $this->resultFactory->error(sprintf(
                'The %s variable must be one of the following values: %s',
                $name,
                implode(', ', $values)
            ));
        }

        return $this->resultFactory->success();
    }
}
