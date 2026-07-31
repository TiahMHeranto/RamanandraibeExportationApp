<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
use App\Service\PersonnelPdfExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personnels')]
class PersonnelController extends AbstractController
{
    #[Route('', name: 'app_personnel_index', methods: ['GET'])]
    public function index(Request $request, PersonnelRepository $personnelRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $personnelRepository->searchPaginated(
            $q !== '' ? $q : null,
            $page,
            15,
        );

        return $this->render('personnel/index.html.twig', [
            'personnels' => $pagination['items'],
            'pagination' => $pagination,
            'q' => $q,
            'total' => $pagination['total'],
        ]);
    }

    #[Route('/export/pdf', name: 'app_personnel_export_pdf', methods: ['GET'])]
    public function exportPdf(
        Request $request,
        PersonnelRepository $personnelRepository,
        PersonnelPdfExporter $pdfExporter,
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        $personnels = $personnelRepository->search($q !== '' ? $q : null);
        $pdf = $pdfExporter->export($personnels, $q !== '' ? $q : null);

        $filename = sprintf(
            'personnels_%s%s.pdf',
            (new \DateTimeImmutable())->format('Y-m-d'),
            $q !== '' ? '_filtre' : ''
        );

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    #[Route('/new', name: 'app_personnel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($personnel);
            $em->flush();
            $this->addFlash('success', 'Personnel créé.');

            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau personnel',
        ]);
    }

    #[Route('/{id}', name: 'app_personnel_show', methods: ['GET'])]
    public function show(Personnel $personnel): Response
    {
        return $this->render('personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personnel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personnel $personnel, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personnel->touch();
            $em->flush();
            $this->addFlash('success', 'Personnel mis à jour.');

            return $this->redirectToRoute('app_personnel_show', ['id' => $personnel->getId()]);
        }

        return $this->render('personnel/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le personnel',
        ]);
    }

    #[Route('/{id}', name: 'app_personnel_delete', methods: ['POST'])]
    public function delete(Request $request, Personnel $personnel, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personnel->getId(), $request->request->get('_token'))) {
            $em->remove($personnel);
            $em->flush();
            $this->addFlash('success', 'Personnel supprimé.');
        }

        return $this->redirectToRoute('app_personnel_index');
    }
}
