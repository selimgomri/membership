<?php

declare(strict_types=1);

namespace Brick\Postcode\Formatter;

use Brick\Postcode\CountryPostcodeFormatter;

/**
 * Validates and formats postcodes in French Southern and Antarctic Territories.
 *
 * French codes in the 98400 range have been reserved, but do not seem to be in use at the moment.
 *
 * @see https://en.wikipedia.org/wiki/List_of_postal_codes
 */
class TFFormatter implements CountryPostcodeFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(string $postcode) : ?string
    {
        if (strlen($postcode) !== 5) {
            return null;
        }

        if (! ctype_digit($postcode)) {
            return null;
        }

        if (substr($postcode, 0, 3) !== '984') {
            return null;
        }

        return $postcode;
    }
}