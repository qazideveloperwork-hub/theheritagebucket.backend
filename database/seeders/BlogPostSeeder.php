<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'The Blue Pottery of Multan: A Thousand Years of Cobalt',
                'excerpt' => 'Tracing the origins of the iconic "Kashi Gari" craft, from the tile-work of ancient shrines to the contemporary dinnerware that graces modern homes.',
                'body' => "Tracing the origins of the iconic \"Kashi Gari\" craft, from the tile-work of ancient shrines to the contemporary dinnerware that graces modern homes. For over a thousand years, the potters of Multan have guarded a technique passed down through generations: a cobalt glaze fired at exacting temperatures to produce the crystalline blue that has come to define the city's identity.\n\nToday, a handful of workshops keep this tradition alive, training the next generation of artisans while supplying collectors and homes around the world with pieces that carry centuries of craft in every brushstroke.",
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCXm8sjJ1oYxP1WnjMsWyA3VCpAtQ2xxV0_OOa89TBfBeUz3SlLnMSFOqgJ6eLTdieuilehirxSTIMBcoGYGpqg7b3T3vRkX59zpcY8o6zMl1tg0WMBu8Vb-5xMss2HmZgIL2Dl76OVoyIPqDzPbAnAuQ1yQjfA514AjCXJJrZzmOZq6tv_2ICBmEvAAPTAA2_yi7mRnIUxttXG5qqq5vDzU17XmZ4xsf-sQ6W48HVU9Q5M2XsijwxB6L1YT-_cMkrODAALEme1A_dh',
                'category' => 'Regional Specialty',
                'author_name' => 'Zainab Rahim',
                'author_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDSFK8rRJaw1lgNsuuSJ72Y8VMElhWqFF2RqdeIbFoSs5w9DcZfQ-2pu4dNIF8MK8Bs4p7t05PE1NgQB6hOqWl9OeXmXu0hoXIHg7KIDWMXnlgb9Bse2pXLtjZIZrfQ-teF-qoWlCaZyDQaAXT6Ig8eI6oT2T-3zsTpD3LVJtBlnwtFUyF7V_HEUsHkGe7vz4bf4ZibokPE3uQ3S_cjnL730AwRmjq4LcldMfchCvNNtsQMVwBUuekz6mjbFiVdjnK23IQpFQ4TsdY0',
                'read_minutes' => 8,
                'is_featured' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Thread by Thread: The Geometry of Khaddar',
                'excerpt' => "Exploring the mathematical precision behind the hand-woven cotton of the Indus Valley and why it remains the world's most breathable fabric.",
                'body' => "Exploring the mathematical precision behind the hand-woven cotton of the Indus Valley and why it remains the world's most breathable fabric. Every Khaddar weave begins with a grid as precise as an architect's blueprint, drawn from generations of inherited pattern-memory rather than paper plans.\n\nThe result is a textile so finely balanced between warmth and breathability that it has outlasted centuries of imported alternatives.",
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA-PAgJrEfeFljIbwzG8VVuAzZxZs-BoXW-McCn6aIv1a8lEYyyKJz3GdCn12DuwTQZrl0StHfc4-zWiZDUMeY4BZv7k8tv9jrstbOxrFfY1eWKU_Mj_KC_GL968IoZmnw3XAAlbyJAMxWnxHgrYVtaaDL6uDd8YAsbJqSR4RxebhpCgYsTxSiRk0X7ZcHFQ2FGbNBh2n-jFFewisLxjB_DT5Xbi9iQ6uXEqpH-IdI9LHEAitPK2W0dQjX7DM1M_UYOUSoCAAoA9ARz',
                'category' => 'Techniques',
                'author_name' => 'Heritage Basket Editorial',
                'author_image_url' => null,
                'read_minutes' => 6,
                'is_featured' => false,
                'published_at' => now()->subDays(17),
            ],
            [
                'title' => 'Master of Blocks: A Legacy in Wood',
                'excerpt' => 'Meet Rahim Baksh, the 7th generation woodcarver who creates the stamps that define our seasonal collections.',
                'body' => "Meet Rahim Baksh, the 7th generation woodcarver who creates the stamps that define our seasonal collections. In a small workshop lined with decades of hand-carved blocks, Rahim continues a family trade passed down since his great-great-grandfather first picked up a chisel.\n\nEach block takes days to carve and can print thousands of yards of fabric before it's retired to the workshop's growing archive of patterns.",
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAEDdMO4neR8VXlG2nTWaRyx6DunEtrgmNaagobl0dffnLZ2o-OkQK0oNWaySfpBOT3LcuKUt6ZwIvN44K54RCEBC5HV-wCl6Ed8F1HPJspQct-6uU2IUBhTI9hfOZSfmkwSg0Tk0Kxj157Isy0BYbUF2Lv678I2edmrYTPCoeydI2Q-0u-T_oN5XqkbkLEb1FQJCZDa_dPXu4xiNrPktklmvX03CcPDziMj8pM7B0955jT4dQq3pcOD8cBEbq1gGZmMrzGUGGmtvbl',
                'category' => 'Profiles',
                'author_name' => 'Heritage Basket Editorial',
                'author_image_url' => null,
                'read_minutes' => 5,
                'is_featured' => false,
                'published_at' => now()->subDays(29),
            ],
            [
                'title' => 'The Slow Movement: Why Handmade Matters',
                'excerpt' => 'How choosing artisanal goods supports local ecosystems and preserves the environment from the impact of fast-fashion.',
                'body' => "How choosing artisanal goods supports local ecosystems and preserves the environment from the impact of fast-fashion. Every handmade purchase is a vote for a slower, more accountable supply chain — one where materials are sourced locally, waste is minimal, and the people behind each piece are known by name rather than lost in an anonymous factory line.\n\nIt's a small shift in habit that, multiplied across households, adds up to a meaningfully different way of consuming.",
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBMeZ1ZUoBYfvFblsoJFDZNGHiq80wj3fiNgIDvewjWvjQal0FSIouzfrEjmH-XJ163nkeYS-XFMdZSMr4QoGjX75UfVTYJX4Lt2Y-jB5JhdOuIK7IpbsbLQx1_PA3sWVneo8GlDNcmGuPW2om_25TxJFSE2zQgA3q0kUZ6UHAVFm9NDvCP9abbXOViibdstYmLrU7bVswrX10kcGXU_1Nhg6xhTz3MqzWLIk75WwXXk8zXBACxYOwZy70QOSmZToQWpg9vSrSUeLPY',
                'category' => 'Sustainability',
                'author_name' => 'Heritage Basket Editorial',
                'author_image_url' => null,
                'read_minutes' => 5,
                'is_featured' => false,
                'published_at' => now()->subDays(38),
            ],
        ];

        foreach ($posts as $post) {
            $slug = Str::slug($post['title']);

            BlogPost::updateOrCreate(['slug' => $slug], array_merge($post, ['slug' => $slug]));
        }
    }
}
