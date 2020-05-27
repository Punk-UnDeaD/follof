<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class Member.
 *
 * @ORM\Entity
 * @ORM\Table(name="billing_numbers")
 */
class Number implements AggregateRoot, \JsonSerializable
{
    use EventsTrait;

    public const FORMAT = '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/';
    /**
     * @ORM\Column(name="number", type="string", length=16)
     * @ORM\Id
     */
    private string $number;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="numbers")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Team $team = null;

    public function __construct(string $number)
    {
        Assert::regex($number, static::FORMAT, 'Wrong format.');
        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function __toString()
    {
        return $this->number;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team = null): self
    {
        if ($this->team === $team) {
            return $this;
        }
        if ($oldTeam = $this->team) {
            $this->team = null;
            $oldTeam->removeNumber($this);
            $this->recordEvent(...$oldTeam->releaseEvents());
        }
        if ($this->team = $team) {
            $team->addNumber($this);
            $this->recordEvent(...$team->releaseEvents());
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->number;
    }
}
