<?php

namespace App\Entity;

use App\Repository\AssociationFeesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\Expr\Base;

#[ORM\Entity(repositoryClass: AssociationFeesRepository::class)]
class AssociationFees extends BaseEntity
{

    #[ORM\Column]
    private ?float $min_price = null;

    #[ORM\Column(nullable: true)]
    private ?float $max_price = null;
    #[ORM\Column]
    private ?float $association_fee = null;


    public function getMinPrice(): ?float
    {
        return $this->min_price;
    }

    public function setMinPrice(float $min_price): static
    {
        $this->min_price = $min_price;

        return $this;
    }

    public function getMaxPrice(): ?float
    {
        return $this->max_price;
    }

    public function setMaxPrice(float $max_price): static
    {
        $this->max_price = $max_price;

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


}
