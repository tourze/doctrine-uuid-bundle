<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

#[WithMonologChannel(channel: 'doctrine_uuid')]
#[AsDoctrineListener(event: Events::prePersist)]
readonly class UuidListener implements EntityCheckerInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine-uuid.property-accessor')] private PropertyAccessor $propertyAccessor,
        private LoggerInterface $logger,
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

            if ([] !== $property->getAttributes(UuidV1Column::class)) {
                $this->processUuidProperty($entity, $property, 'v1');
            }

            if ([] !== $property->getAttributes(UuidV4Column::class)) {
                $this->processUuidProperty($entity, $property, 'v4');
            }
        }
    }

    private function processUuidProperty(object $entity, \ReflectionProperty $property, string $version): void
    {
        try {
            // 已经有值了，我们就跳过
            $v = $property->getValue($entity);
            if (null !== $v && '' !== $v && [] !== $v) {
                return;
            }
        } catch (\Throwable $exception) {
            // 忽略
        }

        $idValue = 'v1' === $version ? Uuid::v1()->toRfc4122() : Uuid::v4()->toRfc4122();
        $idValue = trim($idValue);

        $this->logger->debug("为{$property->getName()}分配UUID {$version} ID", [
            'id' => $idValue,
            'entity' => $entity,
        ]);
        $this->propertyAccessor->setValue($entity, $property->getName(), $idValue);
    }

    public function preUpdateEntity(ObjectManager $objectManager, object $entity, PreUpdateEventArgs $eventArgs): void
    {
        // 更新时，我们不特地去处理，因为有可能我们是需要去数据库特地清空，故意不给值的
    }
}
