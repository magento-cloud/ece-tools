<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MagentoCloud\Config;

/**
 * Represents the properties for a single variable.
 */
class VariableSchema
{
    const SCHEMA_DEFAULT            = 'default';
    const SCHEMA_DESCRIPTION        = 'description';
    const SCHEMA_EXAMPLES           = 'examples';
    const SCHEMA_MAGENTO_CONSTRAINT = 'magento_version';
    const SCHEMA_MERGE_ALLOWED      = 'allow_merge';
    const SCHEMA_SECTIONS           = 'sections';
    const SCHEMA_TYPE               = 'type';
    const SCHEMA_VALIDATORS         = 'validators';
    const SCHEMA_VALUES             = 'values';

    const TYPE_ARRAY  = 'array';
    const TYPE_BOOL   = 'boolean';
    const TYPE_INT    = 'integer';
    const TYPE_STRING = 'string';

    /**
     * @var array
     */
    private $schema;

    /**
     * @param array $schema
     */
    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Default value for this variable.
     *
     * @return array|bool|int|string|null
     */
    public function getDefault()
    {
        $value = $this->get(self::SCHEMA_DEFAULT);

        // Convert the default value for arrays to an empty array, if one hasn't been provided
        return ($value === null && $this->getType() === self::TYPE_ARRAY) ? [] : $value;
    }

    /**
     * Get the description for this variable.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return (string) $this->get(self::SCHEMA_DESCRIPTION);
    }

    /**
     * Get the examples for this variable.
     *
     * @return array
     */
    public function getExamples(): array
    {
        return array_map(static function ($example) {
            return $example['example'] ?? $example;
        }, ($this->get(self::SCHEMA_EXAMPLES) ?? []));
    }

    /**
     * Get the examples including any comments for them.
     *
     * @return array
     */
    public function getExamplesWithComment(): array
    {
        return array_map(static function ($example) {
            return isset($example['comment']) ? $example : ['comment' => '', 'example' => $example];
        }, ($this->get(self::SCHEMA_EXAMPLES) ?? []));
    }

    /**
     * Get the Magento version constraint.
     *
     * @return string
     */
    public function getMagentoConstraint(): string
    {
        return (string) $this->get(self::SCHEMA_MAGENTO_CONSTRAINT);
    }

    /**
     * Can this value be merge with a default or generated value?
     *
     * @return bool
     */
    public function getMergeAllowed(): bool
    {
        return $this->get(self::SCHEMA_MERGE_ALLOWED) ?? false;
    }

    /**
     * What sections of .magento.env.yaml is this variable allow in?
     *
     * @return string[]
     */
    public function getSections(): array
    {
        return $this->get(self::SCHEMA_SECTIONS) ?? [];
    }

    /**
     * Get the type for this variable.
     *
     * @return string
     */
    public function getType(): string
    {
        return (string) $this->get(self::SCHEMA_TYPE);
    }

    /**
     * Get the custom validators for this variable.
     *
     * @return string[]
     */
    public function getValidators(): array
    {
        return $this->get(self::SCHEMA_VALIDATORS) ?? [];
    }

    /**
     * Get the possible values for this variable.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->get(self::SCHEMA_VALUES) ?? [];
    }

    /**
     * Get a value from the schema.
     *
     * @return mixed|null
     */
    private function get(string $key)
    {
        return $this->schema[$key] ?? null;
    }
}
