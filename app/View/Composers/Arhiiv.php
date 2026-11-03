<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Arhiiv extends Composer
{
    protected static $views = [
        'page-arhiiv',
    ];

    public function with(): array
    {
        return [
            'arhiivSeasons' => skduo_get_arhiiv_data(),
        ];
    }
}
