<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Customer
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $utr
 * @property Carbon $dob
 * @property string $phone
 * @property string $profile_pic_path
 * 
 * @property Collection|Income[] $incomes
 *
 * @package App\Models
 */

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'id';

    protected $dates = ['dob'];

    protected $fillable = [
        'id',
        'name',
        'email',
        'utr',
        'dob',
        'phone',
        'profile_pic_path'
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public static function customersWithIncomes($startDate = null, $endDate = null){

        $queryComplements = '';

        $queryTotals = 'with amount_totals as (
            select
                incomes.customer_id,
                sum(incomes.amount) as total
            from incomes

            where deleted_at is null ';


        $queryIncomes = 'select 
                customers.id,
                customers.name,
                customers.email,
                incomes.description,
                incomes.amount,
                amount_totals.total,
                DATE_FORMAT(incomes.income_date, "%d/%m/%Y") as incomeDate
            from customers 
            
            left join incomes
            on incomes.customer_id = customers.id
            
            left join amount_totals
            on customers.id = amount_totals.customer_id
            
            where customers.deleted_at is null and incomes.deleted_at is null ';


        if ($startDate) {
            $queryComplements = 'and incomes.income_date >= "'. $startDate.'"';
        }

        if ($endDate) {
            $queryComplements .= 'and incomes.income_date <= "'. $endDate .'"';
        }

        $queryTotals .= $queryComplements.' group by incomes.customer_id) ';

        $queryIncomes .= $queryComplements.' order by customers.id';

        $query = $queryTotals . $queryIncomes;


        return DB::select($query);
    }

}
