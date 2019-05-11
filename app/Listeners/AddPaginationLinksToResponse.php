<?php

namespace App\Listeners;

use Dingo\Api\Event\ResponseWasMorphed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddPaginationLinksToResponse
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ResponseWasMorphed $event)
    {
        if (isset($event->content['meta']['pagination'])) {
            $links = $event->content['meta']['pagination']['links'];
            $next = isset($links['next']) ? $links['next'] : null;
            $previous = isset($links['previous']) ? $links['previous'] : null;
            $event->response->headers->set(
                'link',
                sprintf('<%s>; rel="next", <%s>; rel="prev"', $next, $previous)
            );
        }
    }
}
