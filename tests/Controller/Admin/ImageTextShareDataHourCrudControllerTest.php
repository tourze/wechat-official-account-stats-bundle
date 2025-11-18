<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\ImageTextShareDataHourCrudController;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataHourCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextShareDataHourCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<ImageTextShareDataHour>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @phpstan-ignore-next-line */
        return self::getService(ImageTextShareDataHourCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'refDate' => ['统计日期'];
        yield 'refHour' => ['小时'];
        yield 'shareScene' => ['分享场景'];
        yield 'shareCount' => ['分享次数'];
        yield 'shareUser' => ['分享人数'];
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

    public function testControllerConfiguration(): void
    {
        $controller = new ImageTextShareDataHourCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(ImageTextShareDataHour::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new ImageTextShareDataHourCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new ImageTextShareDataHourCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
