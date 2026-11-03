<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * View Composer for the homepage.
 *
 * View Composers are the Laravel pattern that keeps Blade templates clean.
 * Instead of querying inside the .blade.php file, you bind data here.
 *
 * Any variable `with()` returns is automatically available in the bound views.
 *
 * @see https://roots.io/sage/docs/blade-and-composers/
 */
class NextMatch extends Composer
{
    /**
     * List of views served by this composer.
     */
    protected static $views = [
        'front-page',
    ];

    /**
     * Data to expose to the view.
     */
    public function with(): array
    {
        $next     = skduo_get_next_match();
        $upcoming = skduo_get_upcoming_matches(4);

        return [
            'nextMatch'        => $next ? skduo_match_data($next) : null,
            'upcomingMatches'  => array_map('skduo_match_data', $upcoming),
        ];
    }
}
