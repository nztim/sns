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
        $decoded = json_decode(request()->getContent(), true);
        if (!is_array($decoded)) {
            log_info('sns', 'Unable to decode message: ' . $decoded);
            return response()->json(['result' => 'Unable to process'], 400);
        }
        $this->qm->add(SnsMessageReceived::fromArray($decoded));
        return response()->json(['result' => 'OK']);
    }
}
