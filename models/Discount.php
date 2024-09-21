<?php
class Discount {
    public function calculateDiscounts($order) {
        $discounts = [];
        $totalDiscount = 0;
        $currentTotal = $order['total']; // Mevcut toplam tutar
    
        // 1. Kural: 1000 TL ve üzeri alışverişlerde %10 indirim
        if ($currentTotal >= 1000) {
            $discountAmount = $currentTotal * 0.10;
            $totalDiscount += $discountAmount;
            $currentTotal -= $discountAmount; // İndirim uygulandıktan sonra mevcut tutarı güncelle
            $discounts[] = [
                'discountReason' => '10_PERCENT_OVER_1000',
                'discountAmount' => number_format($discountAmount, 2),
                'subtotal' => number_format($currentTotal, 2)
            ];
        }
    
        // 2. Kural: 2 ID'li kategoriden 6 ürün alınırsa bir tane ücretsiz
        foreach ($order['items'] as $item) {
            if ($item['category_id'] == 2 && intval($item['quantity']) >= 6) {
                // Ürün miktarından 1'i ücretsiz vereceğiz
                $discountAmount = $item['unit_price']; // Ücretsiz verilen ürünün fiyatı
                $totalDiscount += $discountAmount;
                $currentTotal -= $discountAmount; // İndirim uygulandıktan sonra mevcut tutarı güncelle
                $discounts[] = [
                    'discountReason' => 'BUY_5_GET_1',
                    'discountAmount' => number_format($discountAmount, 2),
                    'subtotal' => number_format($currentTotal, 2)
                ];
            }
        }
    
        // 3. Kural: 1 ID'li kategoriden iki farklı üründen toplam 2 veya daha fazla ürün ya da aynı üründen 2 veya daha fazla alınırsa en ucuz ürüne %20 indirim
        $category1Items = array_filter($order['items'], function ($item) {
            return $item['category_id'] == 1;
        });
    
        // 1 ID'li kategoriden alınan ürünlerin miktarını topla
        $totalCategory1Quantity = array_reduce($category1Items, function ($carry, $item) {
            return $carry + intval($item['quantity']);
        }, 0);
    
        // Eğer toplamda 2 veya daha fazla ürün alındıysa, en ucuz ürüne %20 indirim uygulansın
        if ($totalCategory1Quantity >= 2) {
            // En ucuz ürünü bul
            usort($category1Items, function ($a, $b) {
                return $a['unit_price'] <=> $b['unit_price'];
            });
            $cheapestItem = $category1Items[0];
            $discountAmount = $cheapestItem['unit_price'] * 0.20;
            $totalDiscount += $discountAmount;
            $currentTotal -= $discountAmount; // İndirim uygulandıktan sonra mevcut tutarı güncelle
            $discounts[] = [
                'discountReason' => 'CHEAPEST_20_PERCENT',
                'discountAmount' => number_format($discountAmount, 2),
                'subtotal' => number_format($currentTotal, 2)
            ];
        }
    
        return [
            'discounts' => $discounts,
            'totalDiscount' => number_format($totalDiscount, 2),
            'discountedTotal' => number_format($currentTotal, 2)
        ];
    }
    
    
}
