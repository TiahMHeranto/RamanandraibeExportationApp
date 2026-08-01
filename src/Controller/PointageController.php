<?php

namespace App\Controller;

use App\Entity\PointageJour;
use App\Repository\PersonnelRepository;
use App\Repository\PointageJourRepository;
use App\Service\RendementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pointage')]
class PointageController extends AbstractController
{
    #[Route('', name: 'app_pointage_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        PersonnelRepository $personnelRepository,
        PointageJourRepository $pointageJourRepository,
        EntityManagerInterface $em,
    ): Response {
        $dateStr = (string) $request->query->get('date', (new \DateTimeImmutable('today'))->format('Y-m-d'));
        $date = new \DateTimeImmutable($dateStr);
        $personnels = $personnelRepository->findBy(['actif' => true], ['numeroPersonnel' => 'ASC']);
        $existing = [];
        foreach ($pointageJourRepository->findByDate($date) as $p) {
            $existing[$p->getPersonnel()->getId()] = $p;
        }

        if ($request->isMethod('POST')) {
            $presents = $request->request->all('present') ?? [];
            foreach ($personnels as $personnel) {
                $pointage = $existing[$personnel->getId()] ?? (new PointageJour())
                    ->setDatePointage($date)
                    ->setPersonnel($personnel);
                $pointage->setPresent(isset($presents[(string) $personnel->getId()]));
                $em->persist($pointage);
            }
            $em->flush();
            $this->addFlash('success', 'Pointage enregistré.');
            return $this->redirectToRoute('app_pointage_index', ['date' => $dateStr]);
        }

        return $this->render('pointage/index.html.twig', [
            'date' => $dateStr,
            'personnels' => $personnels,
            'existing' => $existing,
        ]);
    }

    #[Route('/rendement', name: 'app_pointage_rendement', methods: ['GET'])]
    public function rendement(Request $request, RendementService $rendementService): Response
    {
        $fromStr = (string) $request->query->get('from', (new \DateTimeImmutable('first day of this month'))->format('Y-m-d'));
        $toStr = (string) $request->query->get('to', (new \DateTimeImmutable('today'))->format('Y-m-d'));
        $from = new \DateTimeImmutable($fromStr);
        $to = new \DateTimeImmutable($toStr);

        return $this->render('pointage/rendement.html.twig', [
            'from' => $fromStr,
            'to' => $toStr,
            'rows' => $rendementService->byPersonnel($from, $to),
        ]);
    }
}
