<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Diffusé en temps réel à toute la communauté lorsqu'une publication paraît.
 * Implémente ShouldBroadcastNow → diffusion synchrone, sans worker de file.
 */
class CommunityPostCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $postId,
        public string $theme,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('communaute');
    }

    public function broadcastAs(): string
    {
        return 'post.created';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->postId, 'theme' => $this->theme];
    }
}
