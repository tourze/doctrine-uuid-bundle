# Doctrine UUID Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![PHP版本](https://img.shields.io/packagist/php-v/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![许可证](https://img.shields.io/packagist/l/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![构建状态](https://img.shields.io/travis/tourze/doctrine-uuid-bundle/master.svg?style=flat-square)]
(https://travis-ci.org/tourze/doctrine-uuid-bundle)
[![质量评分](https://img.shields.io/scrutinizer/g/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/tourze/doctrine-uuid-bundle)
[![总下载量](https://img.shields.io/packagist/dt/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![代码覆盖率](https://img.shields.io/scrutinizer/coverage/g/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/tourze/doctrine-uuid-bundle)

一个基于 PHP 8 Attribute 的 Symfony Bundle，实现 Doctrine 实体属性的自动 UUID（v1/v4）赋值。

## 安装方法

```bash
composer require tourze/doctrine-uuid-bundle
```

## 环境要求

- PHP 8.1 及以上
- Symfony 6.4 及以上
- Doctrine Bundle 2.13 及以上

## 功能特性

- 实体属性自动生成 UUID v1/v4
- 零配置，开箱即用
- 基于 PHP 8 Attribute，简单灵活
- 与 Doctrine 事件系统无缝集成
- 内置日志，便于调试
- 支持自定义 UUID 生成策略（可扩展）
- 支持可空 UUID 字段
- 自动适配数据库 schema 更新
- 兼容 Symfony 6.4+ 和 Doctrine 2.13+

## 快速开始

在你的实体属性上添加 `UuidV1Column` 或 `UuidV4Column` Attribute：

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

实体在持久化时会自动生成 UUID。

## 配置说明

本 Bundle 采用零配置设计。如需自定义行为，可以：

- 通过扩展基础 Attribute 创建自定义 UUID 属性
- 重写事件监听器服务实现自定义生成逻辑
- 在 Symfony 配置中设置日志级别

## 进阶用法

你可以通过扩展 Attribute 或事件监听器自定义 UUID 生成逻辑。

## 安全考虑

- UUID v1 包含 MAC 地址和时间戳信息，可能被视为敏感数据
- UUID v4 采用加密随机生成，不包含可识别信息
- 请根据安全要求选择合适的 UUID 版本
- 对于面向公众的标识符，建议使用 UUID v4

## 常见问题

1. **未生成 UUID**：请确保实体属性加了正确的 Attribute，且 bundle 已在 `config/bundles.php`
   注册。
2. **数据库 schema 问题**：请运行 `php bin/console doctrine:schema:update --force`
   更新数据库结构。
3. **性能建议**：UUID v1 基于时间，适合用于数据库索引，UUID v4 随机性更强。

## 文档说明

- [API 文档](docs/)
- 配置基本自动完成。如需高级扩展，可自定义事件订阅器。

## 贡献指南

- 欢迎提交 Issue 和 PR
- 遵循 PSR 代码风格
- 提交 PR 前请确保测试通过
- Fork 本仓库，创建分支，推送并提交 PR

## 版权和许可

- 协议：MIT
- 作者：Tourze

## 更新日志

详见 [CHANGELOG](CHANGELOG.md)

---

### 工作流程图

详见 `Mermaid.md` 文件。
