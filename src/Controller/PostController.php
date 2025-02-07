<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_post')]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/insert/post', name: 'insert_post')]
    public function insert(Request $request): Response
    {
        $user = $this->em->getRepository(User::class)->find(1);
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post', ['id' => $post->getId()]);
        }

        return $this->render('post/insert.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/update/post', name: 'update_post')]
    public function update(): Response
    {
        $post = $this->em->getRepository(Post::class)->find(1);
        $post->setTitle('Updated Post');
        $this->em->flush();
        return new Response('Post updated.');
    }

    #[Route('/delete/post', name: 'delete_post')]
    public function delete(): Response
    {
        $post = $this->em->getRepository(Post::class)->find(1);
        $this->em->remove($post);
        $this->em->flush();
        return new Response('Post deleted.');
    }

}
