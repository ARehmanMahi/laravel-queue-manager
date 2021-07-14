<?php

namespace App\Exports;

use App\Models\Car;
use Maatwebsite\Excel\Concerns\FromCollection;

class CarsExport implements FromCollection
{
    /**
     * @var []
     */
    protected $payload = [];

    /**
     * Create a new CarsExport instance.
     *
     * CarsExport constructor.
     * @param array $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Car::limit(5)->get();
    }
}
