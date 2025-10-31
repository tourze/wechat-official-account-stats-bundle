<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\AdvertisingSpaceDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

/**
 * @internal
 */
#[CoversClass(AdvertisingSpaceDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AdvertisingSpaceDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): AdvertisingSpaceDataCrudController
    {
        return self::getService(AdvertisingSpaceDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '广告位类型ID' => ['广告位类型ID'];
        yield '广告位类型名称' => ['广告位类型名称'];
        yield '拉取量' => ['拉取量'];
        yield '曝光量' => ['曝光量'];
        yield '曝光率' => ['曝光率'];
        yield '点击量' => ['点击量'];
        yield '点击率' => ['点击率'];
        yield '收入(分)' => ['收入(分)'];
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
        $controller = new AdvertisingSpaceDataCrudController();
        $this->assertSame(
            AdvertisingSpaceData::class,
            $controller::getEntityFqcn()
        );
    }

    public function testControllerConfiguration(): void
    {
        $controller = new AdvertisingSpaceDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(AdvertisingSpaceData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new AdvertisingSpaceDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new AdvertisingSpaceDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
