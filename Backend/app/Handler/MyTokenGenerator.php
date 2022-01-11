<?php

namespace App\Handler;
use Stichoza\GoogleTranslate\Tokens\TokenProviderInterface;

class MyTokenGenerator implements TokenProviderInterface
{
    public function generateToken(string $source, string $target, string $text) : string
    {
        return 'AIzaSyDudGX_xwGVuLWCjxfY1-RYDgaYfhKyu7k';
    }
}