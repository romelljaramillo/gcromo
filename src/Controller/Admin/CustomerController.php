<?php

namespace Gcromo\Controller\Admin;

use Gcromo\Repository\BudgetRepository;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerController extends FrameworkBundleAdminController
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
        $customers = $this->budgetRepository->findCustomerSummary();

        return $this->render('@Modules/gcromo/views/templates/admin/customer/index.html.twig', [
            'layoutTitle' => $this->translator->trans('Customer overview', [], 'Modules.Gcromo.Admin'),
            'customers' => $customers,
        ]);
    }
}
