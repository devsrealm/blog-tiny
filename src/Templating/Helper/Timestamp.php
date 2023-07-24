<?php

namespace App\Templating\Helper;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Timestamp extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('timestamp', [$this, 'timestamp']),
        ];
    }

    public function timestamp(String $format): void
    {
        echo date($format);
    }
}
