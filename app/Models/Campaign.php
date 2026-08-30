<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'custom_page_published_at' => 'datetime',
            'page_content' => 'array',
        ];
    }

    public function landingContent(): array
    {
        $stored = is_array($this->page_content) ? $this->page_content : [];

        return array_replace_recursive($this->defaultLandingContent(), $stored);
    }

    public function defaultLandingContent(): array
    {
        return [
            'announcement' => $this->top_title_1 ?: '🔥 মেগা অফার! সীমিত সময়ের বিশেষ মূল্য',
            'headline' => $this->heading_1 ?: strip_tags((string) $this->name),
            'subheadline' => strip_tags((string) ($this->short_description ?: $this->description)),
            'cta' => 'অর্ডার করতে ক্লিক করুন',
            'phone_label' => 'যে কোনো প্রয়োজনে কল করুন',
            'phone' => '',
            'hero_image' => $this->banner ?: $this->image_one,
            'gallery' => array_values(array_filter([
                $this->image_one, $this->image_two, $this->image_three,
            ])),
            'video' => $this->video,
            'features_title' => $this->heading_2 ?: 'পণ্যের বিবরণ',
            'features_html' => $this->description ?: '',
            'product_title' => $this->review ?: 'আপনার প্রয়োজন সঠিক সাইজটি সিলেক্ট করুন',
            'billing_title' => 'বিলিং বিবরণ',
            'order_title' => 'আপনার অর্ডার আইটেম',
            'stock_left' => 12,
            'recent_orders' => 34,
            'urgency_text' => 'স্টকে মাত্র {stock}টি বাকি',
            'faq' => [
                ['q' => 'কীভাবে অর্ডার করব?', 'a' => 'সাইজ ও কালার সিলেক্ট করে নাম, ফোন ও ঠিকানা দিয়ে অর্ডার কনফার্ম করুন।'],
                ['q' => 'পেমেন্ট কীভাবে?', 'a' => $this->billing_details ?: 'ক্যাশ অন ডেলিভারিতে পণ্য হাতে পেয়ে মূল্য পরিশোধ করতে পারবেন।'],
            ],
            'sections' => ['announcement', 'hero', 'cta', 'gallery', 'video', 'features', 'reviews', 'products', 'checkout'],
        ];
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.old_price',
                'products.new_price'
            );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'campaign_product',
            'campaign_id',
            'product_id'
        )->select(
            'products.id',
            'products.name',
            'products.slug',
            'products.old_price',
            'products.new_price'
        );
    }

    public function images()
    {
        return $this->hasMany(CampaignReview::class, 'campaign_id')
            ->select(
                'id',
                'image',
                'campaign_id'
            );
    }

    public function orderBumps()
    {
        return $this->hasMany(OrderBump::class, 'campaign_id');
    }

    public function hasVisualPage(): bool
    {
        return filled($this->page_html);
    }

    public function isCustomPageLive(): bool
    {
        return $this->custom_page_published_at !== null
            && filled($this->custom_html);
    }
}
