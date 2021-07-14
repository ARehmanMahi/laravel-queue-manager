<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCar
 */
class Car extends Model
{
    protected $table = 'jans_invoice.car_record';
    protected $primaryKey = 'car_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chassis_no', 'make_id', 'model_id', 'engine_code', 'engine_size', 'fuel_id', 'drive_id', 'seats',
        'doors', 'ext_color_id', 'int_color_id', 'registration_year', 'registration_month', 'vehicle_total_price',
        'manufacture_year', 'instruct_chassis', 'soldout_date_disp', 'car_dealer_price', 'body_type_id', 'grade_id',
        'instruct_meter', 'instruct_emboss', 'manufacture_month', 'dimension_l', 'dimension_w', 'steering_id',
        'dimension_h', 'm3', 'model_code', 'mileage', 'auction_company_id', 'vehicle_price', 'vehicle_fee', 'weight',
        'is_sale', 'currency_id', 'fob_price', 'price_ask', 'sale_price', 'cost', 'parent_id', 'user_id',
        'delete_state', 'registered_time', 'last_edit_user', 'last_edit_time', 'soldout_date', 'salable_registered_day',
        'is_group', 'car_auction_pl', 'accessories_id', 'discount_rate', 'model_year', 'transmission_id',
        'version_class', 'memo', 'discount', 'memo2', 'views', 'sort_no', 'roro_freight', 'roro_vanning', 'inspection',
        'color_code', 'engine_code2', 'repair_cost', 'other_cost', 'is_delivery', 'interior_grade_id',
        'exterior_grade_id', 'is_display', 'price_type_id', 'eta_date', 'shipment_date', 'mukechi_id',
        'mukechi_remarks', 'bl_no'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
