<?php declare(strict_types=1);

namespace NZTim\SNS;

use NZTim\Queue\QueueManager;

class WebhookController
{
    private QueueManager $qm;

    public function __construct(QueueManager $qm)
    {
        $this->qm = $qm;
    }

    public function handle()
    {
        $body = request()->getContent();
        $smr = SnsMessageReceived::fromString($body);
        if (!$smr instanceof SnsMessageReceived) {
            log_info('sns', 'Unable to decode message: ' . $body);
            return response()->json(['result' => 'Unable to process'], 400);
        }
        $this->qm->add($smr);
        return response()->json(['result' => 'OK']);
    }
}
