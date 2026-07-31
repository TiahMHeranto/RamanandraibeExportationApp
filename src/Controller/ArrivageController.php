<?php

namespace App\Controller;

use App\Entity\Arrivage;
use App\Form\ArrivageType;
use App\Repository\ArrivageRepository;
use App\Service\ArrivagePdfExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/arrivages')]
class ArrivageController extends AbstractController
{
    #[Route('', name: 'app_arrivage_index', methods: ['GET'])]
    public function index(Request $request, ArrivageRepository $arrivageRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $arrivageRepository->searchPaginated(
            $q !== '' ? $q : null,
            $page,
            15,
        );

        return $this->render('arrivage/index.html.twig', [
            'arrivages' => $pagination['items'],
            'pagination' => $pagination,
            'q' => $q,
            'total' => $pagination['total'],
        ]);
    }

    #[Route('/export/pdf', name: 'app_arrivage_export_pdf', methods: ['GET'])]
    public function exportPdf(
        Request $request,
        ArrivageRepository $arrivageRepository,
        ArrivagePdfExporter $pdfExporter,
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        $arrivages = $arrivageRepository->search($q !== '' ? $q : null);
        $pdf = $pdfExporter->export($arrivages, $q !== '' ? $q : null);

        $filename = sprintf('arrivages_%s.pdf', (new \DateTimeImmutable())->format('Y-m-d'));

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    #[Route('/new', name: 'app_arrivage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, ArrivageRepository $arrivageRepository): Response
    {
        $arrivage = new Arrivage();
        $arrivage->setNumero($arrivageRepository->nextNumero(new \DateTimeImmutable('today')));

        $form = $this->createForm(ArrivageType::class, $arrivage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($arrivage);
            $em->flush();
            $this->addFlash('success', 'Arrivage enregistré.');

            return $this->redirectToRoute('app_arrivage_show', ['id' => $arrivage->getId()]);
        }

        return $this->render('arrivage/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvel arrivage',
        ]);
    }

    #[Route('/{id}', name: 'app_arrivage_show', methods: ['GET'])]
    public function show(Arrivage $arrivage): Response
    {
        return $this->render('arrivage/show.html.twig', [
            'arrivage' => $arrivage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_arrivage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Arrivage $arrivage, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArrivageType::class, $arrivage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrivage->touch();
            $em->flush();
            $this->addFlash('success', 'Arrivage mis à jour.');

            return $this->redirectToRoute('app_arrivage_show', ['id' => $arrivage->getId()]);
        }

        return $this->render('arrivage/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'arrivage',
        ]);
    }

    #[Route('/{id}', name: 'app_arrivage_delete', methods: ['POST'])]
    public function delete(Request $request, Arrivage $arrivage, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$arrivage->getId(), $request->request->get('_token'))) {
            $em->remove($arrivage);
            $em->flush();
            $this->addFlash('success', 'Arrivage supprimé.');
        }

        return $this->redirectToRoute('app_arrivage_index');
    }
}
