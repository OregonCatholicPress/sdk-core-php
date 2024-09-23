<?php

namespace PayPal\Formatter;

use InvalidArgumentException;

class FormatterFactory
{
    public static function factory($bindingType)
    {
        return match ($bindingType) {
            'NV' => new PPNVPFormatter(),
            'SOAP' => new PPSOAPFormatter(),
            default => throw new InvalidArgumentException("Invalid value for bindingType. You passed $bindingType"),
        };
    }
}
