<?php

namespace Gcromo\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

class BudgetController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(): Response
    {
        return $this->render('@Modules/gcromo/views/templates/admin/budget/index.html.twig', [
            'layoutTitle' => $this->trans('Budget manager', 'Modules.Gcromo.Admin'),
        ]);
    }
}
