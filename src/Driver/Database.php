<?php
declare(strict_types=1);


namespace PantherHQ\Basket\Driver;


use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PantherHQ\Basket\Exception\WarehouseException;
use PantherHQ\Basket\Item\ItemId;
use PantherHQ\Basket\Item\ItemInterface;
use PantherHQ\Basket\Warehouse;
use PantherHQ\Basket\WarehouseInterface;
use Ramsey\Uuid\Uuid;

final class Database implements WarehouseInterface
{

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        Connection $connection
    )
    {
        $this->connection = $connection;
    }

    public function add(ItemInterface $item, Warehouse $warehouse): void
    {
        $this->connection->beginTransaction();
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select([
                'b.basket_id',
                'b.warehouse',
                'b.basket_content',
                'b.date_at',
            ])->from('basket','b')
                ->where('b.warehouse = :warehouse')
                ->setParameter('warehouse',$warehouse->warehouseId());

            $data = $qb->execute()->fetch();
            $items = [$item];
            if (is_array($data)){
                $qb->delete('basket','b')
                    ->where('b.basket_id = :basket_id')
                    ->setParameter('basket_id',$data['basket_id']);

                /** @var ItemInterface[] $items */
                $items = unserialize(base64_decode($data['basket_content'],true));
                foreach ($items as $key => $i){
                    if ($i->itemId()->id() === $item->itemId()->id()){
                        $items[$key] = $item;
                    }
                }
            }
            $qb->insert('basket')->values([
                'basket_id'=>':basket_id',
                'warehouse'=>':warehouse',
                'basket_content'=>':basket_content',
                'date_at'=>':date_at',
            ])
            ->setParameters([
                'basket_id'=>Uuid::getFactory()->uuid4(),
                'warehouse'=>$warehouse->warehouseId(),
                'basket_content'=>base64_encode(serialize($items)),
                'date_at'=>(new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ])->execute();

            $this->connection->commit();
        } catch (\Throwable $exception){
            $this->connection->rollBack();
            throw $exception;
        }

    }

    public function remove(ItemInterface $item, Warehouse $warehouse): void
    {
        // TODO: Implement remove() method.
    }

    public function getByItemId(ItemId $itemId, Warehouse $warehouse): ItemInterface
    {
        $this->connection->beginTransaction();
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select([
                'b.basket_id',
                'b.warehouse',
                'b.basket_content',
                'b.date_at',
            ])->from('basket','b')
                ->where('b.warehouse = :warehouse')
                ->setParameter('warehouse',$warehouse->warehouseId());

            $data = $qb->execute()->fetch();
            $this->connection->commit();
        } catch (\Throwable $exception){
            $this->connection->rollBack();
            throw $exception;
        }

        if (!is_array($data)) {
            throw new WarehouseException(sprintf('Warehouse %s does not exists',  $warehouse->warehouseId()));
        }

        $items = unserialize(base64_decode($data['basket_content'],true));
        $item = current(array_filter($items, function (ItemInterface $item) use ($itemId){
            return $item->itemId()->id() === $itemId->id();
        }));

        if (!$item instanceof ItemInterface){
            throw new WarehouseException(sprintf('Item with item id %s not found in warehouse %s', $itemId->id(), $warehouse->warehouseId()));
        }
        return $item;
    }

    public function findAll(Warehouse $warehouse): array
    {
        // TODO: Implement findAll() method.
    }

    public function destroy(Warehouse $warehouse): void
    {
        // TODO: Implement destroy() method.
    }


}