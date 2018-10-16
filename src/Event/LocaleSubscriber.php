<?php

namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents()
   {
       return [
           KernelEvents::REQUEST => [
               [
                   'onKernelRequest', 20
               ]
           ]
       ];
   }

   public function onKernelRequest(GetResponseEvent $event)
   {
       $request = $event->getRequest();

       if(!$request->hasPreviousSession()){
           return;
       }
       var_dump($request->hasPreviousSession());
       if($locale = $request->attributes->get('_locale')){
           $request->getSession()->set('_locale', $locale);
           var_dump($locale. ' if');
       }else{
           var_dump($request->getSession()->get('_locale'). ' else');
           $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
       }
       var_dump( $request->getLocale());
   }
}