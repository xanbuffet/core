<?php

namespace App\Traits;

trait TelegramHelperTrait
{
    public function escapeMarkdownV2(string $text): string
    {
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!', '\\'];
        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\'.$char, $text);
        }

        return $text;
    }
}
