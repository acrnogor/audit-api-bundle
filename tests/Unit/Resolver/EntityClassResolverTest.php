<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Unit\Resolver;

use Acrnogor\AuditApiBundle\Exception\EntityClassNotFoundException;
use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use PHPUnit\Framework\TestCase;

final class EntityClassResolverTest extends TestCase
{
    private EntityClassResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new EntityClassResolver([
            'App\Entity\User' => 'user',
            'App\Entity\FeatureFlag' => 'feature-flag',
        ]);
    }

    public function testResolvesShortnameToClass(): void
    {
        self::assertSame('App\Entity\User', $this->resolver->resolve('user'));
        self::assertSame('App\Entity\FeatureFlag', $this->resolver->resolve('feature-flag'));
    }

    public function testThrowsForUnknownShortname(): void
    {
        $this->expectException(EntityClassNotFoundException::class);

        $this->resolver->resolve('unknown');
    }

    public function testSupports(): void
    {
        self::assertTrue($this->resolver->supports('user'));
        self::assertFalse($this->resolver->supports('unknown'));
    }

    public function testRejectsDuplicateShortnames(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new EntityClassResolver([
            'App\Entity\User' => 'user',
            'App\Entity\Admin' => 'user',
        ]);
    }
}
