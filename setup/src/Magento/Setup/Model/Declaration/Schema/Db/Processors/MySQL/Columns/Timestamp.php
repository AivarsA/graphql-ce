<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Declaration\Schema\Db\Processors\MySQL\Columns;

use Magento\Setup\Model\Declaration\Schema\Db\Processors\DbSchemaProcessorInterface;
use Magento\Setup\Model\Declaration\Schema\Dto\Columns\Date;
use Magento\Setup\Model\Declaration\Schema\Dto\ElementInterface;

/**
 * Process timestamp and find out it on_update and default values
 *
 * @inheritdoc
 */
class Timestamp implements DbSchemaProcessorInterface
{
    /**
     * This timestamp can be used, when const value as DEFAULT 0 was passed
     */
    const CONST_DEFAULT_TIMESTAMP = '0000-00-00 00:00:00';

    /**
     * @var OnUpdate
     */
    private $onUpdate;

    /**
     * @var DefaultDefinition
     */
    private $defaultDefinition;

    /**
     * @param OnUpdate          $onUpdate
     * @param DefaultDefinition $defaultDefinition
     */
    public function __construct(OnUpdate $onUpdate, DefaultDefinition $defaultDefinition)
    {
        $this->onUpdate = $onUpdate;
        $this->defaultDefinition = $defaultDefinition;
    }

    /**
     * @inheritdoc
     */
    public function canBeApplied(ElementInterface $element)
    {
        return $element instanceof \Magento\Setup\Model\Declaration\Schema\Dto\Columns\Timestamp;
    }

    /**
     * @param \Magento\Setup\Model\Declaration\Schema\Dto\Columns\Timestamp $element
     * @inheritdoc
     */
    public function toDefinition(ElementInterface $element)
    {
        return sprintf(
            '%s %s %s',
            $element->getType(),
            $this->defaultDefinition->toDefinition($element),
            $this->onUpdate->toDefinition($element)
        );
    }

    /**
     * @inheritdoc
     */
    public function fromDefinition(array $data)
    {
        $matches = [];
        if (preg_match('/^(timestamp|datetime).*/', $data['type'], $matches)) {
            $data['type'] = $matches[1];
            if ($data['default'] === self::CONST_DEFAULT_TIMESTAMP) {
                $data['default'] = 0;
            }
            $data = $this->onUpdate->fromDefinition($data);
        }

        return $data;
    }
}
