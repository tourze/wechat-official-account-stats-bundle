<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\ImageTextStatisticsCrudController;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;

/**
 * @internal
 */
#[CoversClass(ImageTextStatisticsCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextStatisticsCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): ImageTextStatisticsCrudController
    {
        return self::getService(ImageTextStatisticsCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '用户来源' => ['用户来源'];
        yield '图文页阅读人数' => ['图文页阅读人数'];
        yield '图文页阅读次数' => ['图文页阅读次数'];
        yield '原文页阅读人数' => ['原文页阅读人数'];
        yield '原文页阅读次数' => ['原文页阅读次数'];
        yield '分享人数' => ['分享人数'];
        yield '分享次数' => ['分享次数'];
        yield '收藏人数' => ['收藏人数'];
        yield '收藏次数' => ['收藏次数'];
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
        $controller = new ImageTextStatisticsCrudController();
        $this->assertSame(
            ImageTextStatistics::class,
            $controller::getEntityFqcn()
        );
    }

    public function testControllerConfiguration(): void
    {
        $controller = new ImageTextStatisticsCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(ImageTextStatistics::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new ImageTextStatisticsCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new ImageTextStatisticsCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
