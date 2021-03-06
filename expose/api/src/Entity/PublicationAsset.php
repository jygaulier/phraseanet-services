<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique_url", columns={"publication_id", "slug"})})
 * @ApiResource(
 *     iri="http://alchemy.fr/PublicationAsset",
 *     itemOperations={
 *         "get",
 *         "put"={
 *              "security"="is_granted('publication:publish')"
 *         }
 *     },
 *     collectionOperations={
 *         "post"={
 *              "security"="is_granted('publication:publish')"
 *         }
 *     }
 * )
 */
class PublicationAsset
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"publication:read"})
     *
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $id;

    /**
     * @var Publication
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "$ref"="#/definitions/Publication",
     *         }
     *     }
     * )
     * @ORM\ManyToOne(targetEntity="Publication", inversedBy="assets")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $publication;

    /**
     * @var Asset
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "$ref"="#/definitions/Asset",
     *         }
     *     }
     * )
     * @Groups({"publication:read"})
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $asset;

    /**
     * Direct access to asset.
     *
     * @ApiProperty()
     * @Groups({"publication:read", "asset:read"})
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @ApiProperty()
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->id = Uuid::uuid4();
    }

    public function getId()
    {
        return $this->id->__toString();
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(Publication $publication): void
    {
        $this->publication = $publication;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(Asset $asset): void
    {
        $this->asset = $asset;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
