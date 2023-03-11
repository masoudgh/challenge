<?php

namespace App\Listeners;

use App\Events\PostDeleted;
use App\Services\ElasticClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeletePostIndex
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostDeleted $event): void
    {
        ElasticClient::client()
            ->index('post')
            ->delete($event->post->id);
    }
}
