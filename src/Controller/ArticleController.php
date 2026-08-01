<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    #[Route('', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $repo): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $repo->findBy([], ['libelle' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article créé.');
            return $this->redirectToRoute('app_article_index');
        }
        return $this->render('article/form.html.twig', ['form' => $form, 'title' => 'Nouvel article']);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Article mis à jour.');
            return $this->redirectToRoute('app_article_index');
        }
        return $this->render('article/form.html.twig', ['form' => $form, 'title' => 'Modifier l\'article']);
    }
}
