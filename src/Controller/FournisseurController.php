<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use App\Service\FournisseurPdfExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fournisseurs')]
class FournisseurController extends AbstractController
{
    #[Route('', name: 'app_fournisseur_index', methods: ['GET'])]
    public function index(Request $request, FournisseurRepository $fournisseurRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $zone = trim((string) $request->query->get('zone', ''));
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $fournisseurRepository->searchPaginated(
            $q !== '' ? $q : null,
            $zone !== '' ? $zone : null,
            $page,
            15,
        );

        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $pagination['items'],
            'pagination' => $pagination,
            'q' => $q,
            'zone' => $zone,
            'zones' => $fournisseurRepository->findDistinctZones(),
            'total' => $pagination['total'],
        ]);
    }

    #[Route('/export/pdf', name: 'app_fournisseur_export_pdf', methods: ['GET'])]
    public function exportPdf(
        Request $request,
        FournisseurRepository $fournisseurRepository,
        FournisseurPdfExporter $pdfExporter,
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        $zone = trim((string) $request->query->get('zone', ''));
        $fournisseurs = $fournisseurRepository->search(
            $q !== '' ? $q : null,
            $zone !== '' ? $zone : null,
        );
        $pdf = $pdfExporter->export(
            $fournisseurs,
            $q !== '' ? $q : null,
            $zone !== '' ? $zone : null,
        );

        $filename = sprintf('fournisseurs_%s.pdf', (new \DateTimeImmutable())->format('Y-m-d'));

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    #[Route('/new', name: 'app_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fournisseur);
            $em->flush();
            $this->addFlash('success', 'Fournisseur créé.');

            return $this->redirectToRoute('app_fournisseur_index');
        }

        return $this->render('fournisseur/form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau fournisseur',
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_show', methods: ['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fournisseur->touch();
            $em->flush();
            $this->addFlash('success', 'Fournisseur mis à jour.');

            return $this->redirectToRoute('app_fournisseur_show', ['id' => $fournisseur->getId()]);
        }

        return $this->render('fournisseur/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le fournisseur',
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->request->get('_token'))) {
            $em->remove($fournisseur);
            $em->flush();
            $this->addFlash('success', 'Fournisseur supprimé.');
        }

        return $this->redirectToRoute('app_fournisseur_index');
    }
}
