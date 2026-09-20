<?php

namespace Tests\Feature\BookingVerification;

use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class WhatsappServiceOtpTest extends TestCase
{
    public function test_send_otp_code_posts_content_template_to_twilio(): void
    {
        config([
            'services.twilio.sid' => 'AC_TEST_SID',
            'services.twilio.token' => 'test-token',
            'services.twilio.whatsapp_from' => 'whatsapp:+14155238886',
            'services.twilio.otp_template_sid' => 'HX_TEST_TEMPLATE',
        ]);

        Http::fake([
            'api.twilio.com/*' => Http::response(['sid' => 'SM_TEST'], 201),
        ]);

        app(WhatsappService::class)->sendOtpCode('+972501234567', '123456');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.twilio.com/2010-04-01/Accounts/AC_TEST_SID/Messages.json'
                && $request['To'] === 'whatsapp:+972501234567'
                && $request['ContentSid'] === 'HX_TEST_TEMPLATE'
                && $request['ContentVariables'] === json_encode(['1' => '123456'])
                && ! isset($request['Body']);
        });
    }

    public function test_send_otp_code_throws_on_non_2xx_twilio_response(): void
    {
        config([
            'services.twilio.sid' => 'AC_TEST_SID',
            'services.twilio.token' => 'test-token',
            'services.twilio.whatsapp_from' => 'whatsapp:+14155238886',
            'services.twilio.otp_template_sid' => 'HX_TEST_TEMPLATE',
        ]);

        Http::fake([
            'api.twilio.com/*' => Http::response(['code' => 63016, 'message' => 'Failed'], 400),
        ]);

        $this->expectException(RuntimeException::class);

        app(WhatsappService::class)->sendOtpCode('+972501234567', '123456');
    }
}
