# Doctrine UUID Bundle

[English](README.md) | [中文](README.zh-CN.md)
[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![License](https://img.shields.io/packagist/l/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![Build Status](https://img.shields.io/travis/tourze/doctrine-uuid-bundle/master.svg?style=flat-square)]
(https://travis-ci.org/tourze/doctrine-uuid-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/tourze/doctrine-uuid-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/doctrine-uuid-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tourze/doctrine-uuid-bundle.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/tourze/doctrine-uuid-bundle)

A Symfony bundle for automatic UUID (v1/v4) assignment in Doctrine entities using PHP 8 attributes.

## Installation

```bash
composer require tourze/doctrine-uuid-bundle
```

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher

## Features

- Automatic UUID v1 and v4 assignment for entity properties
- Zero configuration, works out of the box
- Attribute-driven and easy to use
- Seamless integration with Doctrine event system
- Built-in logging for debugging
- Extensible: custom UUID generation strategies supported
- Nullable UUID fields supported
- Automatic database schema updates
- Compatible with Symfony 6.4+ and Doctrine 2.13+

## Quick Start

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

UUIDs will be automatically generated when the entity is persisted.

## Configuration

The bundle works with zero configuration. If you need to customize the behavior, you can:

- Create custom UUID attributes by extending the base attributes
- Override the event listener service to implement custom generation logic
- Configure logging levels in your Symfony configuration

## Advanced Usage

You may extend the Attribute or event listener to implement custom UUID generation logic.

## Security

- UUID v1 contains MAC address and timestamp information, which may be considered sensitive
- UUID v4 is cryptographically random and doesn't contain identifiable information
- Choose the appropriate UUID version based on your security requirements
- Consider using UUID v4 for public-facing identifiers

## Common Issues

1. **UUID not being generated**: Ensure your entity uses the correct attributes and the bundle is registered
   in `config/bundles.php`.
2. **Database schema issues**: Run `php bin/console doctrine:schema:update --force` to update your
   database schema.
3. **Performance considerations**: UUID v1 is time-based and may be more suitable for database
   indexing than UUID v4.

## Documentation

- [API Docs](docs/)
- Most configuration is automatic. For advanced extension, you may customize the event
  subscriber.

## Contributing

- Feel free to submit Issues and PRs
- Follow PSR code style
- Please ensure tests pass before submitting PRs
- Fork the repo, create a branch, push and submit a PR

## License

- License: MIT
- Author: Tourze

## Changelog

See [CHANGELOG](CHANGELOG.md)

---

### Workflow Diagram

See `Mermaid.md` for a visual workflow.
