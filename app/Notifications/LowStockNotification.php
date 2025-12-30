<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Product;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database','mail']; // عدل حسب حاجتك
    }

    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'name'       => $this->product->name,
            'stock'      => $this->product->stock,
            'reorder_level' => $this->product->reorder_level,
            'url' => route('admin.products.edit', $this->product->id)
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("تنبيه: المنتج \"{$this->product->name}\" قرب النفاد")
            ->line("الكمية المتاحة: {$this->product->stock}. مستوى التنبيه: {$this->product->reorder_level}.")
            ->action('اذهب للصنف', $this->product ? route('admin.products.edit', $this->product->id) : url('/'))
            ->line('راجع المخزون أو اصدر أمر شراء.');
    }
}
