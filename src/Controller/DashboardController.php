<?php

namespace App\Controller;

use App\Enum\ShipmentStatus;
use App\Repository\ArticleRepository;
use App\Repository\ArrivageRepository;
use App\Repository\ClientRepository;
use App\Repository\CouleurRepository;
use App\Repository\FournisseurRepository;
use App\Repository\HangarRepository;
use App\Repository\MagasinRepository;
use App\Repository\MouvementStockRepository;
use App\Repository\PersonnelRepository;
use App\Repository\ShipmentRepository;
use App\Repository\TraitementRepository;
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
        TraitementRepository $traitementRepository,
        MouvementStockRepository $mouvementStockRepository,
        ArticleRepository $articleRepository,
        CouleurRepository $couleurRepository,
        MagasinRepository $magasinRepository,
    ): Response {
        $tvSolde = '0.000';
        $articleTv = $articleRepository->findOneByCode('TV-MAJ');
        $couleurNat = $couleurRepository->findOneByCode('NAT');
        $magasin = $magasinRepository->findDefault();
        if ($articleTv && $couleurNat && $magasin) {
            $tvSolde = $mouvementStockRepository->computeSolde($articleTv, $couleurNat, $magasin);
        }

        $today = new \DateTimeImmutable('today');
        $ecarts = 0;
        foreach ($traitementRepository->findBy(['dateTraitement' => $today]) as $t) {
            if (abs((float) $t->getEcartPoids()) > 0.5) {
                ++$ecarts;
            }
        }

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
            'tvSolde' => $tvSolde,
            'kgTraitesJour' => $traitementRepository->sumPoidsSortieToday(),
            'ecartsEquilibre' => $ecarts,
            'latestTraitements' => $traitementRepository->findBy([], ['dateTraitement' => 'DESC', 'id' => 'DESC'], 5),
        ]);
    }
}
