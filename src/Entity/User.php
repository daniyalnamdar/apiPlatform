<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ["security" => "is_granted('IS_AUTHENTICATED_ANONYMOUSLY') == false"]
        ],
    itemOperations: [
        'get',
        'put' => ["security" => "is_granted('ROLE_USER') and object == user"],
        'delete' => ["security" => "is_granted('ROLE_ADMIN')"]
        ],
    attributes: ["security" => "is_granted('ROLE_USER')"],
    denormalizationContext: ['groups'=>'user:write'],
    normalizationContext: ['groups'=>'user:read']
)]
#[ApiFilter(PropertyFilter::class)]
#[UniqueEntity('username')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[SerializedName('password')]
    #[Groups(['user:write'])]
    private ?string $plainPassword;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['user:read', 'user:write', 'cheese_listing:item:get', 'cheese_listing:write'])]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: CheeseListing::class, cascade: ['Persist'], orphanRemoval: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Valid]
    private Collection $cheeseListings;

    public function __construct()
    {
        $this->cheeseListings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, CheeseListing>
     */
    public function getCheeseListings(): Collection
    {
        return $this->cheeseListings;
    }

    public function addCheeseListing(CheeseListing $cheeseListing): self
    {
        if (!$this->cheeseListings->contains($cheeseListing)) {
            $this->cheeseListings[] = $cheeseListing;
            $cheeseListing->setOwner($this);
        }

        return $this;
    }

    public function removeCheeseListing(CheeseListing $cheeseListing): self
    {
        if ($this->cheeseListings->removeElement($cheeseListing)) {
            // set the owning side to null (unless already changed)
            if ($cheeseListing->getOwner() === $this) {
                $cheeseListing->setOwner(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }


    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

}
