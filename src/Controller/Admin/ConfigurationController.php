<?php

namespace Gcromo\Controller\Admin;

use Gcromo\Form\Admin\ConfigurationType;
use Gcromo\Form\Data\ConfigurationData;
use Gcromo\Provider\BudgetStatusProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigurationController extends FrameworkBundleAdminController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request): Response
    {
        $configuration = ConfigurationData::fromConfiguration();
        $form = $this->createForm(ConfigurationType::class, $configuration, [
            'status_choices' => BudgetStatusProvider::choices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ConfigurationData $data */
            $data = $form->getData();

            foreach ($data->toConfigurationArray() as $configurationKey => $value) {
                \Configuration::updateValue($configurationKey, $value);
            }

            $this->addFlash(
                'success',
                $this->translator->trans('Configuration updated successfully.', [], 'Modules.Gcromo.Admin')
            );

            return $this->redirectToRoute('admin_gcromo_configuration_index');
        }

        return $this->render('@Modules/gcromo/views/templates/admin/configuration/index.html.twig', [
            'layoutTitle' => $this->translator->trans('GCromo settings', [], 'Modules.Gcromo.Admin'),
            'form' => $form->createView(),
        ]);
    }
}
