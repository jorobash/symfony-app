<?php


namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FollowingController
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends Controller
{

    /**
     * @Route("/follow/{id}", name="following_user")
     */
    public function followUser(User $userToFollow)
    {
        $currentUser = $this->getUser();

        if($userToFollow->getId() != $currentUser->getId()){
            $currentUser->follow($userToFollow);
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->redirectToRoute('micro_post_user', ['username' => $userToFollow->getUsername()]);

    }

    /**
     * @Route("/unfollow/{id}", name="unfollow_user")
     */
    public function unFollow(User $userToUnFollwo)
    {
        $currentUser = $this->getUser();
        if($userToUnFollwo->getId() != $currentUser->getId()){}
        $currentUser->getFollowing()->removeElement($userToUnFollwo);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('micro_post_user', ['username' => $currentUser->getUsername()]);
    }

}