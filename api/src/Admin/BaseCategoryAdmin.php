<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseCategoryAdmin extends AbstractAdmin
{
    protected TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        string $code = null,
        string $class = null,
        string $baseControllerName = null,
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->translator = $translator;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('categoryInfo', [
                'label' => 'sonata_admin.form.tab_label.category_info',
            ])
                ->add('label', TextType::class, [
                    'label' => 'sonata_admin.label.general.label',
                ])
                ->add('slug', null, [
                    'label' => 'sonata_admin.label.general.slug',
                    'disabled' => true,
                ])
                ->add('sort', NumberType::class, [
                    'label' => 'sonata_admin.label.general.sort',
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('label', null, [
            'label' => 'sonata_admin.label.general.label',
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('label', null, [
                'label' => 'sonata_admin.label.general.label',
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
            ->add(ListMapper::NAME_ACTIONS, null, [
                'label' => 'sonata_admin.general.actions',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }
}
