<?php

namespace App\Helpers;

class OTPMessageHelper
{
    public static function generateDeliveryOTPMessage(string $otp, string $lang = 'en'): string
    {
        if ($lang === 'ar') {
            return "مرحبًا! 👋\n\n"
                . "شحنتك على وشك الوصول.\n\n"
                . "رمز التحقق من التسليم الخاص بك هو:  *{$otp}*\n\n"
                . "يرجى مشاركة هذا الرمز مع وكيل التوصيل عند استلام الطرد الخاص بك.\n\n"
                . "⏳ سينتهي صلاحية هذا الرمز خلال 10 دقائق.\n\n"
                . "شكرًا لاختياركم لنا! 📦";
        }
        return "Hi! 👋\n\n"
            . "Your shipment is almost there.\n\n"
            . "Your delivery verification code is: *{$otp}*\n\n"
            . "Please share this code with the delivery agent when you receive your package.\n\n"
            . "⏳ This code will expire in 10 minutes.\n\n"
            . "Thank you for choosing us! 📦";
    }

    public static function generateSignupOTPMessage(string $otp, string $lang = 'en'): string
    {
        if ($lang === 'ar') {
            return "مرحبًا! 👋\n\n"
                . "رمز التحقق من التسجيل الخاص بك هو: *{$otp}*\n\n"
                . "⏳ سينتهي صلاحية هذا الرمز خلال 10 دقائق.\n\n"
                . "شكرًا لاختياركم لنا! 📦";
        }
        return "Hi! 👋\n\n"
            . "Your signup verification code is: *{$otp}*\n\n"
            . "⏳ This code will expire in 10 minutes.\n\n"
            . "Thank you for choosing us! 📦";
    }
}
