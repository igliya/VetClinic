<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class APIService
{
    private $params;
    private $userRepository;
    private $passwordEncoder;
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        ParameterBagInterface $params,
        EntityManagerInterface $entityManager
    ) {
        $this->passwordEncoder = $encoder;
        $this->params = $params;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function checkUserCredentials($login, $password)
    {
        $user = $this->userRepository->findDoctorByLogin($login);
        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }
        if (! $this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new BadRequestException('Invalid credentials');
        }

        return $user;
    }
}
