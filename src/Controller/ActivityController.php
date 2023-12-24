<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activity;
use App\Entity\Monitor;
use App\Entity\ActivityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use InvalidArgumentException;




class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'get_activities', methods: ['GET'], format:'json')]
    public function getAll(Request $request, ActivityRepository $entityManager): JsonResponse
    {
        $dateParam = $request->query->get('date_param');
        try {
            $date = DateTime::createFromFormat('d-m-Y', $dateParam);
            if (!$date) {
                throw new \Exception('Invalid date format. Please use dd-MM-yyyy.');
            }
            $formattedDate = $date->format('Y-m-d');
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
        $data = [];
        try {
            $startDate = new DateTime($formattedDate . ' 00:00:00');
            $endDate = new DateTime($formattedDate . ' 23:59:59');
            $activities = $entityManager->createQueryBuilder('a')
                ->where('a.date_start BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
        if (!$activities) {
            return $this->json(['error' => 'No activities for the specified date'], 404);
        }
        foreach ($activities as $activity) {
            $monitorsData = [];
            foreach ($activity->getMonitores() as $monitor) {
                $monitorsData[] = [
                    'monitor_id' => $monitor->getId(),
                    'name'=> $monitor->getName(),
                    'email'=> $monitor->getEmail(),
                    'phone'=> $monitor->getPhone(),
                    'photo'=>$monitor->getPhoto(),
                ];
            }
            $data[] = [
                'id' => $activity->getId(),
                'activity_type_id' => $activity->getActivityType()->getId(),
                'monitors' => $monitorsData,
                'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
                'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
            ];
        }
        return $this->json($data);
    }

    #[Route('/activities', name: 'post_activity', methods: ['POST'], format:'json')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    try {
        $requestData = json_decode($request->getContent(), true);
        $requiredFields = ['activity_type_id', 'date_start', 'date_end', 'monitors'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field])) {
                throw new \Exception("Missing required field: $field");
            }
        }
        if(!$this->existsActivityTypeById(json_encode($requestData['activity_type_id']), $entityManager)){
            throw new \Exception("This type of activity does not exist.");
        }
        foreach ($requestData['monitors'] as $monitorData) {
            if (!$this->existsMonitorById($monitorData['id'], $entityManager)) {
                throw new \Exception("One or more monitors do not exist.");
            }
        }
        $firstMonitor = reset($requestData['monitors']);
        $monitorId = $firstMonitor['id'];
        $monitor = $entityManager->getRepository(Monitor::class)->find($monitorId);
        if (!$monitor) {
            throw new \Exception("The monitor does not exist.");
        }
        $startDateTime = new DateTime($requestData['date_start']);
        $endDateTime = new DateTime($requestData['date_end']);
        $duration = $this->calculateDuration($requestData['date_start'], $requestData['date_end']);
        if ($duration !== 90) {
            throw new \Exception("Invalid duration. Only classes of 90 minutes are allowed.");
        }
        $allowedStartTimes = ['09:00:00', '13:30:00', '17:30:00'];
        $startTime = $startDateTime->format('H:i:s');
        if (!in_array($startTime, $allowedStartTimes)) {
            throw new \Exception("Invalid start time. Only classes starting at 09:00, 13:30, and 17:30 are allowed.");
        }
        $activity = new Activity(); 
        $activityTypeRepository = $entityManager->getRepository(ActivityType::class);
        $activityType = $activityTypeRepository->find($requestData['activity_type_id']);
        if (!$activityType) {
            throw new \Exception("Activity type with ID {$requestData['activity_type_id']} not found.");
        }
        $activity->setActivityType($activityType);
        $activity->setDateStart(new DateTime($requestData['date_start']));
        $activity->setDateEnd(new DateTime($requestData['date_end']));
        $activity->addMonitore($monitor);
        $activity->addMonitorById($monitor->getId(), $entityManager);
        $entityManager->persist($activity);
        $entityManager->flush();
        return $this->json(['message' => 'Activity created successfully'], 201);
    } catch (\Exception $e) {
        return $this->json(['error' => $e->getMessage()], 400);
    }
}

    #[Route('/activities/{id}', name: 'activity_update', methods: ['PUT'])]
    public function updateActivity(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }
        $activity = $entityManager->getRepository(Activity::class)->find($id);
        if (!$activity) {
            return $this->json(['error' => 'Activity not found'], 404);
        }
        $validationErrors = $this->validateActivityData($data, $entityManager);
        if (!empty($validationErrors)) {
            return $this->json(['errors' => $validationErrors], 400);
        }
        $activity->setDateStart(new DateTime($data['date_start']));
        $activity->setDateEnd(new DateTime($data['date_end']));
        $activity->setActivityType($entityManager->getReference(ActivityType::class, $data['activity_type_id']));
        $activity->getMonitores()->clear();
        foreach ($data['monitors'] as $monitorId) {
            $monitor = $entityManager->getReference(Monitor::class, $monitorId);
            $activity->addMonitore($monitor);
        }
        $entityManager->flush();
    
        return $this->json(['message' => 'Activity updated successfully']);
    }



    #[Route('/activities/{id}', name: 'delete_activity', methods: ['DELETE'], format:'json')]
    public function deleteActivity(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $activity = $entityManager->getRepository(Activity::class)->find($id);
        if (!$activity) {
            return $this->json(['error' => 'Activity not found'], 404);
        }
        $entityManager->remove($activity);
        $entityManager->flush();
        return $this->json(['message' => 'Activity deleted successfully']);
    }


    private function validateActivityData(array $data, EntityManagerInterface $entityManager): array
    {
        $errors = [];
        if (!isset($data['monitors']) || !is_array($data['monitors']) || empty($data['monitors'])) {
            $errors[] = 'At least one monitor is required.';
        } else {
            $firstMonitor = reset($data['monitors']);
            if (!isset($firstMonitor['id']) || empty($firstMonitor['id'])) {
                $errors[] = 'Monitor ID is required.';
            } else {
                $monitorId = $firstMonitor['id'];
                $monitor = $entityManager->getRepository(Monitor::class)->find($monitorId);
                if (!$monitor) {
                    $errors[] = 'The monitor does not exist.';
                }
            }
        }
        $activitytype = $entityManager->getRepository(ActivityType::class)->find($data['activity_type_id']);
        if (!isset($data['date_start']) || !$this->isValidDateFormat($data['date_start'])) {
            $errors[] = 'Invalid date_start format. Use Y-m-d H:i:s.';
        }
        if (!isset($data['date_end']) || !$this->isValidDateFormat($data['date_end'])) {
            $errors[] = 'Invalid date_end format. Use Y-m-d H:i:s.';
        }
        if (isset($data['date_start']) && isset($data['date_end'])) {
            $startDateTime = new DateTime($data['date_start']);
            $endDateTime = new DateTime($data['date_end']);
            $duration = $this->calculateDuration($data['date_start'], $data['date_end']);
            if ($duration !== 90) {
                $errors[] = 'Invalid duration. Only classes of 90 minutes are allowed.';
            }
            $allowedStartTimes = ['09:00:00', '13:30:00', '17:30:00'];
            $startTime = $startDateTime->format('H:i:s');
            if (!in_array($startTime, $allowedStartTimes)) {
                $errors[] = 'Invalid start time. Only classes starting at 09:00, 13:30, and 17:30 are allowed.';
            }
        }
        if (!isset($data['monitors']) || !is_array($data['monitors']) || empty($data['monitors'])) {
            $errors[] = 'At least one monitor is required.';
        }
        if (!isset($data['activity_type_id']) || empty($data['activity_type_id'])) {
            $errors[] = 'Activity type is required.';
        }
        if(!$monitor){
            $errors[] = 'The monitor does not exist.';
        }
        if(!$activitytype){
            $errors[] = 'This type of activity does not exist.';
        }
        return $errors;
    }
    private function isValidDateFormat(string $date): bool
    {
        $dateFormat = 'Y-m-d H:i:s';
        $dateTime = DateTime::createFromFormat($dateFormat, $date);
        return $dateTime && $dateTime->format($dateFormat) === $date;
    }
    private function calculateDuration(string $startDate, string $endDate): int
    {
        $format = 'Y-m-d H:i:s';
        $startDateTime = DateTime::createFromFormat($format, $startDate);
        $endDateTime = DateTime::createFromFormat($format, $endDate);
        if (!$startDateTime || !$endDateTime) {
            throw new InvalidArgumentException('Invalid date format');
        }
        $interval = $startDateTime->diff($endDateTime);
        return $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
    }
    private function existsMonitorById(string $monitorId, EntityManagerInterface $entityManager): bool
    {
        $monitor = $entityManager->getRepository(Monitor::class)->find($monitorId);
        if (!$monitor) {
            return false;
        }
        return true;
    }
    private function existsActivityTypeById(string $activityTypeId, EntityManagerInterface $entityManager): bool
    {
        $activityType = $entityManager->getRepository(ActivityType::class)->find($activityTypeId);
        if (!$activityType) {
            return false;
        }
        return true;
    }




}
