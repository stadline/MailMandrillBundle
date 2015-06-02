<?php

namespace Stadline\MailMandrillBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Hip\MandrillBundle\Dispatcher;
use Hip\MandrillBundle\Message;
use Stadline\MailMandrillBundle\Exception\MailException;
use Stadline\MailMandrillBundle\Mail\MandrillMailInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author fabien
 */
class MailManager
{

    /**
     *
     * @var Dispatcher
     */
    private $mandrillDispatcher;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var Router
     */
    private $mailContent = null;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendEmail($addresses, $class, $params = array()){


        //Check the Mail class
        if(!class_exists($class)) {
            throw new MailException('The mail class need to exist');
        }
        
        $mail = new $class();

        $message = new Message();
        
        $this->prepareAddresses($addresses, $message);
        
        foreach($mail->cleanParams($params) as $keys => $param) {
            $message->addGlobalMergeVar($keys, $param);
        }
        
        try {
            $result = $this->mandrillDispatcher->send($message, $mail->getTemplate());
            $this->logger->info('[MailMandrill] : Email '.$class.' sent with success');
        } catch (\Exception $e) {
            $this->logger->error('[MailMandrill] : Send email via mandril failed : ' . print_r($e->getMessage(), true));
            return false;
        }
        
    }
    
    public function sendRawEmail($addresses, $html){

        $message = new Message();
        $message->setHtml($html);
        
        $this->prepareAddresses($addresses, $message);
        
        try {
            $result = $this->mandrillDispatcher->send($message);
            $this->logger->info('[MailMandrill] : Raw email sent with success');
        } catch (\Exception $e) {
            $this->logger->error('[MailMandrill] : Send email via mandril failed : ' . print_r($e->getMessage(), true));
            return false;
        }
        
    }
    
    /**
     * Set Addresses to used in templates
     * 
     * @param type $addresses
     * @param Message $message
     * @throws MailException
     */
    private function prepareAddresses($addresses, Message $message) 
    {
         //Check the Mail class
        if(!array_key_exists('to', $addresses)) {
            throw new MailException('You need to provide at least one destinatary.');
        }
        
        if(!is_array($addresses['to'])) {
            $addresses['to'] = array($addresses['to']);
        } 
        
        foreach($addresses['to'] as $email) {
            $message->addTo($email);
        }

        if(key_exists('cc', $addresses)) {
            $message->setBccAddress($addresses['cc']);
        }
        
        //Set Reply-To
        if(array_key_exists('Reply-To', $addresses)) {
           $message->addHeader('Reply-To', $addresses['Reply-To']);
        }
        
    }

    public function setMandrillDispatcher($mandrillDispatcher)
    {
        $this->mandrillDispatcher = $mandrillDispatcher;
    }

}
