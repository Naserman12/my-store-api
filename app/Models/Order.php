<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'total',
        'currency',

        'customer_name',
        'customer_email',
        'customer_phone',

        'shipping_address',
        'shipping_city',
        'shipping_postal_code',

        'notes',
        'paid_at',

        // 🆕 الحقول الجديدة
        'delivery_date',
        'payment_method',
        'payment_fee',
        'shipping_method',
        'invoice_number',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'delivery_date' => 'datetime', // 🆕 مهم
    ];

    /* ================= RELATIONS ================= */

    // عناصر الطلب
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // سجل الحالات (tracking)
    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    // تقييم الطلب
    public function review()
    {
        return $this->hasOne(OrderReview::class);
    }

    // الدفع (إذا تستخدمه)
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}