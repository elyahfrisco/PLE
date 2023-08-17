<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailBase
{
    use Queueable;
 
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Send verification URL with Mailjet
        $mj = new \Mailjet\Client(getenv('MAILJET_APIKEY'), getenv('MAILJET_APISECRET'), true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "contact@lesplaisirsdeleau.fr",
                        'Name' => "lesplaisirsdeleau"
                    ],
                    'To' => [
                        [
                            'Email' => $notifiable->getEmailForVerification(),
                            'Name' => $notifiable->name
                        ]
                    ],
                    'TemplateID' => 4972161,
                    'TemplateLanguage' => true,
                    'Subject' => "Email Verification",
                    'Variables' => [
                        'verification_url' => $verificationUrl,
                    ]
                ]
            ]
        ];
        $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
        $response->success();
        if ($response->success()) {
            \Log::info("Mail sent successfully.");
        } else {
            \Log::error("Failed to send mail: " . $response->getReasonPhrase());
        }

        return (new MailMessage)->subject('Email Verification');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

}
