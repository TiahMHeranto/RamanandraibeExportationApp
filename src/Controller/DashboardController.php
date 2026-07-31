<?php

namespace App\Controller;

use App\Enum\ShipmentStatus;
use App\Repository\ClientRepository;
use App\Repository\FournisseurRepository;
use App\Repository\PersonnelRepository;
use App\Repository\ProductRepository;
use App\Repository\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ClientRepository $clientRepository,
        FournisseurRepository $fournisseurRepository,
        PersonnelRepository $personnelRepository,
        ProductRepository $productRepository,
        ShipmentRepository $shipmentRepository,
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'personnelCount' => $personnelRepository->count(['actif' => true]),
            'fournisseurCount' => $fournisseurRepository->count(['actif' => true]),
            'clientCount' => $clientRepository->count([]),
            'productCount' => $productRepository->count(['active' => true]),
            'shipmentCount' => $shipmentRepository->count([]),
            'inTransitCount' => $shipmentRepository->countByStatus(ShipmentStatus::InTransit),
            'latestShipments' => $shipmentRepository->findLatest(),
        ]);
    }
}
