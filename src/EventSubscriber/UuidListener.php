<?php

namespace Tourze\DoctrineUuidBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

#[AsDoctrineListener(event: Events::prePersist)]
class UuidListener implements EntityCheckerInterface
{
    public function __construct(
        private readonly PropertyAccessor $propertyAccessor,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->prePersistEntity($args->getObjectManager(), $args->getObject());
    }

    public function prePersistEntity(ObjectManager $objectManager, object $entity): void
    {
        $reflection = $objectManager->getClassMetadata($entity::class)->getReflectionClass();
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            // 如果字段不可以写入，直接跳过即可
            if (!$this->propertyAccessor->isWritable($entity, $property->getName())) {
                continue;
            }

            if (!empty($property->getAttributes(UuidV1Column::class))) {
                try {
                    // 已经有值了，我们就跳过
                    $v = $property->getValue($entity);
                    if (!empty($v)) {
                        continue;
                    }
                } catch (\Throwable $exception) {
                    // 忽略
                }

                $idValue = Uuid::v1()->toRfc4122();
                $idValue = trim($idValue);

                $this->logger?->debug("为{$property->getName()}分配UUID v1 ID", [
                    'id' => $idValue,
                    'entity' => $entity,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $idValue);
            }

            if (!empty($property->getAttributes(UuidV4Column::class))) {
                try {
                    // 已经有值了，我们就跳过
                    $v = $property->getValue($entity);
                    if (!empty($v)) {
                        continue;
                    }
                } catch (\Throwable $exception) {
                    // 忽略
                }

                $idValue = Uuid::v4()->toRfc4122();
                $idValue = trim($idValue);

                $this->logger?->debug("为{$property->getName()}分配UUID v4 ID", [
                    'id' => $idValue,
                    'entity' => $entity,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $idValue);
            }
        }
    }

    public function preUpdateEntity(ObjectManager $objectManager, object $entity, PreUpdateEventArgs $eventArgs): void
    {
        // 更新时，我们不特地去处理，因为有可能我们是需要去数据库特地清空，故意不给值的
    }
}
