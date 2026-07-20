<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateProductAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    /**
     * @param array{
     *     sku?: string,
     *     name?: string,
     *     base_price?: int,
     *     is_active?: bool,
     *     category_id?: int|null
     * } $data
     */
    public function execute(User $actor, int $productId, array $data): Product
    {
        $product = $this->catalog->findProductById($productId);

        if ($product === null) {
            throw new CatalogDomainException(ErrorCode::CatProductNotFound);
        }

        if (isset($data['sku']) && $this->catalog->skuExists($data['sku'], $product->id)) {
            throw new CatalogDomainException(ErrorCode::CatSkuDuplicate);
        }

        if (array_key_exists('category_id', $data) && $data['category_id'] !== null) {
            $this->assertCategoryExists((int) $data['category_id']);
        }

        $oldPrice = (int) $product->base_price;
        $priceChanging = array_key_exists('base_price', $data)
            && (int) $data['base_price'] !== $oldPrice;

        return DB::transaction(function () use ($actor, $product, $data, $oldPrice, $priceChanging): Product {
            $updated = $this->catalog->updateProduct($product, $data);

            if ($priceChanging) {
                $this->auditLogs->append(new AuditLogEntry(
                    action: AuditAction::ProductPriceChanged,
                    actorUserId: (int) $actor->id,
                    subjectType: 'product',
                    subjectId: (int) $updated->id,
                    storeId: null,
                    oldValues: ['base_price' => $oldPrice],
                    newValues: ['base_price' => (int) $updated->base_price],
                    metadata: [
                        'sku' => $updated->sku,
                        'name' => $updated->name,
                    ],
                ));
            }

            return $updated;
        });
    }

    private function assertCategoryExists(int $categoryId): void
    {
        if ($this->catalog->findCategoryById($categoryId) === null) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNotFound);
        }
    }
}
