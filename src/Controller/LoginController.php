<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controlador para gestionar el inicio de sesión y cierre de sesión.
 */
final class LoginController extends AbstractController
{
    /**
     * Muestra la página de inicio de sesión.
     *
     * @param AuthenticationUtils $authenticationUtils Utilidad para la autenticación.
     * @return Response La respuesta HTTP.
     */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Maneja el cierre de sesión.
     *
     * @param AuthenticationUtils $authenticationUtils Utilidad para la autenticación.
     * @return Response La respuesta HTTP.
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(AuthenticationUtils $authenticationUtils): Response
    {
    }
}