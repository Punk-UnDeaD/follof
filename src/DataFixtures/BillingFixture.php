<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\Entity\Account\Team;
use App\Model\Billing\Entity\Account\VoiceMenu;
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

        (new VoiceMenu($team))
            ->setFile('menu.mp3')
            ->setLabel('Kremlin canteen')
            ->setInternalNumber(new InternalNumber('123'))
            ->addPoint('1', new InternalNumber('123-1'))
            ->addPoint('2', new InternalNumber('123-2'))
            ->addPoint('3', new InternalNumber('123-3'))
            ->addPoint('0', new InternalNumber('123'));

        $number = new Number('+7(988)123-45-67');
        $number->setTeam($team);
        $member->setNumber($number);
        $manager->persist($number);
        $number = new Number('+7(988)234-56-78');
        $number->setTeam($team);
        $manager->persist($number);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
