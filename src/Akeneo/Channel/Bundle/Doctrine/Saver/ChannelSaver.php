<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2019 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Channel\Bundle\Doctrine\Saver;

use Akeneo\Channel\Component\Event\ChannelCategoryHasBeenUpdated;
use Akeneo\Channel\Component\Model\ChannelInterface;
use Akeneo\Channel\Component\Query\GetChannelCategoryCodeInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\BulkSaverInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paul Chasle <paul.chasle@akeneo.com>
 */
final class ChannelSaver implements SaverInterface, BulkSaverInterface
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $savedClass;

    /** @var GetChannelCategoryCodeInterface */
    private $getChannelCategoryCode;

    public function __construct(
        ObjectManager $objectManager,
        EventDispatcherInterface $eventDispatcher,
        $savedClass,
        GetChannelCategoryCodeInterface $getChannelCategoryCode

    ) {
        $this->objectManager = $objectManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->savedClass = $savedClass;
        $this->getChannelCategoryCode = $getChannelCategoryCode;
    }

    /**
     * {@inheritdoc}
     *
     * @param ChannelInterface $channel
     */
    public function save($channel, array $options = [])
    {
        $this->validateObject($channel);

        $options['unitary'] = true;
        $options['is_new'] = null === $channel->getId();

        $channelCategoryUpdated = $this->isChannelCategoryUpdated(
            $channel->getCode(),
            $channel->getCategory()->getCode()
        );

        $this->eventDispatcher->dispatch(
            StorageEvents::PRE_SAVE,
            new GenericEvent($channel, $options)
        );

        $this->objectManager->persist($channel);
        $this->objectManager->flush();

        $this->eventDispatcher->dispatch(
            StorageEvents::POST_SAVE,
            new GenericEvent($channel, $options)
        );

        if (true === $channelCategoryUpdated) {
            $this->eventDispatcher->dispatch(
                ChannelCategoryHasBeenUpdated::EVENT_NAME,
                new ChannelCategoryHasBeenUpdated(
                    $channel->getCode(),
                    $channel->getCategory()->getCode()
                )
            );
        }
    }

    private function isChannelCategoryUpdated(string $channelCode, string $newCategoryCode): bool
    {
        $currentCategoryCode = $this->getChannelCategoryCode->execute($channelCode);
        if (null === $currentCategoryCode) {
            return false;
        }

        return $currentCategoryCode !== $newCategoryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(array $objects, array $options = [])
    {
        if (empty($objects)) {
            return;
        }

        $options['unitary'] = false;

        $this->eventDispatcher->dispatch(StorageEvents::PRE_SAVE_ALL, new GenericEvent($objects, $options));

        $areObjectsNew = array_map(
            function ($object) {
                return null === $object->getId();
            },
            $objects
        );

        foreach ($objects as $i => $object) {
            $this->validateObject($object);

            $this->eventDispatcher->dispatch(
                StorageEvents::PRE_SAVE,
                new GenericEvent($object, array_merge($options, ['is_new' => $areObjectsNew[$i]]))
            );

            $this->objectManager->persist($object);
        }

        $this->objectManager->flush();

        foreach ($objects as $i => $object) {
            $this->eventDispatcher->dispatch(
                StorageEvents::POST_SAVE,
                new GenericEvent($object, array_merge($options, ['is_new' => $areObjectsNew[$i]]))
            );
        }

        $this->eventDispatcher->dispatch(StorageEvents::POST_SAVE_ALL, new GenericEvent($objects, $options));
    }

    protected function validateObject($object)
    {
        if (!$object instanceof $this->savedClass) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a "%s", "%s" provided.',
                    $this->savedClass,
                    ClassUtils::getClass($object)
                )
            );
        }
    }
}
