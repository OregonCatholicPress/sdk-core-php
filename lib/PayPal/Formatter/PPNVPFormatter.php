<?php

namespace PayPal\Formatter;

use BadMethodCallException;
use Override;

class PPNVPFormatter implements IPPFormatter
{
    #[Override]
    public function toString($request, $options = [])
    {
        return $request->getRequestObject()->toNVPString();
    }

    #[Override]
    public function toObject($string, $options = []): never
    {
        throw new BadMethodCallException("Unimplemented");
    }
}
