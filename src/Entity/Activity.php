<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;


#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_end = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityType $activity_type = null;

    #[ORM\ManyToMany(targetEntity: Monitor::class)]
    private Collection $monitores;

    public function __construct()
    {
        $this->monitores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): static
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getActivityType(): ?ActivityType
    {
        return $this->activity_type;
    }

    public function setActivityType(?ActivityType $activity_type): static
    {
        $this->activity_type = $activity_type;

        return $this;
    }

    /**
     * @return Collection<int, Monitor>
     */
    public function getMonitores(): Collection
    {
        return $this->monitores;
    }

    public function addMonitore(Monitor $monitore): static
    {
        if (!$this->monitores->contains($monitore)) {
            $this->monitores->add($monitore);
        }

        return $this;
    }

    public function removeMonitore(Monitor $monitore): static
    {
        $this->monitores->removeElement($monitore);

        return $this;
    }


    public function addMonitorById(int $monitorId, EntityManagerInterface $entityManager): static
    {
        $monitor = $entityManager->getRepository(Monitor::class)->find($monitorId);

        if (!$monitor) {
            throw new \Exception("Monitor with ID $monitorId not found.");
        }

        $this->addMonitore($monitor);

        return $this;
    }

}
