<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class MessageType
 * @package App\Enums
 */
final class MessageType extends Enum
{
    const Text = 'text';
    const File = 'file';
    const Image = 'image';

    /**
     * @return array
     */
    public static function list(): array
    {
        return [
            self::Text,
            self::File,
            self::Image,
        ];
    }
}
