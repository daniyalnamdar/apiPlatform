<?php

namespace App\Security\Voter;

use App\Entity\CheeseListing;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CheeseListingVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\CheeseListing;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var CheeseListing $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                if ($subject->getOwner() === $user){
                    return true;
                }
                if ($this->security->isGranted('ROLE_ADMIN')){
                    return true;
                }
                return false;
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        throw new \RuntimeException(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
