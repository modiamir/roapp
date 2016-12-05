<?php

namespace AppBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class EnumType
 * @package AppBundle\DBAL
 * @codingStandardsIgnoreStart
 */
abstract class EnumType extends Type
{
    // @codingStandardsIgnoreEnd
    protected $name;
    protected $values = [];

    /**
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(
            ', ',
            array_map(
                function ($value) {
                    return "'{$value}'";
                },
                $this->values
            )
        );

        return sprintf('SMALLINT CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->values)) {
            throw new \InvalidArgumentException("Invalid '".$this->name."' value.");
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
