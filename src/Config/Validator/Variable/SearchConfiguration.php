<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MagentoCloud\Config\Validator\Variable;

use Magento\MagentoCloud\Config\StageConfigInterface;
use Magento\MagentoCloud\Config\VariableSchema;
use Magento\MagentoCloud\Config\Validator\ResultFactory;
use Magento\MagentoCloud\Config\Validator\ResultInterface;
use Magento\MagentoCloud\Config\VariableValidatorInterface;

/**
 * Validate that SEARCH_CONFIGURATION has a valid value.
 */
class SearchConfiguration implements VariableValidatorInterface
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
        if (isset($value[StageConfigInterface::OPTION_MERGE]) && $value[StageConfigInterface::OPTION_MERGE]) {
            return $this->resultFactory->success();
        }

        return isset($value['engine'])
            ? $this->resultFactory->success()
            : $this->resultFactory->error(sprintf('The %s variable must have an engine configured.', $name));
    }
}
