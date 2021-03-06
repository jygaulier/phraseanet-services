<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MeAction extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route(path="/me")
     */
    public function __invoke()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $token = $this->security->getToken();
        $user = $token->getUser();

        if ($user instanceof User) {
            $data = [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'groups' => array_map(function (Group $group): string {
                    return $group->getName();
                }, $user->getGroups()->toArray()),
            ];
        } else {
            $data = [
                'roles' => $token->getRoleNames(),
            ];
        }

        return new JsonResponse($data);
    }
}
