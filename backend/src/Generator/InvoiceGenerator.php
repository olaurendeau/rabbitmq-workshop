<?php

namespace Generator;

use Logger\Logger;

class InvoiceGenerator
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function generateAndSend($requestId, $email)
    {
        $this->logger->log($requestId, "Generating invoice");
        sleep(rand(2,4));

        // Send email
        $mailer = \Swift_Mailer::newInstance(\Swift_SmtpTransport::newInstance('mailer', 1025));
        $message = \Swift_Message::newInstance('Invoice generated')
            ->setFrom(array('awesome@invoice.com' => 'Contact'))
            ->setTo(array($email))
            ->setBody('Please download your invoice at http://localhost:4446/shared/Invoice_Template.pdf !')
        ;
        $mailer->send($message);

        $this->logger->log($requestId, "Invoice sent by email");
    }
}
