<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ActivityType;
use Doctrine\ORM\EntityManagerInterface;

class ActivityTypeController extends AbstractController
{

    #[Route('/activity-types', name: 'get_activity_types', methods: ['GET'], format:'json')]
    public function getActivityTypes(EntityManagerInterface $entityManager): JsonResponse
    {
        $activityTypeRepository = $entityManager->getRepository(ActivityType::class);
        $activityTypes = $activityTypeRepository->findAll();

        // Construir un array para la respuesta JSON
        $responseArray = [];
        foreach ($activityTypes as $activityType) {
            $responseArray[] = [
                'id' => $activityType->getId(),
                'name' => $activityType->getName(),
                'number_monitors' => $activityType->getNumberMonitors(),
            ];
        }
        return $this->json($responseArray);
    }
}
