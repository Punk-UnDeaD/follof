<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="billing_voice_menus")
 * @ORM\HasLifecycleCallbacks
 */
class VoiceMenu implements HasNumber, AggregateRoot
{
    use EventsTrait;

    protected const DEFAULT_DATA = ['points' => [], 'label' => null, 'isInputAllowed' => false];

    public const POINT_KEY_FORMAT = '/\d{1,3}/';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="voiceMenus")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Team $team = null;

    /**
     * @ORM\Embedded(class="InternalNumber")
     */
    private ?InternalNumber $internalNumber = null;

    /**
     * @ORM\Column(type="json", options={"default" : "{}"})
     */
    private array $data = [];
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $file = null;
    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    public function __construct(Team $team)
    {
        $this->id = Id::next();
        $this->data = self::DEFAULT_DATA;
        $team->addVoiceMenu($this);
        $this->team = $team;
        $this->status = self::STATUS_BLOCKED;
    }

    public function getInternalNumber(): ?InternalNumber
    {
        return $this->internalNumber;
    }

    public function setInternalNumber(InternalNumber $internalNumber): self
    {
        if ($this->internalNumber && !$this->internalNumber->isSame($internalNumber)) {
            Assert::true(($this->getTeam())->checkInternalNumberFor($internalNumber, $this), 'Number can\'t be used.');
        }
        $this->internalNumber = $internalNumber;
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));

        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return InternalNumber[][]
     */
    public function getPoints(): array
    {
        return $this->data['points'];
    }

    /**
     * @return $this
     */
    public function addPoint(string $key, InternalNumber ...$numbers): self
    {
        Assert::Regex($key, self::POINT_KEY_FORMAT, 'Wrong key.');
        Assert::keyNotExists($this->data['points'], $key, 'Key already used.');
        $this->data['points'][$key] = $numbers;
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));

        return $this;
    }

    public function removePoint(string $key): self
    {
        Assert::Regex($key, self::POINT_KEY_FORMAT, 'Wrong key.');
        Assert::keyExists($this->data['points'], $key, 'Key already empty.');
        unset($this->data['points'][$key]);
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): VoiceMenu
    {
        Assert::notEmpty($file, 'Can\'t be empty.');
        $this->file = $file;
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));

        return $this;
    }

    public function activate(): self
    {
        Assert::notNull($this->file, 'File required.');
        Assert::notNull($this->internalNumber, 'Number required.');
        Assert::false($this->isActive(), 'Already activated.');
        $this->status = static::STATUS_ACTIVE;
        $this->recordEvent(new Event\VoiceMenuStatusUpdated($this->getId()->getValue()));

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function isActivated(): bool
    {
        return (bool)$this->file && (bool)$this->internalNumber;
    }

    public function block(): self
    {
        Assert::true($this->isActive(), 'Already blocked.');
        $this->status = static::STATUS_BLOCKED;

        $this->recordEvent(new Event\VoiceMenuStatusUpdated($this->getId()->getValue()));

        return $this;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->internalNumber->isNull()) {
            $this->internalNumber = null;
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        $this->data = array_merge(self::DEFAULT_DATA, $this->data);

        foreach ($this->data['points'] as $k => $points) {
            $this->data['points'][$k] = array_map(fn($point) => new InternalNumber($point), $points);
        }

    }

    public function getLabel(): ?string
    {
        return $this->data['label'];
    }

    public function setLabel(?string $label = null)
    {
        $this->data['label'] = $label;

        return $this;
    }

    public function isInputAllowed(): bool
    {
        return $this->data['isInputAllowed'];
    }

    public function setInputAllowance(bool $allow): self
    {
        $this->data['isInputAllowed'] = $allow;
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));

        return $this;
    }
}
