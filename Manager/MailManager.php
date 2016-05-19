<?php

namespace Stadline\MailMandrillBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Hip\MandrillBundle\Dispatcher;
use Hip\MandrillBundle\Message;
use Stadline\MailMandrillBundle\Exception\MailException;
use Stadline\MailMandrillBundle\Mail\MandrillMailInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
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
        $message->setMergeLanguage('handlebars');
        
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
    
    public function sendRawEmail($addresses, $html, $message = null){

        if($message == null) {
            $message = new Message();
        }
        if(!$message instanceof Message) {
            throw new Exception('Message need to be an instance of Hip\MandrillBundle\Message');
        }

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
        if(!isset($addresses['to'])) {
            throw new MailException('You need to provide at least one destinatary.');
        }
        
        if(!is_array($addresses['to'])) {
            $addresses['to'] = array($addresses['to']);
        } 
        
        foreach($addresses['to'] as $email) {
            $message->addTo($email);
        }

        if(isset($addresses['cc'])) {
            $message->setBccAddress($addresses['cc']);
        }

        //Set Reply-To
        if(isset($addresses['Reply-To'])) {
           $message->addHeader('Reply-To', $addresses['Reply-To']);
        }
        
    }

    public function setMandrillDispatcher($mandrillDispatcher)
    {
        $this->mandrillDispatcher = $mandrillDispatcher;
    }

}
