<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Form;

use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * @RouteScope(scopes={"storefront"})
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractFormController extends StorefrontController
{
    private const REQUIRED_TEMPLATE_FIELDS = [
        'recipients',
        'salesChannelId',
        'contentHtml',
        'contentPlain',
        'subject',
        'senderName',
    ];

    /** @var array<int, string> */
    protected array $violations = [];

    protected LoggerInterface $logger;
    private DataValidator $dataValidator;

    public function __construct(
        LoggerInterface $logger,
        DataValidator $validator
    ) {
        $this->logger = $logger;
        $this->dataValidator = $validator;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function handleForm(InputBag $inputBag, SalesChannelContext $context): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getDataValidationDefinition(SalesChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('wdt_form');
        $definition->add('fieldEmail', new NotBlank(), new Email());
        $definition->add('fieldTextArea', new NotBlank());
        $definition->add('fieldText', new NotBlank());
        $definition->add('fieldSelect', new NotBlank());
        $definition->add('fieldUpload', new NotBlank(), new File(null, '1M'));

        return $definition;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateForm(InputBag $inputBag, SalesChannelContext $context): void
    {
        $definition = $this->getDataValidationDefinition($context);

        $violations = $this->dataValidator->getViolations($inputBag->all(), $definition);
        if ($violations->count() > 0) {
            throw new ConstraintViolationException($violations, $inputBag->all());
        }
    }

    protected function getInputBag(Request $request): InputBag
    {
        $inputBag = $request->request;
        $fileBag = $request->files;

        if ($fileBag->count() > 0) {
            $inputBag->add($fileBag->all());
        }

        return $inputBag;
    }

    public function submit(Request $request, SalesChannelContext $context): JsonResponse
    {
        $inputBag = $this->getInputBag($request);

        $response = [];

        try {
            $this->validateForm($inputBag, $context);
            $this->handleForm($inputBag, $context);

            $responseData = [
                'type' => 'success',
                'alert' => $this->trans($this->getSnippetNameSpacePrefix().'.submitSuccessMessage'),
            ];

            $response[] = $responseData;
        } catch (ConstraintViolationException $formViolations) {
            try {
                foreach ($formViolations->getViolations() as $violation) {
                    $this->setViolations($violation, $this->violations);
                }
                $response[] = $this->getViolationResponseData($this->violations);
            } catch (Exception $e) {
                $this->log($e);
                $response[] = $this->getViolationResponseData([
                    $this->trans($this->getSnippetNameSpacePrefix().'.error'),
                ]);
            }
        } catch (Exception $e) {
            $this->log($e);
            $response[] = $this->getViolationResponseData([
                $this->trans($this->getSnippetNameSpacePrefix().'.error'),
            ]);
        }

        return new JsonResponse($response);
    }

    /**
     * @param array<int, string> $violations
     *
     * @return array<string, string>
     */
    protected function getViolationResponseData(array $violations): array
    {
        return [
            'type' => 'danger',
            'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                'type' => 'danger',
                'list' => $violations,
            ]),
        ];
    }

    protected function getSnippetNameSpacePrefix(): string
    {
        return 'wdt-shopware-helper';
    }

    /**
     * @param array<int, string> $violations
     */
    protected function setViolations(
        ConstraintViolationInterface $violation,
        array &$violations
    ): void {
        $fieldName = substr($violation->getPropertyPath(), 1);
        $fieldLabelSnippet = $this->getSnippetNameSpacePrefix().'.field.'.$fieldName;

        if (in_array($fieldName, self::REQUIRED_TEMPLATE_FIELDS)) {
            throw new Exception(sprintf('%1$s is empty in the template', $fieldName));
        }

        switch ($violation->getCode()) {
            case 'VIOLATION::IS_BLANK_ERROR':
                $violations[] = $this->getFormattedViolationMessageSnippet(
                    $this->trans($fieldLabelSnippet),
                    $this->trans($this->getSnippetNameSpacePrefix().'.violation.isBlank')
                );
                break;
            case 'VIOLATION::TOO_LARGE_ERROR':
                $violations[] = $this->getFormattedViolationMessageSnippet(
                    $this->trans($fieldLabelSnippet),
                    $this->trans($this->getSnippetNameSpacePrefix().'.violation.tooLarge')
                );
                break;
            default:
                $violations[] = $this->getFormattedViolationMessageSnippet(
                    $this->trans($fieldLabelSnippet),
                    $this->trans($this->getSnippetNameSpacePrefix().'.violation.invalid')
                );
        }
    }

    protected function getFormattedViolationMessageSnippet(string $fieldNameSnippet, string $violationSnippet): string
    {
        return $fieldNameSnippet.' - '.$violationSnippet;
    }

    protected function log(Exception $e, ?string $message = null): void
    {
        $this->logger->error(
            null === $message ? $e->getMessage() : $message,
            [
                'exceptionMessage' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
        );
    }
}
