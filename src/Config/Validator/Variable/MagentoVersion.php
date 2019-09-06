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
use Magento\MagentoCloud\Package\MagentoVersion as MagentoVersionManager;
use Magento\MagentoCloud\Package\UndefinedPackageException;

/**
 * Validate that a variable is supported by the current version of Magento.
 */
class MagentoVersion implements VariableValidatorInterface
{
    /**
     * @var MagentoVersionManager
     */
    private $magentoVersion;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param MagentoVersionManager $magentoVersion
     */
    public function __construct(ResultFactory $resultFactory, MagentoVersionManager $magentoVersion)
    {
        $this->resultFactory = $resultFactory;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $name, VariableSchema $spec, $value): ResultInterface
    {
        try {
            if (!$this->magentoVersion->satisfies($spec->getMagentoConstraint())) {
                return $this->resultFactory
                    ->error("The {$name} variable is not compatible with the current version of Magento.");
            }
        } catch (UndefinedPackageException $e) {
            return $this->resultFactory->success();
        }

        return $this->resultFactory->success();
    }
}
