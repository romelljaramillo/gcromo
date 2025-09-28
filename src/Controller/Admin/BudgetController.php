<?php

namespace Gcromo\Controller\Admin;

use DateTimeImmutable;
use Exception;
use Gcromo\Form\Admin\BudgetType;
use Gcromo\Form\Data\BudgetData;
use Gcromo\Provider\BudgetStatusProvider;
use Gcromo\Repository\BudgetRepository;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

class BudgetController extends FrameworkBundleAdminController
{
    private BudgetRepository $budgetRepository;
    private TranslatorInterface $translator;

    public function __construct(BudgetRepository $budgetRepository, TranslatorInterface $translator)
    {
        $this->budgetRepository = $budgetRepository;
        $this->translator = $translator;
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(): Response
    {
        $budgets = $this->budgetRepository->findAll();

        return $this->render('@Modules/gcromo/views/templates/admin/budget/index.html.twig', [
            'layoutTitle' => $this->translator->trans('Budget manager', [], 'Modules.Gcromo.Admin'),
            'budgets' => $budgets,
            'statusLabels' => BudgetStatusProvider::STATUSES,
            'statusBadgeClasses' => BudgetStatusProvider::badgeClasses(),
            'deleteToken' => Tools::getAdminTokenLite('AdminGcromoBudget'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     */
    public function createAction(Request $request): Response
    {
        $budget = new BudgetData();
        $budget->setQuoteDate(new DateTimeImmutable());
        $budget->setStatus((string) \Configuration::get('GCROMO_DEFAULT_STATUS', null, null, null, 'draft'));
        $budget->setSalesRep(\Configuration::get('GCROMO_DEFAULT_SALES_REP') ?: null);

        $form = $this->createForm(BudgetType::class, $budget, [
            'status_choices' => BudgetStatusProvider::choices(),
            'customer_choices' => $this->buildCustomerChoices(),
        ]);

        return $this->handleForm($form, $request, $budget);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     */
    public function editAction(Request $request, int $budgetId): Response
    {
        $existing = $this->budgetRepository->find($budgetId);

        if (!$existing) {
            $this->addFlash('error', $this->translator->trans('Budget not found.', [], 'Modules.Gcromo.Admin'));

            return $this->redirectToRoute('admin_gcromo_budget_index');
        }

        try {
            $budget = BudgetData::fromArray($existing);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->translator->trans('The budget data could not be loaded.', [], 'Modules.Gcromo.Admin'));

            return $this->redirectToRoute('admin_gcromo_budget_index');
        }

        $form = $this->createForm(BudgetType::class, $budget, [
            'status_choices' => BudgetStatusProvider::choices(),
            'customer_choices' => $this->buildCustomerChoices(),
        ]);

        return $this->handleForm($form, $request, $budget, $budgetId);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     */
    public function deleteAction(Request $request, int $budgetId): RedirectResponse
    {
        $token = $request->query->get('token');
        if (!$this->isTokenValid($token)) {
            $this->addFlash('error', $this->trans('Invalid security token.', 'Modules.Gcromo.Admin'));

            return $this->redirectToRoute('admin_gcromo_budget_index');
        }

        $this->budgetRepository->delete($budgetId);

    $this->addFlash('success', $this->translator->trans('Budget deleted successfully.', [], 'Modules.Gcromo.Admin'));

        return $this->redirectToRoute('admin_gcromo_budget_index');
    }

    private function handleForm(FormInterface $form, Request $request, BudgetData $budget, ?int $budgetId = null): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BudgetData $data */
            $data = $form->getData();
            $data->setId($budgetId);

            $budgetId = $this->budgetRepository->save($data);

            $this->addFlash('success', $this->translator->trans('Budget saved successfully.', [], 'Modules.Gcromo.Admin'));

            $submittedButtons = $request->request->get($form->getName(), []);
            if (is_array($submittedButtons) && array_key_exists('save', $submittedButtons)) {
                return $this->redirectToRoute('admin_gcromo_budget_index');
            }

            return $this->redirectToRoute('admin_gcromo_budget_edit', ['budgetId' => $budgetId]);
        }

        return $this->render('@Modules/gcromo/views/templates/admin/budget/form.html.twig', [
            'layoutTitle' => $budgetId
                ? $this->translator->trans('Edit budget', [], 'Modules.Gcromo.Admin')
                : $this->translator->trans('Create budget', [], 'Modules.Gcromo.Admin'),
            'form' => $form->createView(),
            'isEditing' => null !== $budgetId,
            'budget' => $budget,
        ]);
    }

    private function buildCustomerChoices(): array
    {
        $customers = \Customer::getCustomers();
        $choices = [];

        foreach ($customers as $customer) {
            $label = sprintf('%s %s (%s)', $customer['firstname'], $customer['lastname'], $customer['email']);
            $choices[$label] = (int) $customer['id_customer'];
        }

        return $choices;
    }

    private function isTokenValid(?string $token): bool
    {
        return $token && $token === Tools::getAdminTokenLite('AdminGcromoBudget');
    }
}
