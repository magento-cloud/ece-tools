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
 * Validate that CRON_CONSUMERS_RUNNER has a valid value.
 */
class CronConsumersRunner implements VariableValidatorInterface
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
        if (isset($value['cron_run']) && !is_bool($value['cron_run'])) {
            $this->resultFactory->error(sprintf('The value for %s.cron_run must be a boolean', $name));
        }

        if (isset($value['max_messages']) && (!is_int($value['max_messages']) || $value['max_messages'] < 0)) {
            $this->resultFactory->error(sprintf('The value for %s.max_messages must be a positive integer', $name));
        }

        if (isset($value['consumers']) && !is_array($value['consumers'])) {
            $this->resultFactory->error(sprintf('The value for %s.consumers must be an array', $name));
        }

        return $this->resultFactory->success();
    }
}
