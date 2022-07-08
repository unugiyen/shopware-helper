<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Form\Controller;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Captcha\Annotation\Captcha;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wdt\ShopwareHelper\Config\ExampleConfigService;
use Wdt\ShopwareHelper\Form\AbstractFormController;
use Wdt\ShopwareHelper\Mail\CustomMailService;

/**
 * @RouteScope(scopes={"storefront"})
 */
class CustomFormController extends AbstractFormController
{
    private CustomMailService $customMailService;
    private ExampleConfigService $customConfigService;

    public function __construct(
        LoggerInterface $logger,
        DataValidator $dataValidator,
        CustomMailService $customMailService,
        ExampleConfigService $customConfigService
    ) {
        parent::__construct($logger, $dataValidator);
        $this->customMailService = $customMailService;
        $this->customConfigService = $customConfigService;
    }

    /**
     * @Route(
     *     "/wdt/form/submit",
     *     name="frontend.wdt.form.submit",
     *     methods={"POST"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     * @Captcha
     */
    public function submit(Request $request, SalesChannelContext $context): JsonResponse
    {
        return parent::submit($request, $context);
    }

    /**
     * @Route(
     *     "/wdt/form/index",
     *     name="frontend.wdt.form.index",
     *     methods={"GET"}
     * )
     */
    public function index(SalesChannelContext $context): Response
    {
        $customConfig = $this->customConfigService->getCustomConfig($context->getSalesChannelId());

        return $this->renderStorefront(
            '@WdtShopwareHelper/storefront/page/content/wdt-custom-form-index.twig',
            [
                'customConfig' => $customConfig,
            ]
        );
    }

    protected function handleForm(InputBag $inputBag, SalesChannelContext $context): void
    {
        $this->customMailService->sendMails($inputBag, $context);
    }
}
