<?php

namespace Shpartko\Madsms\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Shpartko\Madsms\Notifications\BaseNotification;
use Shpartko\Madsms\Traits\RelatedEventProperties;

use Shpartko\Madsms\Events\MessageCannotSend as RelatedEvent;

/**
 * Notification for any problems when message was not sended
 *
 * @package Shpartko\Madsms
 */
class MessageCannotSend extends BaseNotification
{
    use RelatedEventProperties;

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(trans('madsms::notifications.message_cannot_send_title', ['application_name' => $this->applicationName()]))
            ->line(trans('madsms::notifications.message_cannot_send_body', ['gateway_name' => $this->getGatewayName()]));

        $mailMessage->line('phone: '.$this->getPhoneNumber());
        $mailMessage->line('gateway: '.$this->getGatewayName());
        $mailMessage->line('message: '.$this->getMessage());

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)
            ->success()
            ->from(config('madsms.notifications.slack.username'), config('madms.notifications.slack.icon'))
            ->to(config('madsms.notifications.slack.channel'))
            ->content(trans('madsms::notifications.slack_message_cannot_send', ['application_name' => $this->applicationName(), 'gateway_name' => $this->getGatewayName()]))
            ->attachment(function (SlackAttachment $attachment) {
                 $attachment->fields([
                    'phone' => $this->getPhoneNumber(),
                    'gateway' => $this->getGatewayName(),
                    'message' => $this->getMessage(),
                 ]);
            })
            ;
    }
}
