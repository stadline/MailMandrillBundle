<?php

namespace Stadline\MailMandrillBundle\Mail;

interface MandrillMailInterface
{
    /**
     * Return the Mandrill template name
     */
    public function getTemplate();
    
    /**
     * Return params for the template as an array to merge
     */
    public function cleanParams($params);
}
