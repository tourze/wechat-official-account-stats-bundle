<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\MessageSendMonthDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;

/**
 * @internal
 */
#[CoversClass(MessageSendMonthDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendMonthDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<MessageSendMonthData>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @phpstan-ignore-next-line */
        return self::getService(MessageSendMonthDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '消息类型' => ['消息类型'];
        yield '发送用户数' => ['发送用户数'];
        yield '发送消息总数' => ['发送消息总数'];
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
        $controller = new MessageSendMonthDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(MessageSendMonthData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new MessageSendMonthDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new MessageSendMonthDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
