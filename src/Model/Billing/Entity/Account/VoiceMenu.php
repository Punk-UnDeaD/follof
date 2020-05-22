<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\Billing\Entity\Account\DataType\Id;
use App\Model\Billing\Entity\Account\Field\DataTrait;
use App\Model\Billing\Entity\Account\Field\IdTrait;
use App\Model\Billing\Entity\Account\Field\InternalNumberTrait;
use App\Model\Billing\Entity\Account\Field\LabelTrait;
use App\Model\Billing\Entity\Account\Field\MenuPointsTrait;
use App\Model\Billing\Entity\Account\Field\StatusTrait;
use App\Model\EventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="billing_voice_menus")
 * @ORM\HasLifecycleCallbacks
 */
class VoiceMenu implements AggregateRoot
{
    use IdTrait;

    use EventsTrait;
    use InternalNumberTrait;
    use DataTrait,
        LabelTrait,
        MenuPointsTrait,
        StatusTrait {
        DataTrait::checkData as protected baseCheckData;
        MenuPointsTrait::checkData as protected pointsCheckData;
    }

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="voiceMenus")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Team $team = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $file = null;

    public function __construct(Team $team)
    {
        $this->id = Id::next();
        $this->data = self::defaultData();
        $team->addVoiceMenu($this);
        $this->team = $team;
        $this->status = self::STATUS_BLOCKED;
    }

    public static function defaultData(): array
    {
        return LabelTrait::defaultData()
            + MenuPointsTrait::defaultData()
            + ['isInputAllowed' => false];
    }

    /**
     * @return Team
     */
    public function getTeam(): ?Team
    {
        return $this->team;
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

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        $this->baseCheckData();
        $this->pointsCheckData();
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

    protected function onUpdateInternalNumber()
    {
        $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));
    }

    protected function onUpdateMenuPoints(): self
    {
        return $this->recordEvent(new Event\VoiceMenuDataUpdated($this->getId()->getValue()));
    }

    public function isActivated(): bool
    {
        return (bool) $this->file && (bool) $this->internalNumber;
    }

    protected function onUpdateStatus(): self
    {
        return $this->recordEvent(new Event\VoiceMenuStatusUpdated($this->getId()->getValue()));
    }
}
