<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Product::query();

        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['enabled']) && $this->filters['enabled'] !== '') {
            $query->where('enabled', $this->filters['enabled']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category ID',
            'Name',
            'Description',
            'Price',
            'Stock',
            'Enabled',
            'Deleted At',
            'Created At',
            'Updated At',
        ];
    }
}