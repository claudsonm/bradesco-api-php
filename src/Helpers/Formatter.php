<?php
namespace BradescoApi\Helpers;

class Formatter
{
    // remove non numeric chars
    public static function cleanNumeric($value)
    {
        return preg_replace("/[^0-9]/", '', (string) $value);
    }

    // expected format: no decimal and thousand separators
    public static function formatCurrency($value)
    {
        if (is_float($value) || is_int($value)) {
            $value = number_format($value, 2, "", "");
        } else {
            $value = static::cleanNumeric($value);
        }

        return $value;
    }

    // expected format: ddmmyyyy
    public static function formatDate($value)
    {
        // remove the time part
        if (is_string($value)) {
            $value = substr($value, 0, 10);
        }

        if (is_string($value) && preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
            $value = \DateTime::createFromFormat('d/m/Y', $value);
        }

        if (is_string($value) && preg_match('/\d{4}\-\d{2}\-\d{2}/', $value)) {
            $value = \DateTime::createFromFormat('Y-m-d', $value);
        }

        if (is_object($value) && method_exists($value, 'format')) {
            $value = $value->format('d.m.Y');
        }

        return $value;
    }

    // expected format: no dots and slashes, with 14 leading zeros
    public static function formatCpfCnpj($value)
    {
        $value = static::cleanNumeric($value);

        $value = str_pad($value, 14, '0', STR_PAD_LEFT);

        return $value;
    }

    // expected format: no special chars, API has trouble processing them
    public static function formatText($value)
    {
        $rules = [
            ['[ãáàâ]', 'a'],
            ['[ÃÁÀÂ]', 'A'],
            ['[ẽéèê]', 'e'],
            ['[ẼÉÈÊ]', 'E'],
            ['[ĩíìî]', 'i'],
            ['[ĨÍÌÎ]', 'I'],
            ['[õóòô]', 'o'],
            ['[ÕÓÒÔ]', 'O'],
            ['[ũúùû]', 'u'],
            ['[ŨÚÙÛ]', 'U'],
            ['[ç]', 'c'],
            ['[Ç]', 'C']
        ];

        foreach ($rules as $rule) {
            $value = preg_replace('/'. $rule[0] . '/u', $rule[1], $value);
        }

        return $value;
    }
}

?>
