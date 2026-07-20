<?php

declare(strict_types=1);

namespace App\Application\Promotions\Actions;

use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Models\Promotion;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ListPromotionsAction
{
    public function __construct(
        private readonly PromotionsRepositoryInterface $promotions,
    ) {}

    /**
     * Without per_page: full list. With per_page: keyset page.
     *
     * @return array{promotions: Collection<int, Promotion>, next_cursor: string|null}
     */
    public function execute(?string $cursor = null, ?int $perPage = null): array
    {
        if ($perPage === null) {
            return [
                'promotions' => $this->promotions->list(),
                'next_cursor' => null,
            ];
        }

        try {
            $page = $this->promotions->listPage($cursor, $perPage);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return [
            'promotions' => $page['items'],
            'next_cursor' => $page['next_cursor'],
        ];
    }
}
