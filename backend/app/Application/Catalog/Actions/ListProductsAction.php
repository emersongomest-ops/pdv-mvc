<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ListProductsAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    /**
     * Without per_page: full list (inventory pickers). With per_page: keyset page.
     *
     * @return array{products: Collection<int, Product>, next_cursor: string|null}
     */
    public function execute(
        ?int $categoryId = null,
        ?bool $isActive = null,
        ?string $search = null,
        ?string $cursor = null,
        ?int $perPage = null,
    ): array {
        if ($perPage === null) {
            return [
                'products' => $this->catalog->listProducts($categoryId, $isActive, $search),
                'next_cursor' => null,
            ];
        }

        try {
            $page = $this->catalog->listProductsPage(
                $categoryId,
                $isActive,
                $search,
                $cursor,
                $perPage,
            );
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return [
            'products' => $page['items'],
            'next_cursor' => $page['next_cursor'],
        ];
    }
}
