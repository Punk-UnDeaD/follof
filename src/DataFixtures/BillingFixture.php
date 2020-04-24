<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\Entity\Account\Team;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BillingFixture extends Fixture implements DependentFixtureInterface
{
    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference(UserFixture::REFERENCE_USER);
        $team = new Team($user, 'kreml.in');
        $member = new Member($team);
        $member->setCredentials('putin', $this->hasher->hash('kadirov'));
        $manager->persist($team);
        new SipAccount($member, 'putin', 'kadirov');

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
