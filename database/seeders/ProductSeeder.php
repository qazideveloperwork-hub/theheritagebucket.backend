<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $products = [
            [
                'name' => 'Indigo Floral Vase',
                'category' => 'blue-pottery',
                'regular_price' => 12500,
                'badge' => 'Specialty',
                'is_new_arrival' => true,
                'short_description' => 'Hand-painted floral motifs in deep cobalt blue on a creamy ceramic glaze.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCu1neqAvhrfyQOzfroFsKF6rB3JO8qalpoC8VOJDLLHWsHJcR7yg2aSmpAoxdCtvRQTH9i6DI4it43B0A4jmQ86idC4CLsYGXkqe89_wZ4eVnGzk12PK5LTejPfup7AQcyxDguJOqjRMqh7LANTzfwhLQyBMknEV57JPzJPnL5c6NmnDBaTPe5Rel5FP_xMuI7JQqygi2jJ4IxIbJKBqqfmi82oSaRaIhQ-flKcAplsWQmh40JJZ3qarMFBcbw2fMqistJu1-7mOLa',
            ],
            [
                'name' => 'Mirror-work Cushion',
                'category' => 'textiles-shawls',
                'regular_price' => 4800,
                'badge' => 'Handmade',
                'is_new_arrival' => true,
                'short_description' => 'Hand-woven cushion cover with traditional Balochi mirror (shisha) work.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB4GqhXOTjV29NGngG9Qi9-VVU7rgSOmXVC3iE8XG7Wk3CoDNJLOQdMXSK6BsYxt94CmbcJSrERtGrbRUKPW5D5dCeT_SpN0cKV4uqbnV5S-HForikTQWnnUF8mdNX74SWnCxADlr6b9YmvUq0Wutn04-40GpcueDgMtxO4kzMVkJtV-gUIcxbmqFWU5LOpFvcHQ3I4PnzLRYXnfpflw1sEsmR03mGvy5sFEzLsxbW6hSTlvteQh69PTFoS__8fIVvlJI1SEbMo8pUE',
            ],
            [
                'name' => 'Ajrak Motif Coasters',
                'category' => 'home-decor',
                'regular_price' => 2200,
                'is_new_arrival' => true,
                'short_description' => 'A set of four ceramic coasters with traditional block-print Ajrak patterns.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDOtSeeQvSPldk978mZu5p2bidK7YSznHQXjo6XMcX6lYjTAJz6K4TpXZNp9EnmKDaNSs6QDRZPC3OUS2XdVJq2JWrwf639bw3nhBqcc9HQXAWLS-KssFo1OVxLXTJiBqriBTxXvLn1f7CO9fKgwg7LTuoP-Iuuoa9Z1lUjWI9GztR04KfPAtSS1CLchU06Jc2EvskyFjplPm0VT4M7EFVwbWV9R95N-0tmboCRy7KQPWfNEoVOcdF_3314o3WpI_LWEYQLKs3ib4PA',
            ],
            [
                'name' => 'Copper Engraved Bowl',
                'category' => 'brass-copperware',
                'regular_price' => 8900,
                'is_new_arrival' => true,
                'short_description' => 'Hand-hammered copper fruit bowl with Persian-inspired floral engravings.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCBZ6RAFZfp4TuoSPavO2YclfL-PjOKX1fPmrIcJsAVwTN8SXSGrxdBRoHFxZHyZyfg_ay75vrmrsKn3W24cTzj79WVUfcvloqXqmwD9etDl3XU5WbjRR6qhhxHrNacZAQaOkncPKmfrnRTK401iSmm4oXFc45MUlsOLtF1xpgq_PZ527p8j6XbBNjbMLAu0FIHm7haq-E9W5k9bp8km7DGP6AP88HoT7wah3jx72aUaBNTrGZssHcqIoD46xSEoYukAtfGX95dZhK-',
            ],
            [
                'name' => 'Indigo Crimson Ajrak Shawl',
                'category' => 'textiles-shawls',
                'regular_price' => 12500,
                'badge' => 'Sindh Specialty',
                'is_featured' => true,
                'short_description' => 'Hand-blocked traditional silk-cotton mix shawl in deep indigo and crimson.',
                'material' => 'Silk-Cotton Mix',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBS4pYJFALcZMIIyX8mn6QTV319jsgszi74XnVebYFO27COCAR-1b8tx_lRuWCmu0uMzOZhoNASg-tvCDUNK-NWqEf5WZeAgDs9RV74U3HoZLBAwJvu-upkprvNFFBcD3XH8eWC95V_KMGvLxq8_2YBP_f9BXkJUsVTzkdTzMV48V8ZnxqpR8i99E8YlkLZJUfhORrIkv19kEATtLYqp9523MCAtdBHxwWTRP67llecQfu-9XlRUI7GfsCQLXPQC65sWNy14Cj74LxY',
            ],
            [
                'name' => 'Golden Tilla Embroidered Khussa',
                'category' => 'traditional-footwear',
                'regular_price' => 4800,
                'badge' => 'Heritage',
                'is_featured' => true,
                'short_description' => 'Premium camel leather Khussa with intricate gold Tilla embroidery and pearl work.',
                'material' => 'Camel Leather, Silk Thread',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCpYbGLEqKIRMyNmw607wnJaocmJNwWIM6NHGA21yiBKt1-5RJAS9BKkOlVoP1bnFo0IeOrqfYXVHzNAxhP3ccV0PbtBvxapQBhtGcCxkgoUFaBG9fw7w0HjZXfdUh80eqBvVWEhXIhbS7TrNAmxuUCkknrx1LgBZWJsOwdJhyJHsA4LH8qQxUQbRZnZlOZhTpa6QZLz-h022RrdufLKXnnwlMGTGQnO0zHnDLGAWsv2Iqz6FnefXWC8PyVTkYezp4CrIL0qiBUP4On',
            ],
            [
                'name' => 'Cobalt Floral Blue Pottery Set',
                'category' => 'blue-pottery',
                'regular_price' => 8200,
                'badge' => 'Classic',
                'is_featured' => true,
                'short_description' => 'Hand-painted glazed ceramic vase set with classic cobalt floral motifs.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC1tG0m__u7wDwGVee3xfM7x8peJObg9LxWVX3PE4RYooHSuN4Iu0-h5E2YR-33slPhJFU3PsiK2bfvck6uLfoM0dLG9ZTMpba62acKULrSmxQWUYgKBUdqRCDH6emDvvaonWQPGLUJ90zZVfPWp8gYkyGE4-U1yJrVAmdd_hiBEAy6G9aRuRN6JF5WVGu0oz4xB_Rbq_FCNHLDNGIL-8KXcazMhhozlUDX2jRiJKX2MxnpYqXuBBQid_TuADiFjZIsZcoR8x4L1Rm_',
            ],
            [
                'name' => 'Classic Oxblood Peshawari',
                'category' => 'traditional-footwear',
                'regular_price' => 5500,
                'badge' => 'Traditional',
                'is_featured' => true,
                'short_description' => 'Masterfully stitched Peshawari chappal in deep oxblood leather.',
                'material' => 'Full-grain Leather',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCWqtZ1KKX6qGreWcaYmzv1qPglKRXnwCUkqH4MCmLhCp8KaCRk6clzzv5dC6sHyvWzeqFHWSmK_yhr-j378MzajsATaE_GyHUsZP-6LQKFxPwVCGBxQGxcMD0ZfIuBxXBlvlzcdEWvp5EV3XGtBevAjnm5EnIm3tzCFwjnJcTtZDycplFoGyImOqwPLnkdwYX0tF7Lcwe_09hf3wV8if-bQGdSq198sctXHQDgQvGP4TMfyyy31KDAQmM4gYI_xR4BakiBkur23etL',
            ],
            [
                'name' => 'Hand-Woven Ivory Pashmina',
                'category' => 'textiles-shawls',
                'regular_price' => 32000,
                'badge' => 'Luxury',
                'is_featured' => true,
                'short_description' => '100% pure fine wool Pashmina with hand Soozni embroidery.',
                'material' => 'Pure Pashmina Wool',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNB5yxyZLzj7abXRq1rX4-TwEiUjVBwrwwwVyKtEZtFSvOMyX5IgvYx3LELv4OPjYLsIW7tPlRTOuRzBQcFNZdDwFbjkiZelDD26qLOA0Njb3NIaXCqlpyJ9nTUxTJW-p8DVV6QqDuZcieEaTONx4uBFZlGvsA9ODK7l-eI8qh9B6dxoKbc8dViUPKUadJ_7q2QebTpxep499y7AAOBjBWEHwYN5DAf1h9F3VJOg8E65X-C9eRJLQYzk1PYATxAJFjJbtIRp8M2E6Y',
            ],
            [
                'name' => 'Royal Brass Filigree Lantern',
                'category' => 'brass-copperware',
                'regular_price' => 15900,
                'badge' => 'Limited Edition',
                'is_featured' => true,
                'short_description' => 'Hand-beaten pure brass lantern with geometric fretwork and amber glass panels.',
                'material' => 'Pure Brass, Amber Glass',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBrjPPR6w7VbdeZvB8NTFQxUcaEqHJM7kAz7pgIk0tLh9FV8bzu33ybiVVUnZAZX_tRMGYdy-iiO2pjHlZKQb0qVEb-aY-Uqe3KsvpQitn7F_6piOG4TASt2tMQv8ls0f9aYA-GDpycWT9QmIU0Z9EoNORA2640xdd1kNv3h-1gDCDLXX3GMJj62aXtAAaOwXvRG6cbUaZfAWMmZiygzbHtMkn_ky5elk8ZXIzoGKYmlyfhHypJ8Tf3F_-FkYglAHk8VCgLvLaAvKBp',
            ],
            [
                'name' => 'Hand-painted Multani Blue Pottery Vase',
                'category' => 'blue-pottery',
                'regular_price' => 12500,
                'badge' => 'Regional Specialty',
                'is_featured' => true,
                'short_description' => 'A masterpiece of seven centuries of Kashi-kari artistic lineage.',
                'story' => "Born from the ancient \"Kashi-kari\" traditions of Multan, this vase is a testament to seven centuries of artistic lineage. Each piece is meticulously hand-thrown on a traditional potter's wheel and painted with natural cobalt pigments that endure for lifetimes. The signature floral motifs symbolize growth and the eternal flow of the Chenab river.",
                'artisan_name' => 'Ustad Ghulam Murtaza',
                'artisan_bio' => 'With over 45 years of experience, Ustad Ghulam is one of the few remaining masters of the authentic "Kashi" style. He personally oversees the firing process, ensuring the blue glaze reaches its iconic crystalline depth. Every purchase directly supports his workshop and the training of four local apprentices.',
                'artisan_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6uNEbeItGPGR85hweuA9leD1vKM4Et15upOl0iiM9P6od6Nel3D6e_v8UvZgR69S1w1xbdkbEaSLvgulrQNs4XFm-dH4l1EWmW3qhK7KH8BOTHtrX4Jh60D5UO4kAMI2xpRINIpbvQdf0Jj1lJsgM3LVu9b41k8kj2xK4IVY2ZVRfSnqI3cNM2xz4PfQ2Dgfe0a5UV3i_wGMuCyqIKhyi_U8803Sshmo67s7NmPoE3dbR_kG1pV1vKo2ZGmbWowW44zibGnuZmZJ2',
                'dimensions' => 'H: 12" | D: 8" | W: 2.4kg',
                'material' => 'White Clay, Cobalt Glaze',
                'care_instructions' => [
                    'Hand wash only with mild soap and lukewarm water.',
                    'Avoid abrasive sponges to preserve the hand-painted finish.',
                    'Not suitable for microwave or dishwasher use.',
                    'Display in a cool, dry place away from direct high-intensity sunlight.',
                ],
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAN-n8vgulea_5isOPgo9AFys6jwKhkQtq2kAeYuEEcOniSQIQ36JdFHT8lB9LnfNg-0PsZ95wfR1y9dB0XrEyIKyy_Z9wG6s8-mX4U1mYdCJ35LSKENKZxBclJRraNBIzvp6Khhec1Kk1PiPjghLVDOqT0isvG3sM-ZPUpUuDlfQrO-HJCqvmHHplPzZPyp-GkSvb4wtSwBNh012TlkK2-YpspaEtx8sWZDbP9jtQuIzXh_-0Z53z9Yx2ZRtq-6lpW2q6SERsDMsiH',
                'gallery' => [
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuBQ6hAgrkTauScz11wKQafRD-h-MWVL5i9CuYeo6dvQKUDh02qWCVtItlyGYkh1QultzLsLz3Oe_a25Uwdn9mHQO0FEJhmTsLO52_WEn_Y8xtmBjw9uArB9mXwjG1rtnvNyzDMkyv61G2OPTIS-g00KXLnIFFxutBmAuZN0dyjSnXJ09DXsBBoqhQKmILf0Y4dUgMio-CGpRm6wiWhafLrjhZXhlQTTR45k7tpJEAar0-aihD5DRM4yfbfowcKv68i64x4dYtQK5SJo',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuAYZpdiiADAam0MhyxQ0M0Jfl47NxFC24kGkj3JJ_zqyiiiL9uH3wM-4or3aZkK8AWRL9w5bxW6CkdMQCO1tTMFqN67o-9Q7OxgyLPyrh6SkmeNVoGVkJmtLhlfF19x0eThZQJ-4izmwy_QD4l_LkQ3qfPXsQeo5Ev8jVW20mkc5Ir8NM3-1-2PhtiyEUPPuRpLLFhZE3iujvHIM4XQPL3MevIHNaOZq146BLuNX4N6Jwd1NJxMSPVqzYeaT3IVXw4s1kVd22Xt2aRK',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuAaAJ3ReQjT9T_a3R55Wq38iXNTsbooJPS4rE7r6pl-MAfOdfuCpA-AXMHDqLAvKook82zsLt4aVu_yYqQezJQifOf_NLTESL97fwUw26PjDm6Um5-vNtih_6MJ2c_e5Lg4gYunZjUDEnBE_fTIubvXSb7qiyvkv_czjdUbt6UN0-61Rg7otW_mi1qvPaJ2sxQ3yNlda18RWN73gRyZlzjd1UdffVfbky0NC9uIz2UIia5vYfD0zjasm8uyqHnie3Wp87IlMLVjPrFX',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuDn5bKHQzGzPN6OEwlIvvQ-jJTXaGXcYP9K56xCDfzhFObXGiuUupo7zz_-vtFOavZ1OeND2WXO_FByFZ1c-ZQCxZxmSAZU-3Zm-keefwJCw7r3t851zLZ211WhGfySz3XOrkyKMX7AZC8hf1T-0P5vzp_26M-TbcOSlxiS3Y_NO2rnvfjgMR8XItpWUedmAlZgFs4Y6X6QCtL_0OrvxakKelJCmvEzTN8CBkC82Mx0cr2UZgyFkLBXmza3hz1U0fI3fRP4dEqIpcRG',
                ],
            ],
            [
                'name' => 'Traditional Kashi Decorative Plate',
                'category' => 'blue-pottery',
                'regular_price' => 4800,
                'short_description' => 'Hand-painted decorative plate with intricate geometric Islamic patterns.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuANqleyd6eDijEF8-L3_xS5EUBIwg9srD0XGYkpNhObpmi9SBX4xwgsAQqQ_kXPGv8xKG7rerRB917yV4uqmLjuK3bx1aePt9n1OSh0rHfunuevbLe5a-7-9tIjvIRZuO6pPuwmd-G79xCYTqH-zacK-P8I_JhoAva0dlniPHzr4NqoI2H_i4Gh42rtFW52ak5EDMlEkuiQ4IQC4wvAYpm9TsOjezQNXHnlAEcdfp3rxWNDq4LX_YNTCoKh6CTeQ4pcQ2bBhyQ2ICLk',
            ],
            [
                'name' => 'Blue Floral Tea Set',
                'category' => 'blue-pottery',
                'regular_price' => 6200,
                'short_description' => 'A set of four modern minimalist Multani blue pottery tea cups with saucers.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDY0mgp0PNCi8jJrWTaOrslAyozF2yLpC8pl9Yqc7D0-SrKweDb45KI-wbzfbflQZ4iTFrZsKqYle3T-D5B_SQpw1-HUOymJqLdZafwvTDW91sSBk9zFiQ-QGcuoLl5sf-AOvMEUBipwUU875RvnQ0S9pW-dq7rATLIr4eyJ6pNqU6BrrjYfVBCpnGTY1wM4s1sOuONkBaYyRM_vfT_sn9TTmbQTKZwZ1BM0jie03aF-dddMZNVHS2idx79RxMa3eWwYc_B_Ia8MDOH',
            ],
            [
                'name' => 'Filigree Ceramic Lamp Base',
                'category' => 'blue-pottery',
                'regular_price' => 15000,
                'short_description' => 'Ornate Multani blue pottery lamp base with intricate arabesque cutouts.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAQ3hqjujOe8Y5-1D5Co4mUdVzBAmBDVrN1ew-mCmj3FZY52pmyfms8VFyJdROKwmRyUa76MYPXtDmGKTOruz_rDKx9Zxr6aWE811id8NYXTFe23d4O1GaAbmSfhi8YGPf62IpJd34ZWJ6-cgJfVZt2pbnO6Fk0vNJeqdBTbGiLGfPzzPomxC8azaYggAyASFeJGPtytwbqr1Omq0ljCjMm15Sbxn80TmV7-RA-9G1nQz9DQdsjCc1zkLlZOaolxvcEZFf7ZE60z3dw',
            ],
            [
                'name' => 'Kashi Spice Jar Duo',
                'category' => 'blue-pottery',
                'regular_price' => 3500,
                'short_description' => 'A pair of hand-painted Multani blue pottery spice jars with cork lids.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8uJm-0F51nS0rVSHbLA7PnMceVmcSVGxLuui_1mO-F1HEfzwzEt2vmTt751Ru8DJEYD7OfEEKv1edlKmuk8eWNQIlGDS-ZTc_wECmQNICDWH-IiMvJvxvBDYQaB4tS1d-avACI099DMBPEBHToAV3BKESDZ2x3tAuRzPFub0G8yZq2udnn4CRTRrDenvIVk3iBo3OJHqGpCAH1ESJyPvJdA9aISMkxPvh8QgJuaaozzXUxGYa-8rX2NVmplmL9DsgcTHGFLOZRDIC',
            ],
            [
                'name' => 'Heritage Rose Pashmina',
                'category' => 'textiles-shawls',
                'regular_price' => 32000,
                'badge' => 'Limited',
                'short_description' => 'A hand-woven Pashmina shawl in a soft, dusty rose colour with a fine weave.',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBR3sFc-EIF6X4kXxDMpbubIcAVLN9a680iJMKqwliPB-r29YnCq4LcyjWWa1wpyPHsswL3A5v148yGgkm32oaFOxKfcCfJR6JjCZ1gP-gdGiFrxtlEDs-d3pksvQbcsFIZolVkS9yYa8dlGcHJgBcI4DcN3cBwYKUBl-WAeCW7kfYToKJz6l_6MigcA3YUZmD24XEeoURevt8Fo7xIuyEARIsd2ttzFEHUYLv1A5UV4qsbbBh9Z-X5QDTHjzQSqobcKIr4uD40ufkY',
            ],
            [
                'name' => 'Carved Sheesham Coasters',
                'category' => 'home-decor',
                'regular_price' => 3900,
                'short_description' => 'Carved sheesham wood coasters with traditional jali cutwork patterns.',
                'material' => 'Sheesham Wood',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuArAMe5oiFfwkW3J1He3OXN_WG-b5RTAKo5Eb1iPQNjF6wT31Y-mNTZS4KkC6ixlAUWcZ4WtytuPzPCcCjyDtdA3HZUYO4bOqm1tJ2g4JyJxqH_4lfr0vKgRr68t38OkCbIX1lUT39V5lLTWeRGs2YFDXC7ukvelCAfZQUs3vonCMLMWt2b2AMCEKqhr7kpN4F_4x9DcUPRyoZcKF1Fs7flvv_a2Z1CRyd4jmGJD8AwLD5XYtj3wWTyAjp1S4u1GjIos2upUW8wozr8',
            ],
            [
                'name' => 'Hammered Brass Tray',
                'category' => 'brass-copperware',
                'regular_price' => 9500,
                'short_description' => 'An artisanal hammered brass tray with a subtle patina and engraved floral rim.',
                'material' => 'Hammered Brass',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBpKesmjq6jERPhMxlPujPp02PiGu2sNVBHsAJHICyzl8eJ0A7Al3RsruycWsj9cb0lrHl_n1lfW7acq-STHPyEFJkoEM3xF4X3YDA7l5Wg0B8hRi58OUlP5KkkcF_6_1ivtJhAiCbiwYzNNNvfZ5aM0HuXja06Xpb0fELxTxYc-mGueKps2pYLv2KHMurr4k8vPV8cWFCu3EjEABsBTmW5SJOZ0fTumzHPbt6-QGVas6pjZO_ObFgBJsZySOX4TQDktR5qfbbX2Ts0',
            ],
        ];

        foreach ($products as $index => $data) {
            $gallery = $data['gallery'] ?? [];
            unset($data['gallery']);

            $categorySlug = $data['category'];
            unset($data['category']);

            $story = $data['story'] ?? null;
            unset($data['story']);

            $shortDescription = $data['short_description'] ?? null;
            unset($data['short_description']);

            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug' => $slug,
                    'type' => 'simple',
                    'sku' => sprintf('HB-%04d', $index + 1),
                    'stock_quantity' => 25,
                    'status' => 'active',
                    'category_id' => $categories[$categorySlug]->id,
                ])
            );

            if ($story || $shortDescription) {
                $product->description()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['content' => $story, 'short_description' => $shortDescription]
                );
            }

            if (! empty($gallery)) {
                $product->images()->delete();
                foreach ($gallery as $sortOrder => $url) {
                    $product->images()->create(['url' => $url, 'sort_order' => $sortOrder]);
                }
            }
        }
    }
}
