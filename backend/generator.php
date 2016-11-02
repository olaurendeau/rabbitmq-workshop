<?php

require_once __DIR__.'/vendor/autoload.php';

// Usage :
// > generator.php john.doe@foo.com
// Will generate a file and send it by mail to john.doe@foo.com

// Simulate slowness
sleep(rand(2,4));

// Send email
$mailer = Swift_Mailer::newInstance(Swift_SmtpTransport::newInstance('mailer', 1025));
$message = Swift_Message::newInstance('Invoice generated')
    ->setFrom(array('awesome@invoice.com' => 'Contact'))
    ->setTo(array($argv[1]))
    ->setBody('Please find your invoice attached !')
    ->attach(Swift_Attachment::fromPath(sprintf('%s/shared/Invoice_Template.pdf', __DIR__)));
;
$mailer->send($message);
