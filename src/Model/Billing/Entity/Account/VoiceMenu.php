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
    private InternalNumber $internalNumber;

    /**
     * @ORM\Column(type="billing_voice_menu_data")
     */
    private array $data;
    /**
     * @ORM\Column(type="string")
     */
    private string $file;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    public function __construct(Team $team, InternalNumber $internalNumber, string $file)
    {
        $this->id = Id::next();
        $this->data = VoiceMenuDataType::default();
        Assert::true($team->checkInternalNumberFor($internalNumber, $this), 'Number can\'t be used.');
        $this->internalNumber = $internalNumber;
        $team->addVoiceMenu($this);
        $this->team = $team;
        $this->status = self::STATUS_BLOCKED;
        $this->setFile($file);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getInternalNumber(): InternalNumber
    {
        return $this->internalNumber;
    }

    public function setInternalNumber(InternalNumber $internalNumber): self
    {
        if (!$this->internalNumber->isSame($internalNumber)) {
            Assert::true(
                ($team ?? $this->getTeam())->checkInternalNumberFor($internalNumber, $this),
                'Number can\'t be used.'
            );
            $this->internalNumber = $internalNumber;
        }

        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @return InternalNumber[]
     */
    public function getPoints(): array
    {
        return $this->data['points'];
    }

    public function addPoint(string $key, InternalNumber $number): self
    {
        Assert::Regex($key, self::POINT_KEY_FORMAT, 'Wrong key.');
        Assert::keyNotExists($this->data['points'], $key, 'Key already used.');
        $this->data['points'][$key] = $number;

        return $this;
    }

    public function removePoint(string $key): self
    {
        Assert::Regex($key, self::POINT_KEY_FORMAT, 'Wrong key.');
        Assert::keyExists($this->data['points'], $key, 'Key already empty.');
        unset($this->data['points'][$key]);

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): VoiceMenu
    {
        Assert::notEmpty($file, 'Can\'t be empty.');
        $this->file = $file;

        return $this;
    }

    public function activate(): self
    {
        Assert::false($this->isActive(), 'Already activated.');
        $this->status = static::STATUS_ACTIVE;

//        $this->recordEvent(new Event\MemberSipPoolUpdated($this->getId()->getValue()));

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function block(): self
    {
        Assert::true($this->isActive(), 'Already blocked.');
        $this->status = static::STATUS_BLOCKED;

//        $this->recordEvent(new Event\MemberSipPoolUpdated($this->getId()->getValue()));

        return $this;
    }
}