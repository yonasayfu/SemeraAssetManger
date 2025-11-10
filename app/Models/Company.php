<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'address_2',
        'city',
        'state',
        'postal_code',
        'country',
        'timezone',
        'currency',
        'date_format',
        'financial_year_start',
        'logo',
        'sidebar_logo',
        'hr_email',
        'brand_color',
        'secondary_color',
        'brand_logo_height',
        'brand_title_size',
        'brand_print_logo_height',
        'brand_logo_padding',
        'brand_logo_fit',
        'brand_logo_scale',
        'brand_logo_width',
        'brand_logo_offset_x',
        'brand_logo_offset_y',
        'sidebar_logo_height',
        'sidebar_logo_width',
    ];
}
