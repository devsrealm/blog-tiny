<?php

namespace App\Document\Areabrick;

class Header extends AbstractAreaBrick
{
    public function getName():string
    {
        return 'Header';

    }

    public function getDescription():string
    {
        return 'A component for rendering a Header';
    }
}
