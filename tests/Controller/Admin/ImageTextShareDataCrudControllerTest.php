<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\ImageTextShareDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextShareDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): ImageTextShareDataCrudController
    {
        return self::getService(ImageTextShareDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '分享场景' => ['分享场景'];
        yield '分享次数' => ['分享次数'];
        yield '分享人数' => ['分享人数'];
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
        $controller = new ImageTextShareDataCrudController();
        $this->assertSame(
            ImageTextShareData::class,
            $controller::getEntityFqcn()
        );
    }

    public function testControllerConfiguration(): void
    {
        $controller = new ImageTextShareDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(ImageTextShareData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new ImageTextShareDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new ImageTextShareDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
