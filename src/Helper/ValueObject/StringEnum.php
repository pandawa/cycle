<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Helper\ValueObject;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait StringEnum
{
    public static function typecast(string $value): static
    {
        return static::from($value);
    }

    public function rawValue(): string
    {
        return (string) $this->value;
    }

    public function rawType(): int
    {
        return \PDO::PARAM_STR;
    }
}
