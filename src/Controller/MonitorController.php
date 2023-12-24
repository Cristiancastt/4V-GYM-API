<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Monitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class MonitorController extends AbstractController
{
    #[Route('/monitors', name: 'monitors', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $monitors = $entityManager->getRepository(Monitor::class)->findAll();
        $data = [];
        if (!$monitors) {
            throw $this->createNotFoundException('No se encontraron actividades.');
        }
        foreach ($monitors as $monitor) {
            $data[] = [
                'id'=> $monitor->getId(),
                'name'=> $monitor->getName(),
                'email'=> $monitor->getEmail(),
                'phone'=> $monitor->getPhone(),
                'photo'=> $monitor->getPhoto(),

            ];
        };
        return $this->json($data);
    }




    #[Route('/monitors', name: 'monitor_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validación básica
        if (!$this->validateMonitorData($data)) {
            return $this->json(['error' => 'Invalid data for creating a monitor'], 400);
        }

        $monitor = new Monitor();
        $monitor->setName($data['name']);
        $monitor->setEmail($data['email']);
        $monitor->setPhone($data['phone']);
        $monitor->setPhoto($data['photo']);

        $entityManager->persist($monitor);
        $entityManager->flush();

        $dataNewMonitor[] = [
            'id'=> $monitor->getId(),
            'name'=> $monitor->getName(),
            'email'=> $monitor->getEmail(),
            'phone'=> $monitor->getPhone(),
            'photo'=> $monitor->getPhoto(),

        ];


        return $this->json($dataNewMonitor);
    }


    #[Route('/monitors/{id}', name: 'monitor_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }
        if (!is_array($data) || !$this->validateMonitorData($data)) {
            return $this->json(['error' => 'Invalid data for updating a monitor'], 400);
        }
        $monitor = $entityManager->getRepository(Monitor::class)->find($id);
        if (!$monitor) {
            return $this->json(['error' => 'Monitor not found'], 404);
        }
        $monitor->setName($data['name']);
        $monitor->setEmail($data['email']);
        $monitor->setPhone($data['phone']);
        $monitor->setPhoto($data['photo']);
        $dataNewMonitor[] = [
            'id'=> $monitor->getId(),
            'name'=> $monitor->getName(),
            'email'=> $monitor->getEmail(),
            'phone'=> $monitor->getPhone(),
            'photo'=> $monitor->getPhoto(),

        ];

        $entityManager->flush();
        return $this->json($dataNewMonitor);
    }


    #[Route('/monitors/{id}', name: 'monitor_delete', methods:"DELETE")]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        
        $monitor = $entityManager->getRepository(Monitor::class)->find($id);

        if (!$monitor) {
            return $this->json(['error' => 'Monitor not found'], 404);
        }

        $entityManager->remove($monitor);
        $entityManager->flush();

        return $this->json(['message' => 'Monitor deleted successfully']);
    }

    private function validateMonitorData(array $data): bool
    {
        return isset($data['name']) && isset($data['email']) && isset($data['phone']) && isset($data['photo']);
    }
}
