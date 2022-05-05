<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $price;

/*    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPromotion::class)]
    private $productPromotions;

    public function __construct()
    {
        $this->productPromotions = new ArrayCollection();
    }*/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ProductPromotion>
     */
    /*
    public function getProductPromotions(): Collection
    {
        return $this->productPromotions;
    }

    public function addPromotion(ProductPromotion $promotion): self
    {
        if (!$this->productPromotions->contains($promotion)) {
            $this->productPromotions[] = $promotion;
            $promotion->setProduct($this);
        }

        return $this;
    }

    public function removePromotion(ProductPromotion $promotion): self
    {
        if ($this->productPromotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getProduct() === $this) {
                $promotion->setProduct(null);
            }
        }

        return $this;
    }
    */
}
