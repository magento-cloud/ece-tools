<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MagentoCloud\Config;

/**
 * Interface for variable validators.
 */
interface VariableValidatorInterface
{
    /**
     * Run the validator.
     *
     * @param string $name
     * @param VariableSchema $schema
     * @param string|array|int|bool|null $value
     */
    public function validate(string $name, VariableSchema $schema, $value): Validator\ResultInterface;
}
