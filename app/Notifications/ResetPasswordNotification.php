<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends ResetPasswordBase
{
    use Queueable;

    public function toMail($notifiable)
    {
        $url = url(config('app.url').route('password.reset', $this->token, false));

        // Send reset URL with Mailjet
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
                    'TemplateID' =>  4984810,
                    'TemplateLanguage' => true,
                    'Subject' => "Password Reset",
                    'Variables' => [
                        'reset_url' => $url,
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

        return (new MailMessage)->subject('Validation compte');
    }
}
