<?php
declare(strict_types=1);

namespace App\Controller\Request;

use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;

class ArticleApiRequestParser
{
    private const MAX_TITLE_LENGTH = 200;
    private const MAX_TAG_LENGTH = 200;

    public static function parseCreateArticleParams(array $parameters): CreateArticleParams
    {
        return new CreateArticleParams(
            self::parseInteger($parameters, 'user_id'),
            self::parseString($parameters, 'title', maxLength: self::MAX_TITLE_LENGTH),
            self::parseStringArray($parameters, 'tags', maxLength: self::MAX_TAG_LENGTH)
        );
    }

    public static function parseEditArticleParams(array $parameters): EditArticleParams
    {
        return new EditArticleParams(
            self::parseInteger($parameters, 'id'),
            self::parseInteger($parameters, 'user_id'),
            self::parseString($parameters, 'title', maxLength: self::MAX_TITLE_LENGTH),
            self::parseString($parameters, 'content'),
            self::parseStringArray($parameters, 'tags', maxLength: self::MAX_TAG_LENGTH)
        );
    }

    public static function parseInteger(array $parameters, string $name): int
    {
        $value = $parameters[$name] ?? null;
        if (!self::isIntegerValue($value))
        {
            throw new RequestValidationException([$name => 'Invalid integer value']);
        }
        return (int)$value;
    }

    public static function parseString(array $parameters, string $name, ?int $maxLength = null): string
    {
        $value = $parameters[$name] ?? null;
        if (!is_string($value))
        {
            throw new RequestValidationException([$name => 'Invalid string value']);
        }
        if ($maxLength !== null && mb_strlen($value) > $maxLength)
        {
            throw new RequestValidationException([$name => "String value too long (exceeds $maxLength characters)"]);
        }
        return $value;
    }

    public static function parseIntegerArray(array $parameters, string $name): array
    {
        $values = self::parseArray($parameters, $name);
        foreach ($values as $index => $value)
        {
            if (!self::isIntegerValue($value))
            {
                throw new RequestValidationException([$name => "Invalid non-integer value at index $index"]);
            }
        }
        return $values;
    }

    public static function parseStringArray(array $parameters, string $name, ?int $maxLength = null): array
    {
        $values = self::parseArray($parameters, $name);
        foreach ($values as $index => $value)
        {
            if (!is_string($value))
            {
                throw new RequestValidationException([$name => "Invalid string value at index $index"]);
            }
            if ($maxLength !== null && mb_strlen($value) > $maxLength)
            {
                throw new RequestValidationException([$name => "String value too long (exceeds $maxLength characters) at index $index"]);
            }
        }
        return $values;
    }

    public static function parseArray(array $parameters, string $name): array
    {
        $values = $parameters[$name] ?? null;
        if (!is_array($values))
        {
            throw new RequestValidationException([$name => 'Not an array']);
        }
        return $values;
    }

    private static function isIntegerValue(mixed $value): bool
    {
        return is_numeric($value) && (is_int($value) || ctype_digit($value));
    }
}
