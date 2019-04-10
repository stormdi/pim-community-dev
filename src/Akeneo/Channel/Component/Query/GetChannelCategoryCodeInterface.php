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

namespace Akeneo\Channel\Component\Query;

/**
 * @author Paul Chasle <paul.chasle@akeneo.com>
 */
interface GetChannelCategoryCodeInterface
{
    public function execute(string $channelCode): ?string;
}
