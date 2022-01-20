<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Income
 *
 * @property int $id
 * @property string $description
 * @property int $customer_id
 * @property double $amount
 * @property Carbon $income_date
 * @property string $tax_year
 * @property string $income_file_path
 * 
 * @property Customer $customers
 *
 * @package App\Models
 */

class Income extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'incomes';
    protected $primaryKey = 'id';

    protected $dates = ['income_date'];

    protected $fillable = [
        'description',
        'customer_id',
        'amount',
        'income_date',
        'tax_year'
    ];

    public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id');
	}

}
