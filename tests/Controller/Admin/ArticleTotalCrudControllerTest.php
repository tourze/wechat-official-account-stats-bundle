<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\ArticleTotalCrudController;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

/**
 * @internal
 */
#[CoversClass(ArticleTotalCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ArticleTotalCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): ArticleTotalCrudController
    {
        return self::getService(ArticleTotalCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '送达人数' => ['送达人数'];
        yield '图文页阅读次数' => ['图文页阅读次数'];
        yield '图文页阅读人数' => ['图文页阅读人数'];
        yield '原文页阅读次数' => ['原文页阅读次数'];
        yield '原文页阅读人数' => ['原文页阅读人数'];
        yield '分享次数' => ['分享次数'];
        yield '分享人数' => ['分享人数'];
        yield '收藏次数' => ['收藏次数'];
        yield '收藏人数' => ['收藏人数'];
    }

    public static function provideNewPageFields(): iterable
    {
        // 此控制器禁用了NEW操作，但需要提供虚拟数据避免空数据提供器错误
        yield 'dummy' => ['dummy'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 此控制器禁用了EDIT操作，但需要提供虚拟数据避免空数据提供器错误
        yield 'dummy' => ['dummy'];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new ArticleTotalCrudController();
        $this->assertSame(
            ArticleTotal::class,
            $controller::getEntityFqcn()
        );
    }

    public function testControllerConfiguration(): void
    {
        $controller = new ArticleTotalCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(ArticleTotal::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new ArticleTotalCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new ArticleTotalCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
