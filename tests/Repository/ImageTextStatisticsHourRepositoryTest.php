<?php

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsHourRepository;

class ImageTextStatisticsHourRepositoryTest extends TestCase
{
    /**
     * 测试仓库构造函数是否接受正确的实体类类名
     */
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);

        // 测试能否正确实例化仓库
        $repository = new ImageTextStatisticsHourRepository($registry);

        $this->assertInstanceOf(ImageTextStatisticsHourRepository::class, $repository);
    }

    /**
     * 测试仓库类是否与正确的实体类关联
     */
    public function testRepositoryEntityClassAssociation(): void
    {
        // 验证实体类名被正确传递给父类构造函数
        $constructorBody = $this->getConstructorBody(ImageTextStatisticsHourRepository::class);
        $this->assertStringContainsString('ImageTextStatisticsHour::class', $constructorBody, '构造函数应传递 ImageTextStatisticsHour::class 给父类');
    }

    /**
     * 获取类构造函数的代码内容
     */
    private function getConstructorBody(string $className): string
    {
        $reflection = new \ReflectionClass($className);
        if (!$reflection->hasMethod('__construct')) {
            return '';
        }

        $constructor = $reflection->getMethod('__construct');
        $filename = $constructor->getFileName();
        $startLine = $constructor->getStartLine();
        $endLine = $constructor->getEndLine();

        $fileContent = file($filename);
        return implode('', array_slice($fileContent, $startLine - 1, $endLine - $startLine + 1));
    }
}