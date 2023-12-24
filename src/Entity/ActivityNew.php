<?php

namespace App\Entity;

use App\Repository\ActivityNewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityNewRepository::class)]
class ActivityNew
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_end = null;

    #[ORM\Column]
    private ?int $activity_type_id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $monitors_id = [];

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

    public function getActivityTypeId(): ?int
    {
        return $this->activity_type_id;
    }

    public function setActivityTypeId(int $activity_type_id): static
    {
        $this->activity_type_id = $activity_type_id;

        return $this;
    }

    public function getMonitorsId(): array
    {
        return $this->monitors_id;
    }

    public function setMonitorsId(array $monitors_id): static
    {
        $this->monitors_id = $monitors_id;

        return $this;
    }
}
