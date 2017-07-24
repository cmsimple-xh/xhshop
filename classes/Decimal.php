<?php

namespace Xhshop;

class Decimal
{
    /**
     * @var string
     */
    private $value;

    public function __construct($value)
    {
        assert(!($value instanceof Decimal));
        if (is_string($value) && preg_match('/^-?(?:\d|[1-9]\d+)\.\d{2}$/', $value)) {
            $this->value = $value;
        } else {
            $this->value = number_format($value, 2, '.', '');
        }
    }

    public function __toString()
    {
        return $this->value;
    }
}
