<?php

namespace App\Controller\Admin;

use App\Entity\Kategorie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class KategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Kategorie::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
