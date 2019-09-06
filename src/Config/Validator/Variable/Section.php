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
 * Validate that a variable is in the correct section/stage.
 */
class Section implements VariableValidatorInterface
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
    public function validate(string $name, VariableSchema $schema, $section): ResultInterface
    {
        $sections = $schema->getSections();

        if (!in_array($section, $sections, true)) {
            return $this->resultFactory->error(sprintf(
                'The %s variable was defined in the %s section but is only allowed in [%s].',
                $name,
                $section,
                implode(', ', $sections)
            ));
        }

        return $this->resultFactory->success();
    }
}
