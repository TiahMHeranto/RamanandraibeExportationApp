<?php

namespace App\Controller;

use App\Entity\Shipment;
use App\Entity\ShipmentLine;
use App\Form\ShipmentType;
use App\Repository\ShipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shipments')]
class ShipmentController extends AbstractController
{
    #[Route('', name: 'app_shipment_index', methods: ['GET'])]
    public function index(ShipmentRepository $shipmentRepository): Response
    {
        return $this->render('shipment/index.html.twig', [
            'shipments' => $shipmentRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_shipment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $shipment = new Shipment();
        $shipment->setReference('REX-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)));
        $shipment->setOriginPort('Toamasina');
        $shipment->addLine(new ShipmentLine());

        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shipment);
            $em->flush();
            $this->addFlash('success', 'Shipment created.');

            return $this->redirectToRoute('app_shipment_show', ['id' => $shipment->getId()]);
        }

        return $this->render('shipment/form.html.twig', [
            'form' => $form,
            'title' => 'New shipment',
        ]);
    }

    #[Route('/{id}', name: 'app_shipment_show', methods: ['GET'])]
    public function show(Shipment $shipment): Response
    {
        return $this->render('shipment/show.html.twig', [
            'shipment' => $shipment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shipment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shipment $shipment, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Shipment updated.');

            return $this->redirectToRoute('app_shipment_show', ['id' => $shipment->getId()]);
        }

        return $this->render('shipment/form.html.twig', [
            'form' => $form,
            'title' => 'Edit shipment',
        ]);
    }

    #[Route('/{id}', name: 'app_shipment_delete', methods: ['POST'])]
    public function delete(Request $request, Shipment $shipment, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shipment->getId(), $request->request->get('_token'))) {
            $em->remove($shipment);
            $em->flush();
            $this->addFlash('success', 'Shipment deleted.');
        }

        return $this->redirectToRoute('app_shipment_index');
    }
}
