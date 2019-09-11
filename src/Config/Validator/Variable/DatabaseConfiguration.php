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
 * Validate that DATABASE_CONFIGURATION has a valid value.
 */
class DatabaseConfiguration implements VariableValidatorInterface
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

        if (!isset(
            $value['connection']['default']['host'],
            $value['connection']['default']['dbname'],
            $value['connection']['default']['username'],
            $value['connection']['default']['password']
        )) {
            return $this->resultFactory->error(sprintf(
                'The %s variable is not configured properly. The default connection requires '
                    . 'a host, dbname, username, and password.',
                $name
            ));
        }

        return $this->resultFactory->success();
    }
}
