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
 * Validate that RESOURCE_CONFIGURATION has a valid value.
 */
class ResourceConfiguration implements VariableValidatorInterface
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
        $wrongResources = [];

        foreach ($value as $resourceName => $resourceData) {
            if (!isset($resourceData['connection'])) {
                $wrongResources[] = $resourceName;
            }
        }

        if ($wrongResources) {
            return $this->resultFactory->error(sprintf(
                'The %s variable is not configured properly. Add connection information to the following resources: %s',
                $name,
                implode(', ', $wrongResources)
            ));
        }

        return $this->resultFactory->success();
    }
}
