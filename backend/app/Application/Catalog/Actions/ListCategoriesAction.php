<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Collection;

final class ListCategoriesAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    /**
     * @return Collection<int, Category>
     */
    public function execute(): Collection
    {
        return $this->catalog->listCategories();
    }
}
