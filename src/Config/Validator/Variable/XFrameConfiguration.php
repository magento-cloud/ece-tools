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
 * Validate that X_FRAME_CONFIGURATION has a valid value.
 */
class XFrameConfiguration implements VariableValidatorInterface
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
        $value = strtolower($value);

        return ($value === 'deny' || $value === 'sameorigin' || preg_match('/^allow-from .+/', $value))
            ? $this->resultFactory->success()
            : $this->resultFactory->error(sprintf(
                'The %s variable must be either "deny", "sameorigin", or "allow-from [uri]"',
                $name
            ));
    }
}
