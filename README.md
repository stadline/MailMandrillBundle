Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require stadline/mail-mandrill-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

The Bundle use HIPMandrillBundle that you need to install first : https://github.com/Hipaway-Travel/HipMandrillBundle .

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            //Add HipMandrillBundle first
            new Hip\MandrillBundle\HipMandrillBundle(),
            new Stadline\MailMandrillBundle\StadlineMailMandrillBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Update your config.yml
-------------------------

```
    #Update configuration for hip_mandrill
    hip_mandrill:
        api_key: %mandrill_api_key% #Add the key in your parameters.dist.yml and parameters.yml
        default:
            sender: site@test.fr
            sender_name: Admin Root

    stadline_mail_mandrill: ~
```


Usage
============

//Todo