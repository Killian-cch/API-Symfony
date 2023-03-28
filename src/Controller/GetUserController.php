<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetUserController extends AbstractController
{
    public function __invoke(): User
    {
        $user = $this->getUser();
        return $user;
    }
}