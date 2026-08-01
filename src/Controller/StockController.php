<?php

namespace App\Controller;

use App\Entity\MouvementStock;
use App\Enum\TypeOperationStock;
use App\Form\MouvementStockType;
use App\Repository\ArticleRepository;
use App\Repository\MagasinRepository;
use App\Repository\MouvementStockRepository;
use App\Service\StockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stock')]
class StockController extends AbstractController
{
    #[Route('', name: 'app_stock_index', methods: ['GET'])]
    public function index(StockService $stockService): Response
    {
        return $this->render('stock/index.html.twig', [
            'soldes' => $stockService->getSoldes(),
        ]);
    }

    #[Route('/mouvements', name: 'app_stock_mouvements', methods: ['GET'])]
    public function mouvements(
        Request $request,
        MouvementStockRepository $repo,
        ArticleRepository $articleRepository,
        MagasinRepository $magasinRepository,
    ): Response {
        $articleId = $request->query->getInt('article') ?: null;
        $magasinId = $request->query->getInt('magasin') ?: null;
        $article = $articleId ? $articleRepository->find($articleId) : null;
        $magasin = $magasinId ? $magasinRepository->find($magasinId) : null;

        return $this->render('stock/mouvements.html.twig', [
            'mouvements' => $repo->findFiltered(null, null, $article, $magasin),
            'articles' => $articleRepository->findActiveOrdered(),
            'magasins' => $magasinRepository->findActiveOrdered(),
            'articleId' => $articleId,
            'magasinId' => $magasinId,
        ]);
    }

    #[Route('/mouvement/new', name: 'app_stock_mouvement_new', methods: ['GET', 'POST'])]
    public function newMouvement(Request $request, StockService $stockService, MagasinRepository $magasinRepository): Response
    {
        $mouvement = new MouvementStock();
        $defaultMagasin = $magasinRepository->findDefault();
        if ($defaultMagasin) {
            $mouvement->setMagasin($defaultMagasin);
        }
        $mouvement->setTypeOperation(TypeOperationStock::ColorationSortie);
        $mouvement->setSens(TypeOperationStock::ColorationSortie->defaultSens());

        $form = $this->createForm(MouvementStockType::class, $mouvement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $stockService->createMouvement(
                    $mouvement->getTypeOperation(),
                    $mouvement->getArticle(),
                    $mouvement->getCouleur(),
                    $mouvement->getMagasin(),
                    (string) $mouvement->getPoids(),
                    $mouvement->getDateMouvement(),
                    $mouvement->getSens(),
                    $mouvement->getReference(),
                    $mouvement->getFournisseur(),
                    $mouvement->getContrat(),
                    $mouvement->getHangar(),
                    null,
                    null,
                    $mouvement->getObservations(),
                    true,
                    true,
                );
                $this->addFlash('success', 'Mouvement enregistré.');
                return $this->redirectToRoute('app_stock_mouvements');
            } catch (\Throwable $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('stock/mouvement_form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau mouvement de stock',
        ]);
    }
}
