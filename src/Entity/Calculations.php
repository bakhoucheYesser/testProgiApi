<?php

namespace App\Entity;

use App\Repository\CalculationsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: CalculationsRepository::class)]
class Calculations extends BaseEntity
{

    #[ORM\ManyToOne(inversedBy: 'calculations')]
    private ?User $user = null;

    #[Groups("user")]
    #[ORM\ManyToOne(inversedBy: 'calculations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VehiculeType $vehicle_type = null;


    #[Groups("user")]
    #[ORM\Column]
    private ?float $base_price = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?float $basic_fee = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?float $special_fee = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?float $association_fee = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?float $storage_fee = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?float $total_price = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getVehicleType(): ?VehiculeType
    {
        return $this->vehicle_type;
    }

    public function setVehicleType(?VehiculeType $vehicle_type): static
    {
        $this->vehicle_type = $vehicle_type;

        return $this;
    }

    public function getBasePrice(): ?float
    {
        return $this->base_price;
    }

    public function setBasePrice(float $base_price): static
    {
        $this->base_price = $base_price;

        return $this;
    }

    public function getBasicFee(): ?float
    {
        return $this->basic_fee;
    }

    public function setBasicFee(float $basic_fee): static
    {
        $this->basic_fee = $basic_fee;

        return $this;
    }

    public function getSpecialFee(): ?float
    {
        return $this->special_fee;
    }

    public function setSpecialFee(float $special_fee): static
    {
        $this->special_fee = $special_fee;

        return $this;
    }

    public function getAssociationFee(): ?float
    {
        return $this->association_fee;
    }

    public function setAssociationFee(float $association_fee): static
    {
        $this->association_fee = $association_fee;

        return $this;
    }

    public function getStorageFee(): ?float
    {
        return $this->storage_fee;
    }

    public function setStorageFee(float $storage_fee): static
    {
        $this->storage_fee = $storage_fee;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }
}
