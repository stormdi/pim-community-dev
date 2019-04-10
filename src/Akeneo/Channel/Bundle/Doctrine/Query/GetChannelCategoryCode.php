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

namespace Akeneo\Channel\Bundle\Doctrine\Query;

use Akeneo\Channel\Component\Query\GetChannelCategoryCodeInterface;
use Doctrine\DBAL\Connection;

/**
 * @author Paul Chasle <paul.chasle@akeneo.com>
 */
final class GetChannelCategoryCode implements GetChannelCategoryCodeInterface
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(string $channelCode): ?string
    {
        $sql = <<<'SQL'
            SELECT category.code
            FROM pim_catalog_channel channel
            INNER JOIN pim_catalog_category category
                ON channel.category_id = category.id
            WHERE channel.code = :channel_code
SQL;

        $stmt = $this->connection->executeQuery(
            $sql,
            [
                'channel_code' => $channelCode,
            ],
            [
                'channel_code' => \PDO::PARAM_STR,
            ]
        );

        if (false === $categoryCode = $stmt->fetchColumn(0)) {
            return null;
        }

        return $categoryCode;
    }
}
