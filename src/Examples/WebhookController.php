<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NZTim\Queue\QueueManager;
use NZTim\SNS\SnsMessageReceived;

class WebhookController
{
    private QueueManager $qm;

    public function __construct(QueueManager $qm)
    {
        $this->qm = $qm;
    }

    public function handle(Request $request)
    {
        $this->qm->add(SnsMessageReceived::fromLaravelRequest($request));
        return response('Thanks!', 200)->header('Content-Type', 'text/plain');
    }
}
