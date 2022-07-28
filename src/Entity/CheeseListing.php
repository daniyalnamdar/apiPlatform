<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CheeseListingRepository::class)]
#[
    ApiResource(

        collectionOperations: ['get', 'post'],
        itemOperations: [
            'get' => [
                'normalization_context' => ['groups' => ['cheese_listing:read', 'cheese_listing:item:get']]
                ],
            'put', 'delete'],
        shortName: 'cheeses',
        attributes: ['pagination_items_per_page'=> 7],
        denormalizationContext: ['groups'=>'cheese_listing:write'],
        formats: ['jsonld', 'json', 'html', 'jsonhal', 'csv' => ['text/csv']],
        normalizationContext: ['groups'=>'cheese_listing:read']

    )

]
#[ApiFilter(
    BooleanFilter::class,
    properties: ['isPublished']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title' => SearchFilterInterface::STRATEGY_PARTIAL,
        'description' => SearchFilterInterface::STRATEGY_EXACT,
        'owner' => SearchFilterInterface::STRATEGY_EXACT,
        'owner.username' => SearchFilterInterface::STRATEGY_PARTIAL
    ]
)]
#[ApiFilter(
    RangeFilter::class,
    properties: ['price']
)]
#[ApiFilter(PropertyFilter::class)]
class CheeseListing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        maxMessage: 'Describe Your cheese in 50 char or less'
    )]
    #[Groups(['cheese_listing:read', 'cheese_listing:write', 'user:read', 'user:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['cheese_listing:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['cheese_listing:read', 'cheese_listing:write', 'user:read', 'user:write'])]
    #[Assert\NotBlank]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    #[Groups(['cheese_listing:read'])]
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'cheeseListings')]
    #[Groups(['cheese_listing:read', 'cheese_listing:write'])]
    #[Assert\Valid]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function __construct(string $title = null)
    {
        $this->title = $title;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(['cheese_listing:read'])]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40){
            return $this->description;
        }
        return substr($this->description, 0, 40).'...';
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[SerializedName('description')]
    #[Groups(['cheese_listing:write', 'user:write'])]
    public function setTextDescription(string $description): self
    {
        $this->description =nl2br($description);

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['cheese_listing:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
