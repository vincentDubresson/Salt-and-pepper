<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\CostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CostRepository::class)]
#[ORM\Table(name: '`cost`')]
#[ApiResource(
    operations: [],
    // Display when reading the object
    normalizationContext: ['groups' => ['cost:read']],
    graphQlOperations: [
        new QueryCollection(),
        new Query(),
    ]
)]
class Cost implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['cost:read', 'recipe:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le coût ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['cost:read', 'recipe:read'])]
    private ?string $label = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive(
        message: 'Le tri doit être positif.'
    )]
    #[Groups(['cost:read', 'recipe:read'])]
    private ?int $sort = null;

    #[ORM\OneToMany(mappedBy: 'cost', targetEntity: Recipe::class)]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getLabel();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->setCost($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getCost() === $this) {
                $recipe->setCost(null);
            }
        }

        return $this;
    }
}
