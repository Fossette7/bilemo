<?php

namespace App\Entity;

use App\Repository\MobileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MobileRepository::class)]
class Mobile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $brandname = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrandname(): ?string
    {
        return $this->brandname;
    }

    public function setBrandname(string $brandname): self
    {
        $this->brandname = $brandname;

        return $this;
    }
}
