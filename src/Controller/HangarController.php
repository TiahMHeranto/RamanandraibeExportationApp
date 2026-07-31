<?php

namespace App\Controller;

use App\Entity\Hangar;
use App\Form\HangarType;
use App\Repository\HangarRepository;
use App\Service\HangarPdfExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/hangars')]
class HangarController extends AbstractController
{
    #[Route('', name: 'app_hangar_index', methods: ['GET'])]
    public function index(Request $request, HangarRepository $hangarRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $hangarRepository->searchPaginated(
            $q !== '' ? $q : null,
            $page,
            15,
        );

        return $this->render('hangar/index.html.twig', [
            'hangars' => $pagination['items'],
            'pagination' => $pagination,
            'q' => $q,
            'total' => $pagination['total'],
        ]);
    }

    #[Route('/export/pdf', name: 'app_hangar_export_pdf', methods: ['GET'])]
    public function exportPdf(
        Request $request,
        HangarRepository $hangarRepository,
        HangarPdfExporter $pdfExporter,
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        $hangars = $hangarRepository->search($q !== '' ? $q : null);
        $pdf = $pdfExporter->export($hangars, $q !== '' ? $q : null);

        $filename = sprintf('hangars_%s.pdf', (new \DateTimeImmutable())->format('Y-m-d'));

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    #[Route('/new', name: 'app_hangar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $hangar = new Hangar();
        $form = $this->createForm(HangarType::class, $hangar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($hangar);
            $em->flush();
            $this->addFlash('success', 'Hangar créé.');

            return $this->redirectToRoute('app_hangar_index');
        }

        return $this->render('hangar/form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau hangar',
        ]);
    }

    #[Route('/{id}', name: 'app_hangar_show', methods: ['GET'])]
    public function show(Hangar $hangar): Response
    {
        return $this->render('hangar/show.html.twig', [
            'hangar' => $hangar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hangar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hangar $hangar, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(HangarType::class, $hangar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hangar->touch();
            $em->flush();
            $this->addFlash('success', 'Hangar mis à jour.');

            return $this->redirectToRoute('app_hangar_show', ['id' => $hangar->getId()]);
        }

        return $this->render('hangar/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le hangar',
        ]);
    }

    #[Route('/{id}', name: 'app_hangar_delete', methods: ['POST'])]
    public function delete(Request $request, Hangar $hangar, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hangar->getId(), $request->request->get('_token'))) {
            $em->remove($hangar);
            $em->flush();
            $this->addFlash('success', 'Hangar supprimé.');
        }

        return $this->redirectToRoute('app_hangar_index');
    }
}
