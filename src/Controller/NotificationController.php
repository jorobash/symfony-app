<?php

namespace App\Controller;


use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class NotificationController
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends Controller
{

    /**
     * @var Notification
     */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        return $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount()
    {
      return new JsonResponse([
           'count' => $this->notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notification()
    {
        return $this->render('notification/notifications.html.twig',
              [
                  'notifications' => $this->notificationRepository->findBy([
                      'seen' => false,
                      'user' => $this->getUser()
                  ])
              ]
            );
    }

    /**
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     */
    public function acknowledge(Notification $notification)
    {

        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     */
    public function acknowledgeAll()
    {
        $this->notificationRepository->markAllAsReadByUser($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }
}