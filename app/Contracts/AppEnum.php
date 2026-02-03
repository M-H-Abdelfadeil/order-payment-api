<?php

namespace App\Contracts;

interface AppEnum
{
    public static function values(): array;
    public function label(): string;
}
