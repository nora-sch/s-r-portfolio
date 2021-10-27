<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ResetPasswordAction
{
    /**
     *
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordEncoder, EntityManagerInterface $entityManager, JWTTokenManagerInterface $tokenManager)
    {
        $this->validator = $validator;
        $this->userPasswordHasher = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }
    // Warning: the __invoke() method parameter MUST be called $data, otherwise, it will not be filled correctly!
    public function __invoke(User $data)
    {
        // __invoke instanciates the class as a single methode
        // $reset = newResetPasswordAction();
        // $reset();

        // var_dump(
        //     $data->getNewPassword(),
        //     $data->getNewRetypedPassword(),
        //     $data->getOldPassword(),
        //     $data->getRetypedPassword()
        // );
        // die;



        $this->validator->validate($data, ['groups' => 'put-reset-password']);

        $data->setPassword(
            $this->userPasswordHasher->hashPassword($data, $data->getNewPassword())
        );

        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);
    }
}
