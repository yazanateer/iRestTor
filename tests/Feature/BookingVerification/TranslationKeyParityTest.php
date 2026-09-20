<?php

namespace Tests\Feature\BookingVerification;

use Tests\TestCase;

class TranslationKeyParityTest extends TestCase
{
    private const NEW_KEYS = [
        'chooseChannel',
        'channelSms',
        'channelWhatsapp',
        'verifyDescriptionSms',
        'verifyDescriptionWhatsapp',
        'otpDeliveryFailed',
        'whatsappNotAvailable',
        'whatsappNumberNotFound',
        'invalidChannel',
    ];

    /**
     * Feature: otp-delivery-channel, Property 10: Translation key parity across locales.
     * For any new booking.* key introduced by this feature, the key SHALL be present
     * in all three locale files (en, ar, he). Validates: Requirement 11.1.
     */
    public function test_new_booking_keys_are_present_in_all_locales(): void
    {
        $enKeys = $this->extractBookingKeys('en');
        $arKeys = $this->extractBookingKeys('ar');
        $heKeys = $this->extractBookingKeys('he');

        foreach (self::NEW_KEYS as $key) {
            $this->assertContains($key, $enKeys, "Missing booking.{$key} in en.ts");
            $this->assertContains($key, $arKeys, "Missing booking.{$key} in ar.ts");
            $this->assertContains($key, $heKeys, "Missing booking.{$key} in he.ts");
        }
    }

    public function test_booking_key_sets_are_identical_across_locales(): void
    {
        $enKeys = $this->extractBookingKeys('en');
        $arKeys = $this->extractBookingKeys('ar');
        $heKeys = $this->extractBookingKeys('he');

        sort($enKeys);
        sort($arKeys);
        sort($heKeys);

        $this->assertSame($enKeys, $arKeys, 'booking.* keys differ between en.ts and ar.ts');
        $this->assertSame($enKeys, $heKeys, 'booking.* keys differ between en.ts and he.ts');
    }

    /**
     * @return string[]
     */
    private function extractBookingKeys(string $locale): array
    {
        $path = base_path("resources/js/i18n/locales/{$locale}.ts");
        $lines = file($path);

        $keys = [];
        $inBooking = false;

        foreach ($lines as $line) {
            if (! $inBooking && preg_match('/^\s{4}booking:\s*\{/', $line)) {
                $inBooking = true;
                continue;
            }

            if ($inBooking) {
                if (preg_match('/^\s{4}\},/', $line)) {
                    break;
                }

                if (preg_match('/^\s{8}([A-Za-z0-9_]+):/', $line, $matches)) {
                    $keys[] = $matches[1];
                }
            }
        }

        return $keys;
    }
}
