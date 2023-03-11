<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\PostCreationNotification;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostCreationNotifications
{
    private UserRepositoryInterface $userRepository;

    /**
     * Create the event listener.
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        foreach ($this->userRepository->getAllUsers() as $user) {
            // Send mail
            $user->notify(new PostCreationNotification($event->post));

            // Send push
            $author = $event->post->user->name;
            OneSignalFacade::sendNotificationToUser(
                "The new post was published by {$author}.",
                $user->onesignal_id
            );
        }
    }
}
