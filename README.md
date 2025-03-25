# Doctrine UUID Bundle

[English](#english) | [中文](#中文)

## English

A Symfony bundle that provides automatic UUID generation for Doctrine entities using PHP 8 attributes.

### Features

- Automatic UUID v1 and v4 generation for entity properties
- Uses PHP 8 attributes for configuration
- Integrates with Doctrine's event system
- Supports logging for debugging
- No configuration required
- Custom UUID generation strategies
- Support for nullable UUID fields
- Automatic database schema updates

### Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine Bundle 2.13 or higher

### Installation

```bash
composer require tourze/doctrine-uuid-bundle
```

### Usage

Add the `UuidV1Column` or `UuidV4Column` attribute to your entity properties:

```php
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class YourEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[UuidV1Column]
    private string $uuidV1;

    #[ORM\Column(type: 'uuid')]
    #[UuidV4Column]
    private ?string $uuidV4 = null;
}
```

The UUID will be automatically generated when the entity is persisted.

### Advanced Configuration

You can customize the UUID generation behavior using additional attributes:

```php
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidOptions;

#[ORM\Entity]
class YourEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[UuidV1Column]
    #[UuidOptions(unique: true, nullable: false)]
    private string $uuidV1;
}
```

### Common Issues

1. **UUID not being generated**: Ensure your entity is properly configured with Doctrine annotations and the bundle is registered in your `config/bundles.php`.

2. **Database schema issues**: Run `php bin/console doctrine:schema:update --force` to update your database schema.

3. **Performance considerations**: UUID v1 is time-based and may be more suitable for database indexing than UUID v4.

### Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 中文

一个为 Doctrine 实体提供自动 UUID 生成的 Symfony Bundle，使用 PHP 8 属性进行配置。

### 特性

- 为实体属性自动生成 UUID v1 和 v4
- 使用 PHP 8 属性进行配置
- 与 Doctrine 事件系统集成
- 支持调试日志
- 无需额外配置
- 自定义 UUID 生成策略
- 支持可空 UUID 字段
- 自动数据库架构更新

### 要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine Bundle 2.13 或更高版本

### 安装

```bash
composer require tourze/doctrine-uuid-bundle
```

### 使用方法

在实体属性上添加 `UuidV1Column` 或 `UuidV4Column` 属性：

```php
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class YourEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[UuidV1Column]
    private string $uuidV1;

    #[ORM\Column(type: 'uuid')]
    #[UuidV4Column]
    private ?string $uuidV4 = null;
}
```

当实体被持久化时，UUID 将自动生成。

### 高级配置

你可以使用额外的属性来自定义 UUID 生成行为：

```php
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidOptions;

#[ORM\Entity]
class YourEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[UuidV1Column]
    #[UuidOptions(unique: true, nullable: false)]
    private string $uuidV1;
}
```

### 常见问题

1. **UUID 未生成**：确保你的实体正确配置了 Doctrine 注解，并且在 `config/bundles.php` 中注册了该 bundle。

2. **数据库架构问题**：运行 `php bin/console doctrine:schema:update --force` 来更新数据库架构。

3. **性能考虑**：UUID v1 是基于时间的，可能比 UUID v4 更适合数据库索引。

### 贡献

欢迎贡献！请随时提交 Pull Request。

1. Fork 仓库
2. 创建特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 打开 Pull Request

### 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。
