<?php

namespace App\Entity;

use App\Repository\VehiculeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeTypeRepository::class)]
class VehiculeType extends BaseEntity
{

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $basic_fee_min = null;

    #[ORM\Column]
    private ?float $basic_fee_max = null;

    #[ORM\Column]
    private ?float $basic_fee_rate = null;

    #[ORM\Column]
    private ?float $special_fee_rate = null;

    /**
     * @var Collection<int, Calculations>
     */
    #[ORM\OneToMany(targetEntity: Calculations::class, mappedBy: 'vehicle_type')]
    private Collection $calculations;

    public function __construct()
    {
        $this->calculations = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBasicFeeMin(): ?float
    {
        return $this->basic_fee_min;
    }

    public function setBasicFeeMin(float $basic_fee_min): static
    {
        $this->basic_fee_min = $basic_fee_min;

        return $this;
    }

    public function getBasicFeeMax(): ?float
    {
        return $this->basic_fee_max;
    }

    public function setBasicFeeMax(float $basic_fee_max): static
    {
        $this->basic_fee_max = $basic_fee_max;

        return $this;
    }

    public function getBasicFeeRate(): ?float
    {
        return $this->basic_fee_rate;
    }

    public function setBasicFeeRate(float $basic_fee_rate): static
    {
        $this->basic_fee_rate = $basic_fee_rate;

        return $this;
    }

    public function getSpecialFeeRate(): ?float
    {
        return $this->special_fee_rate;
    }

    public function setSpecialFeeRate(float $special_fee_rate): static
    {
        $this->special_fee_rate = $special_fee_rate;

        return $this;
    }

    /**
     * @return Collection<int, Calculations>
     */
    public function getCalculations(): Collection
    {
        return $this->calculations;
    }

    public function addCalculation(Calculations $calculation): static
    {
        if (!$this->calculations->contains($calculation)) {
            $this->calculations->add($calculation);
            $calculation->setVehicleType($this);
        }

        return $this;
    }

    public function removeCalculation(Calculations $calculation): static
    {
        if ($this->calculations->removeElement($calculation)) {
            // set the owning side to null (unless already changed)
            if ($calculation->getVehicleType() === $this) {
                $calculation->setVehicleType(null);
            }
        }

        return $this;
    }
}
