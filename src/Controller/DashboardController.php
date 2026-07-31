<?php

namespace App\Controller;

use App\Enum\ShipmentStatus;
use App\Repository\ArrivageRepository;
use App\Repository\ClientRepository;
use App\Repository\FournisseurRepository;
use App\Repository\HangarRepository;
use App\Repository\PersonnelRepository;
use App\Repository\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ArrivageRepository $arrivageRepository,
        ClientRepository $clientRepository,
        FournisseurRepository $fournisseurRepository,
        HangarRepository $hangarRepository,
        PersonnelRepository $personnelRepository,
        ShipmentRepository $shipmentRepository,
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'personnelCount' => $personnelRepository->count(['actif' => true]),
            'fournisseurCount' => $fournisseurRepository->count(['actif' => true]),
            'hangarCount' => $hangarRepository->count(['actif' => true]),
            'arrivageCount' => $arrivageRepository->count([]),
            'clientCount' => $clientRepository->count([]),
            'shipmentCount' => $shipmentRepository->count([]),
            'inTransitCount' => $shipmentRepository->countByStatus(ShipmentStatus::InTransit),
            'latestArrivages' => $arrivageRepository->findLatest(),
            'latestShipments' => $shipmentRepository->findLatest(),
        ]);
    }
}
