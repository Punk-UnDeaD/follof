<?php

namespace App\EntityListener;

use App\Model\Billing\Entity\Account\Member;
use App\Model\User\Entity\User\User;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class EntityListener
{
    private TagAwareCacheInterface $cachePool;

    public function __construct(TagAwareCacheInterface $myCachePool)
    {
        $this->cachePool = $myCachePool;
    }

    /**
     * @param Member|User $entity
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function postUpdate($entity)
    {
        $this->cachePool->invalidateTags([$entity->getId()->getValue()]);
    }
}
