<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Config;

use Magento\MagentoCloud\Config\Stage\DeployInterface;
use Magento\MagentoCloud\Config\Stage\PostDeployInterface;
use Magento\MagentoCloud\Filesystem\SystemList;
use Magento\MagentoCloud\Filesystem\Driver\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Configuration schema for .magento.env.yaml file
 */
class Schema
{
    const SCHEMA_TYPE = 'type';
    const SCHEMA_VALUE_VALIDATION = 'value_validation';
    const SCHEMA_STAGE = 'stage';
    const SCHEMA_SYSTEM = 'system';
    const SCHEMA_DEFAULT_VALUE = 'default_values';
    const SCHEMA_REPLACEMENT = 'replacement';

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var VariableSchema[]
     */
    private $variables;

    /**
     * @var array
     */
    private $schemaYaml;

    /**
     * @var File
     */
    private $file;

    /**
     * @var SystemList
     */
    private $roots;

    public function __construct(SystemList $roots, File $file)
    {
        $this->file = $file;
        $this->roots = $roots;
    }

    /**
     * Returns default values.
     *
     * @var string $stage Filter the defaults to just those for a given stage
     *
     * @return array
     */
    public function getDefaults(string $stage = ''): array
    {
        if (!isset($this->defaults)) {
            $this->loadSchema();

            foreach (array_keys($this->schemaYaml) as $itemName) {
                $schema = $this->get($itemName);

                $this->defaults[$itemName] = $schema->getDefault();
            }
        }

        if ($stage) {
            return array_filter($this->defaults, function (string $key) use ($stage): bool {
                return in_array($stage, $this->get($key)->getSections(), true);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $this->defaults;
    }

    /**
     * Get the schema for a particular variable.
     *
     * @param string $key
     * @return VariableSchema
     */
    public function get(string $key): VariableSchema
    {
        if (!isset($this->variables[$key])) {
            $this->loadSchema();

            if (!isset($this->schemaYaml[$key])) {
                throw new \RuntimeException('No schema defined for ' . $key);
            }

            $this->variables[$key] = new VariableSchema($this->schemaYaml[$key]);
        }

        return $this->variables[$key];
    }

    /**
     * Returns array of deprecated variables.
     *
     * @return array
     */
    public function getDeprecatedSchema()
    {
        return [
            StageConfigInterface::VAR_SCD_EXCLUDE_THEMES => [
                self::SCHEMA_REPLACEMENT => StageConfigInterface::VAR_SCD_MATRIX,
            ],
        ];
    }

    /**
     * Read schema.yml file into memory.
     */
    private function loadSchema(): void
    {
        if (isset($this->schemaYaml)) {
            return;
        }

        $this->schemaYaml = Yaml::parse($this->file->fileGetContents($this->roots->getRoot() . '/schema.yml'));
    }
}
