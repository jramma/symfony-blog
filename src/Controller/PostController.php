<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
final class PostController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/', name: 'app_post')]
    public function insert(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, Security $security): Response
    {
        $post = new Post();
        $posts = $em->getRepository(Post::class)->findAllPosts();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // Verificar si el usuario está autenticado
        $user = $security->getUser(); // Obtiene el usuario autenticado

        if (!$user) {
            $this->addFlash('error', 'Debes estar autenticado para crear un post.');
            return $this->redirectToRoute('app_login'); // Redirige al login si el usuario no está autenticado
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejo del archivo
            $file = $form->get('file')->getData();
            if ($file && $file->isValid()) {
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($fileName);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($this->getParameter('files_directory'), $newFilename);
                    $post->setFile($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir archivo: ' . $e->getMessage());
                    return $this->redirectToRoute('app_post');
                }
            }

            // Generación de la URL a partir del título
            $url = str_replace(' ', '-', trim($form->get('title')->getData()));
            $post->setUrl($url);

            // Asignar el usuario autenticado
            $post->setUser($user);

            // Guardar en la base de datos
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post creado con éxito.');
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }


    #[Route('/post/{url}', name: 'post_detail')]
    public function detail(string $url, PostRepository $postRepository): Response
    {
        // Buscar el post basado en la URL amigable
        $post = $postRepository->findOneBy(['url' => $url]);

        if (!$post) {
            throw $this->createNotFoundException('Post no encontrado.');
        }

        return $this->render('post/detail.html.twig', [
            'post' => $post,
        ]);
    }


}
