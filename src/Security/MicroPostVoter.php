<?php
/**
 * Created by PhpStorm.
 * User: Joro
 * Date: 9/9/2018
 * Time: 1:43 PM
 */

namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @var AccessDecsionManagerInterface
     */
    private $decssionManageer;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decssionManageer = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }

        if(!$subject instanceof MicroPost){
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->decssionManageer->decide($token, [User::ROLE_ADMIN])){
            return true;
        }

        $authenticatedUser = $token->getUser();

        if(!$authenticatedUser instanceof User){
            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return $microPost->getUser()->getId() === $authenticatedUser->getId();
    }
}