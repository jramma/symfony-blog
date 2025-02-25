<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM.EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador para gestionar los usuarios.
 */
final class UserController extends AbstractController
{

    private EntityManagerInterface $em;

    /**
     * Constructor del controlador.
     *
     * @param EntityManagerInterface $em El administrador de entidades.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Maneja el registro de nuevos usuarios.
     *
     * @param Request $request La solicitud HTTP.
     * @param UserPasswordHasherInterface $passwordHasher El servicio para hashear contraseñas.
     * @return Response La respuesta HTTP.
     */
    #[Route('/registration', name: 'user_registration')]
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $plainTextPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('user_registration');
        }
        return $this->render('user/index.html.twig', [
            'registration_form' => $form->createView(),
        ]);
    }
}