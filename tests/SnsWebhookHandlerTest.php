<?php declare(strict_types=1);

namespace NZTim\SNS\Tests;

use Illuminate\Events\Dispatcher;
use NZTim\SNS\SnsMessageValidator;
use NZTim\SNS\SnsWebhook;
use NZTim\SNS\SnsWebhookHandler;
use Tests\TestCase;

class SnsWebhookHandlerTest extends TestCase
{
    /** @test */
    public function dispatches_event()
    {
        $webhook = new SnsWebhook(json_encode($this->notification()));
        $this->mock(SnsMessageValidator::class)->shouldReceive('validate')->andReturn(\NZTim\SNS\Result::createSuccess());
        $this->mock(Dispatcher::class)->shouldReceive('dispatch')->once();
        $handler = $this->getHandler();
        $handler->handle($webhook);
        $this->assertTrue(true); // Test is shouldReceive('dispatch')
    }

    private function getHandler(): SnsWebhookHandler
    {
        return app(SnsWebhookHandler::class);
    }

    private function notification(): array
    {
        return [
            "Type"             => "Notification",
            "MessageId"        => "11111111-1111-1111-1111-111111111111",
            "TopicArn"         => "arn:aws:sns:us-east-1:733069810212:ses_messages",
            "Message"          => "Hello World",
            "Timestamp"        => "2020-05-17T22:39:41.363Z",
            "SignatureVersion" => "1",
            "Signature"        => "f9NkV8ihb9EtW9fmTKg/VV6WBLxijcv41pEcx3aufi68JbhJA8H3W0SKryS21eJtjISJVJO2SBMYkSFwYeMUE+/hDyrr7/xRxohHcB/Lfy9hORW/MHUjH/6RW8DKAyPwtKsWrecRHR0YjE/cWovHE/5iLF+wpmvYm19OGsUDP8rUeBT1XW7+FVokyxxyiczVXM+UfuZc4axzVJuAlNWA2No5Ro3APCM8LmlrbHMSLbdiMg195VTtXHC3fPST7jRinZARP8S7WdSSd9QntPbT7nyqKZE2ud6cxYPz6OdY8FNF+2ivMoVCmrDbkgTnaACF63T6rfCVXtTFYnU9csg5BQ==",
            "SigningCertURL"   => "https://sns.us-east-1.amazonaws.com/SimpleNotificationService-blahblahblah.pem",
            "UnsubscribeURL"   => "https://sns.us-east-1.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-east-1:111111111111:ses_messages:11111111-1111-1111-1111-111111111111",
        ];
    }
}
