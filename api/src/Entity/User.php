<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\UserRepository;
use App\Resolver\UserMutationResolver;
use App\State\UserStateProcessor;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email a déjà été utilisée.')]
#[ApiResource(
    operations: [],
    // Display when reading the object
    normalizationContext: ['groups' => ['user:read']],
    // Available to write
    denormalizationContext: ['groups' => ['user:create', 'user:update']],
    graphQlOperations: [
        new QueryCollection(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Query(
            security: 'is_granted("ROLE_USER")'
        ),
        new Mutation(
            name: 'create',
            processor: UserStateProcessor::class,
        ),
        new Mutation(
            security: 'is_granted("ROLE_USER")',
            name: 'update',
            processor: UserStateProcessor::class,
        ),
        new Mutation(
            resolver: UserMutationResolver::class,
            args: [
                'email' => ['type' => 'String!', 'description' => 'Email of the user '],
                'plainPassword' => ['type' => 'String!', 'description' => 'Password of the user '],
            ],
            validate: false,
            write: false,
            name: 'loginCheck',
        ),
        new DeleteMutation(
            security: 'is_granted("ROLE_USER")',
            name: 'delete'
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use sluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read', 'recipe:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le prénom ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'recipe:read'])]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'recipe:read'])]
    private ?string $lastname = null;

    /**
     * @var string
     */
    #[Groups(['user:read'])]
    protected $slug;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Une adresse email est obligatoire.')]
    #[Assert\Length(
        max: 180,
        maxMessage: "L'email ne peut pas dépasser 180 caractères.",
    )]
    #[Assert\Email(message: "Cette adresse email n'est pas au bon format.")]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create'])]
    private ?string $plainPassword = null;

    #[Groups(['user:read'])]
    private ?string $token = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?array $roles = null;

    #[Groups(['user:create', 'user:update'])]
    private bool $isAdmin = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'adresse ne peut pas dépasser 255 caractères.",
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $address1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le complément d'adresse ne peut pas dépasser 255 caractères.",
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $address2 = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?int $zipCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La ville ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'recipe:read'])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le pays est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le pays ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le numéro de téléphone ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:update', 'recipe:read'])]
    private ?bool $isApiPicture = true;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?DateTimeImmutable $birthDate = null;

    #[Vich\UploadableField(mapping: 'user_picture_file', fileNameProperty: 'pictureName')]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    #[Assert\Image(
        allowLandscape: false,
        allowPortrait: false,
    )]
    private ?File $pictureFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l\'image ne peut pas dépasser 255 caractères.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'recipe:read'])]
    private ?string $pictureName = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?bool $isEnable = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?bool $acceptNewsletter = null;

    /**
     * @var DateTimeInterface
     */
    #[Groups(['user:read'])]
    protected $createdAt;

    /**
     * @var DateTimeInterface
     */
    #[Groups(['user:read'])]
    protected $updatedAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Recipe::class)]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * This method is used in Sonata Admin Bundle.
     */
    public function getFirstRoleAsString(): string
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        $role = reset($roles);

        return 'ROLE_ADMIN' === $role ? 'Administrateur' : 'Utilisateur';
    }

    /**
     * @param array<string>|null $roles
     *
     * @return $this
     */
    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @return $this
     */
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @return $this
     */
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthDate(): ?DateTimeImmutable
    {
        return $this->birthDate;
    }

    /**
     * @return $this
     */
    public function setBirthDate(?DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * @return $this
     */
    public function setAddress1(?string $address1): static
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @return $this
     */
    public function setAddress2(?string $address2): static
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    /**
     * @return $this
     */
    public function setZipCode(?int $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return $this
     */
    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return $this
     */
    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return $this
     */
    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIsApiPicture(): ?bool
    {
        return $this->isApiPicture;
    }

    public function setIsApiPicture(?bool $isApiPicture): void
    {
        $this->isApiPicture = $isApiPicture;
    }

    public function setPictureFile(File $pictureFile = null): void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureName(?string $pictureName): void
    {
        $this->pictureName = $pictureName;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function isIsEnable(): ?bool
    {
        return $this->isEnable;
    }

    /**
     * @return $this
     */
    public function setIsEnable(bool $isEnable): static
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    public function isIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function isAcceptNewsletter(): ?bool
    {
        return $this->acceptNewsletter;
    }

    public function setAcceptNewsletter(bool $acceptNewsletter): static
    {
        $this->acceptNewsletter = $acceptNewsletter;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['firstname', 'lastname'];
    }

    /**
     * @param array<string> $values
     */
    public function generateSlugValue(array $values): string
    {
        $stringValues = strtolower(implode(' ', $values));

        $slugger = new AsciiSlugger('fr');

        return $slugger->slug($stringValues);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
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
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getUser() === $this) {
                $recipe->setUser(null);
            }
        }

        return $this;
    }
}
