<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;

class StepRecipeAdmin extends AbstractAdmin
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        string $code = null,
        string $class = null,
        string $baseControllerName = null
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->translator = $translator;
    }

    public function configure(): void
    {
        $this->classnameLabel = $this->translator->trans('sonata_admin.breadcrum.step_recipe_list');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('StepRecipeInfo', [
                'label' => 'sonata_admin.form.tab_label.step_recipe_info',
            ])
                ->add('description', TextareaType::class, [
                    'label' => 'sonata_admin.label.step_recipe.description',
                ])
                ->add('sort', NumberType::class, [
                    'label' => 'sonata_admin.label.general.sort',
                ])
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('description', null, [
                'label' => 'sonata_admin.label.step_recipe.description',
            ])
            ->add('recipe', null, [
                'label' => 'sonata_admin.label.recipe.recipe',
            ])
            ->add('createdAt', 'date', [
                'label' => 'sonata_admin.label.general.created_at',
                'format' => 'd/m/Y - H:i:s',
                'locale' => 'fr',
                'timezone' => 'Europe/Paris',
            ])
            ->add('updatedAt', 'date', [
                'label' => 'sonata_admin.label.general.updated_at',
                'format' => 'd/m/Y - H:i:s',
                'locale' => 'fr',
                'timezone' => 'Europe/Paris',
            ])
            ->add('sort', null, [
                'label' => 'sonata_admin.label.general.sort',
                'editable' => true,
            ])
        ;
    }
}
