<?php

namespace App\Controller;

use App\Entity\Traitement;
use App\Entity\TraitementLigne;
use App\Form\TraitementType;
use App\Repository\ArticleRepository;
use App\Repository\CouleurRepository;
use App\Repository\HangarRepository;
use App\Repository\MagasinRepository;
use App\Repository\TraitementRepository;
use App\Service\TraitementPdfExporter;
use App\Service\TraitementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/traitements')]
class TraitementController extends AbstractController
{
    #[Route('', name: 'app_traitement_index', methods: ['GET'])]
    public function index(Request $request, TraitementRepository $repo, HangarRepository $hangarRepository): Response
    {
        $dateStr = trim((string) $request->query->get('date', ''));
        $date = $dateStr !== '' ? new \DateTimeImmutable($dateStr) : null;
        $hangarId = $request->query->getInt('hangar') ?: null;
        $trieuseId = $request->query->getInt('trieuse') ?: null;
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $repo->searchPaginated($date, $hangarId, $trieuseId, $page, 15);

        return $this->render('traitement/index.html.twig', [
            'traitements' => $pagination['items'],
            'pagination' => $pagination,
            'date' => $dateStr,
            'hangarId' => $hangarId,
            'trieuseId' => $trieuseId,
            'total' => $pagination['total'],
            'hangars' => $hangarRepository->findBy(['actif' => true], ['numero' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_traitement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TraitementService $traitementService,
        MagasinRepository $magasinRepository,
        ArticleRepository $articleRepository,
        CouleurRepository $couleurRepository,
        HangarRepository $hangarRepository,
    ): Response {
        $traitement = new Traitement();
        $traitement->setMagasin($magasinRepository->findDefault());
        $traitement->setArticleSource($articleRepository->findOneByCode('TV-MAJ'));
        $traitement->setCouleurSource($couleurRepository->findOneByCode('NAT'));
        $hangars = $hangarRepository->findBy(['actif' => true], ['numero' => 'ASC']);
        if ($hangars) {
            $traitement->setHangar($hangars[0]);
        }
        $traitement->addLigne(new TraitementLigne());
        $traitement->setReference('BT-TMP');

        $form = $this->createForm(TraitementType::class, $traitement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $traitementService->save($traitement, true);
            $ecart = abs((float) $traitement->getEcartPoids());
            if ($ecart > 0.5) {
                $this->addFlash('error', sprintf('Attention: écart de poids %s kg (sortie vs entrées).', $traitement->getEcartPoids()));
            } else {
                $this->addFlash('success', 'Traitement enregistré et stock mis à jour.');
            }
            return $this->redirectToRoute('app_traitement_show', ['id' => $traitement->getId()]);
        }

        return $this->render('traitement/form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau traitement',
        ]);
    }

    #[Route('/{id}', name: 'app_traitement_show', methods: ['GET'])]
    public function show(Traitement $traitement): Response
    {
        return $this->render('traitement/show.html.twig', ['traitement' => $traitement]);
    }

    #[Route('/{id}/edit', name: 'app_traitement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Traitement $traitement, TraitementService $traitementService): Response
    {
        $form = $this->createForm(TraitementType::class, $traitement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $traitementService->save($traitement, false);
            $this->addFlash('success', 'Traitement mis à jour.');
            return $this->redirectToRoute('app_traitement_show', ['id' => $traitement->getId()]);
        }
        return $this->render('traitement/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le traitement',
        ]);
    }

    #[Route('/{id}/pdf', name: 'app_traitement_pdf', methods: ['GET'])]
    public function pdf(Traitement $traitement, TraitementPdfExporter $exporter): Response
    {
        $pdf = $exporter->export($traitement);
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$traitement->getReference().'.pdf"',
        ]);
    }
}
