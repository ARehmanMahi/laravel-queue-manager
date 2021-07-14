<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Car
 *
 * @mixin IdeHelperCar
 * @property int $car_id
 * @property string $chassis_no
 * @property int $make_id
 * @property int $model_id
 * @property string|null $engine_code sed as engine no
 * @property int|null $engine_size
 * @property int|null $fuel_id
 * @property int $drive_id
 * @property int|null $seats
 * @property int|null $doors
 * @property int $ext_color_id
 * @property int|null $int_color_id
 * @property string|null $registration_year
 * @property string|null $registration_month
 * @property int|null $vehicle_total_price
 * @property string|null $manufacture_year
 * @property int|null $instruct_chassis
 * @property string|null $soldout_date_disp
 * @property string|null $car_dealer_price
 * @property int $body_type_id
 * @property int|null $grade_id
 * @property int|null $instruct_meter
 * @property int|null $instruct_emboss
 * @property int|null $manufacture_month
 * @property int|null $dimension_l
 * @property int|null $dimension_w
 * @property int|null $steering_id
 * @property int|null $dimension_h
 * @property int|null $m3
 * @property string|null $model_code
 * @property int|null $mileage
 * @property int $auction_company_id
 * @property int $vehicle_price
 * @property int $vehicle_fee
 * @property string|null $weight
 * @property int $is_sale
 * @property int|null $currency_id
 * @property string|null $fob_price
 * @property int|null $price_ask
 * @property string $sale_price
 * @property string $cost
 * @property int $parent_id
 * @property int $user_id
 * @property int $delete_state
 * @property string $registered_time
 * @property int|null $last_edit_user
 * @property string|null $last_edit_time
 * @property string|null $soldout_date
 * @property string|null $salable_registered_day
 * @property int|null $is_group
 * @property string|null $car_auction_pl
 * @property int|null $accessories_id not required here, remove after discussion
 * @property float|null $discount_rate
 * @property string|null $model_year
 * @property int|null $transmission_id
 * @property string|null $version_class
 * @property string|null $memo
 * @property string|null $discount
 * @property string|null $memo2
 * @property int $views
 * @property int $sort_no
 * @property int|null $roro_freight
 * @property int|null $roro_vanning
 * @property int|null $inspection
 * @property string|null $color_code
 * @property string|null $engine_code2 used as engine code
 * @property int $repair_cost
 * @property int $other_cost
 * @property int|null $is_delivery
 * @property int $interior_grade_id
 * @property int $exterior_grade_id
 * @property int|null $is_display
 * @property int|null $price_type_id
 * @property string|null $eta_date
 * @property string|null $shipment_date
 * @property int $mukechi_id
 * @property string|null $mukechi_remarks
 * @property string|null $bl_no
 * @method static \Illuminate\Database\Eloquent\Builder|Car newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Car newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Car query()
 */
	class IdeHelperCar extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 */
	class IdeHelperUser extends \Eloquent {}
}

