<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\RebateGoodsDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

/**
 * @internal
 */
#[CoversClass(RebateGoodsDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class RebateGoodsDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<RebateGoodsData>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(RebateGoodsDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'date' => ['统计日期'];
        yield 'exposureCount' => ['曝光量'];
        yield 'clickCount' => ['点击量'];
        yield 'clickRate' => ['点击率'];
        yield 'orderCount' => ['订单量'];
        yield 'orderRate' => ['下单率'];
        yield 'totalFee' => ['订单金额(分)'];
        yield 'totalCommission' => ['预估收入(分)'];
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
        $controller = new RebateGoodsDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(RebateGoodsData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new RebateGoodsDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new RebateGoodsDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
