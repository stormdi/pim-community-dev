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

namespace Akeneo\Channel\Component\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Paul Chasle <paul.chasle@akeneo.com>
 */
final class ChannelCategoryHasBeenUpdated extends Event
{
    public const EVENT_NAME = 'CHANNEL_CATEGORY_HAS_BEEN_UPDATED';

    protected $channelCode;

    protected $categoryCode;

    public function __construct(string $channelCode, string $categoryCode)
    {
        $this->channelCode = $channelCode;
        $this->categoryCode = $categoryCode;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }

    public function getCategoryCode(): string
    {
        return $this->categoryCode;
    }
}
