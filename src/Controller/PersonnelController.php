<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
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

        return $this->render('personnel/index.html.twig', [
            'personnels' => $personnelRepository->search($q !== '' ? $q : null),
            'q' => $q,
            'total' => $personnelRepository->count([]),
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
