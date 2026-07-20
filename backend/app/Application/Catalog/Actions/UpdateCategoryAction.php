<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Category;

final class UpdateCategoryAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    /**
     * @param array{name?: string, is_active?: bool} $data
     */
    public function execute(int $categoryId, array $data): Category
    {
        $category = $this->catalog->findCategoryById($categoryId);

        if ($category === null) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNotFound);
        }

        if (isset($data['name']) && $this->catalog->categoryNameExists($data['name'], $category->id)) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNameDuplicate);
        }

        return $this->catalog->updateCategory($category, $data);
    }
}
