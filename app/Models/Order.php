<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number','user_id','guest_token',
        'customer_name','customer_email','customer_phone','customer_ci',
        'address_line1','address_city','address_department','address_country','address_notes',
        'billing_ruc','billing_name',
        'payment_method','payment_status','shipping_method','shipping_cost',
        'transfer_receipt','bancard_process_id','pagopar_hash',
        'subtotal','total','status','internal_notes',
    ];

    protected $casts = ['shipping_cost' => 'integer'];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }

    public static function generateNumber(): string
    {
        return 'ORD-' . strtoupper(substr(uniqid(), -8));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendiente'  => 'Pendiente',
            'confirmado' => 'Confirmado',
            'preparando' => 'Preparando',
            'enviado'    => 'Enviado',
            'entregado'  => 'Entregado',
            'cancelado'  => 'Cancelado',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendiente'  => 'yellow',
            'confirmado' => 'blue',
            'preparando' => 'indigo',
            'enviado'    => 'purple',
            'entregado'  => 'green',
            'cancelado'  => 'red',
            default      => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pendiente'                => 'Pendiente',
            'pagado'                   => 'Pagado',
            'rechazado'                => 'Rechazado',
            'reembolsado'              => 'Reembolsado',
            'pendiente_confirmacion'   => 'Pendiente de confirmación',
            default                    => ucfirst($this->payment_status),
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Gs. ' . number_format($this->total, 0, ',', '.');
    }
}
