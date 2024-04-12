<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\Table(name: 'clubs')]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[ORM\Index(columns: ['budget'], name: 'budget_index')]
class Club
{
    #[Assert\Type(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[ORM\Column(type: 'string', length: 3)]
    private string $shortname;

    #[Assert\Country]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 2)]
    private string $country;

    #[Assert\Type(type: 'float')]
    #[ORM\Column(type: 'float')]
    private float $budget;

    /**
     * @var ArrayCollection<Player>
     */
    #[Assert\Type(type: 'object')]
    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Player::class)]
    private Collection $players;

    /**
     * @var ArrayCollection<Coach>
     */
    #[Assert\Type(type: 'object')]
    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Coach::class)]
    private Collection $coaches;

    #[ORM\Column(name: 'created', type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $created;
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updated;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->coaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShortname(): string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return Collection<Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): void
    {
        $player->setClub($this);
        $this->players->add($player);
    }

    /**
     * @return Collection<Coach>
     */
    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): void
    {
        $coach->setClub($this);
        $this->coaches->add($coach);
    }
}
