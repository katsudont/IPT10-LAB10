<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Controllers\BaseController;

class SupplierController extends BaseController
{
    public function list()
    {
        $obj = new Supplier();
        $suppliers = $obj->all();

        $template = 'suppliers';
        $data = [
            'title' => 'Suppliers',
            'items' => $suppliers
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function single($id)
    {
        $obj = new Supplier();
        $supplier = $obj->getSupplier($id);

        $template = 'single-supplier';
        $data = [
            'title' => 'Supplier',
            'supplier' => $supplier
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function update($id)
    {
        $obj = new Supplier();
        $supplier = $obj->getSupplier($id);
        $supplier->fill($_POST);
        $result = $supplier->update();

        $template = 'single-supplier';
        $data = [
            'title' => 'Supplier',
            'supplier' => $supplier
        ];

        $output = $this->render($template, $data);

        return $output;
    }
}