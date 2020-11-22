<?php

namespace App\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshedTokenListener implements EventSubscriberInterface{


    private $cookieSecure = false;

    public function setRefreshToken(AuthenticationSuccessEvent $event){
        $refreshToken = $event->getData()['refresh_token'];

        $response = $event->getResponse();

        if($refreshToken){

            $response->headers->setCookie(new Cookie('REFRESH_TOKEN', $refreshToken, (
                new \DateTime())
                ->add(new \DateInterval('PT' . 2592000 . 'S')), '/', null, $this->cookieSecure));

        }

    }


    public static function getSubscribedEvents(){
        return[
            'lexik_jwt_authentication.on_authentication_success' =>[
                ['setRefreshToken']
            ]
            ];
    }

}