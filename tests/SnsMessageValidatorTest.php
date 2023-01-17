<?php

namespace NZTim\SNS\Tests;

use GuzzleHttp\Psr7\Response;
use NZTim\SimpleHttp\Http;
use NZTim\SimpleHttp\HttpResponse;
use NZTim\SNS\SnsMessageValidator;
use Tests\TestCase;

class SnsMessageValidatorTest extends TestCase
{
    /** @test */
    public function invalid_url()
    {
        $validator = $this->getValidator();
        //
        $data = ['SigningCertURL' => 'http://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem'];
        $result = $validator->validate($data);
        $this->assertFalse($result->success);
        $this->assertTrue(str_contains($result->message, 'Invalid certificate scheme'));
        //
        $data = ['SigningCertURL' => 'https://sns.us-west-2.amazonaws.com/SimpleNotificationService'];
        $result = $validator->validate($data);
        $this->assertFalse($result->success);
        $this->assertTrue(str_contains($result->message, 'Invalid certificate URL filename'));
        //
        $data = ['SigningCertURL' => 'https://sns.us-west-2.attacker.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem'];
        $result = $validator->validate($data);
        $this->assertFalse($result->success);
        $this->assertTrue(str_contains($result->message, 'Invalid hostname'));
    }

    /** @test */
    public function caches_certificate()
    {
        $this->mock(Http::class, function ($mock) {
            // Only one call for two verifications
            $mock->shouldReceive('get')->once()->andReturn(new ResponseMock(new Response()));
        });
        $validator = $this->getValidator();
        $data = $this->notification();
        $result = $validator->validate($data);
        $this->assertTrue($result->success);
        $result = $validator->validate($data);
        $this->assertTrue($result->success);
    }

    /** @test */
    public function checks_signature_correctly()
    {
        $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('get')->once()->andReturn(new ResponseMock(new Response()));
        });
        /** @var $validator SnsMessageValidator */
        $validator = app(SnsMessageValidator::class);
        $data = $this->notification();
        $result = $validator->validate($data);
        $this->assertTrue($result->success);
        $data['Message'] .= 'a change';
        $result = $validator->validate($data);
        $this->assertFalse($result->success);
    }

    /** @test */
    public function try_openssl_functions()
    {
        // Signing the notification in a way that works with SnsMessageValidator
        // Created the public/private keypair with:
        // $config = ["digest_alg" => "SHA1", "private_key_type" => OPENSSL_KEYTYPE_RSA, "encrypt_key" => false,];
        // $r = openssl_pkey_new($config);
        // Then export the public and private keys
        // Fortunately, openssl_get_publickey() works with both PEM public keys and certificates
        $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('get')->once()->andReturn(new ResponseMock(new Response()));
        });
        $data = $this->notification();
        $validator = $this->getValidator();
        $stringToSign = $validator->stringToSign($data);
        $signature = '';
        $this->assertTrue(openssl_sign($stringToSign, $signature, $this->privateKey(), OPENSSL_ALGO_SHA1));
        $data['Signature'] = base64_encode($signature);
        $result = $validator->validate($data);
        $this->assertTrue($result->success);
    }

    protected function getValidator(): SnsMessageValidator
    {
        return app(SnsMessageValidator::class);
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

    private function privateKey(): string
    {
        return <<<EOF
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC0UlrueU6VcUjn
wcR8FeGJtLhHAW7hK3QQ6dYOhjE7mdnMM9e5aAB1kJsrV/MOyo1H8ooqzpe5d0Yg
vysR4At50DztF1cdzN4+ucGG+O9EPfMH9MnkGDAPzwoXuIvTJ5fuv1c5Ekfqjihv
4ma2m9sfgaJ+e8uxOkeJLZlb14ycN4/h5IHyqQUqFdErHbMSkgaN2sNhMR5BFMnu
WxGi/LuP70EFoHWb43k3TF/qXG7Gf01GE+Ks54FmYkRBJMwMnUeXy+mwJgOPIZr9
iAUMPWFQvswRcnX6xyluH2nqPTDD2M1lgRQFaiYH9R+q5pB5gm/vyf+aTZLjZeT4
yjy3zXnDAgMBAAECggEBAK4hhUwa+scXoZakKJDHNIgda4KuIJvysV+P4DUgYzSy
Cn/GZIDXrPHa1e1SPhY/hZU/4ysZW60vQBppYTceyxY0AFHYIzlU2B1sljU3+R5G
UIWTXs8UiH0Lqyxi+yvKZU6Zmao0PByGcQgycovPEBhpwDgyZe5cYC5ZSWpGSbOj
2KVVk1TzSJGTg1do+WytBZhCW0iGkIJ1E+O72bCEtsWbr42A+1CRtN8iHIJ1+uYZ
GG7sCqRkP9bQJWx2pm2gChsNRn2+8JpcxQtoGJhBQXEfRceqshYEAWvP1Sscfj1q
1Xj50Gwuz98mQp+Up5WBSfM0T2k1HEYC1MBSF90f1kkCgYEA4c7PzBEgJ43V5eSd
452Lcw+lmSdvsU1dutrr1TKEGLgrZ6oQ7q+tqcC8CX9V7zCgyUb8t8PPq2Gat7Bx
M1V63/cODy7kWpzKA12czgqCbEDGwiU2k3A4ofqMyuDgtseiyvbmqRUld9JfWmPT
ycvqFs8HxjyJFvVOHYcaAVUq4vUCgYEAzG6Ydaa9vZs64RUG3yhOINF94HfnkP1J
kNBkheZ32q8DYVPjmKqJ2llbyA/U2xvWdKhRYcSXRElwwuJKSrSHCPLhEEm6Z1nE
1vlGEjDy7hhiQBtO92xLlee73FoU9VX0WK6Cj/eSndXF6nf1+47/rnkId9s8N1Ap
N+NgJaRmptcCgYEAtAxTZEPDf8Z4Z3aC7psQNg6j3Bq11In70qH8qWI+MfenpGDW
X3t03YXwaSI/QxljGxqfJ2fajqyk0RK/ME9YSnyTmUeqjRW3fjeGcEOw+uArm2JQ
wf8ZKQ6+dIYap6NHCs8T3H4gAgqcPVab6KvPW6B4cniSVtwCDUlMUwO1zoECgYBB
bzDrj5hyBSq+es19Rlhjlsp0u8xqEzil2p4iYdeBLr2lPIXACu4e7rU6/x5Bl9J3
+pw58AP+/obSSj4/VTNXwO4bGY5JoGkp6hXsorBPV4yzOkp3VbmH3Om7qTXGJWIV
tJ3j2Pmb/Z3g487fWzEptmHog0rt5YGb+vJ+efXFdwKBgGeBehnnWFRag7E5x+R9
RpcZNp4vRen6RUB5GGGxBz4bEMQAaNcDWXievt4BITCsJMzzJISeKBvA0Esu+oeO
HyOntyD3EQ3j0y4id6cYJUgO6H90S9UkwuXtgmzFN6AkAlzsNWKj1HGJvHpES5+4
a7CjqoP6PhziEyasmX5gh2IZ
-----END PRIVATE KEY-----
EOF;

    }
}

class ResponseMock extends HttpResponse
{
    public function body(): string
    {
        return <<<EOF
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtFJa7nlOlXFI58HEfBXh
ibS4RwFu4St0EOnWDoYxO5nZzDPXuWgAdZCbK1fzDsqNR/KKKs6XuXdGIL8rEeAL
edA87RdXHczePrnBhvjvRD3zB/TJ5BgwD88KF7iL0yeX7r9XORJH6o4ob+Jmtpvb
H4GifnvLsTpHiS2ZW9eMnDeP4eSB8qkFKhXRKx2zEpIGjdrDYTEeQRTJ7lsRovy7
j+9BBaB1m+N5N0xf6lxuxn9NRhPirOeBZmJEQSTMDJ1Hl8vpsCYDjyGa/YgFDD1h
UL7MEXJ1+scpbh9p6j0ww9jNZYEUBWomB/UfquaQeYJv78n/mk2S42Xk+Mo8t815
wwIDAQAB
-----END PUBLIC KEY-----
EOF;
    }
}
