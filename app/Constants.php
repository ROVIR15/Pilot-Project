<?php

namespace App;
class Constants
{
    const PRODUCT_TYPE = [
        'KM' => 'KM',
        'KF' => 'KF',
        'KS' => 'KS',
        'KA' => 'KA',
    ];

    const GOODS_CATEGORY = [
        'finished_goods' => 'Gudang Barang Jadi',
        'raw_materials' => 'Gudang Bahan Mentah',
    ];

    const DESCRIPTION_OPTIONS = [
        'Organik' => 'Organik',
        'Anorganik' => 'Anorganik',
    ];

    // validate the goods category value
    public static function validateGoodsCategory($value)
    {
        if (!array_key_exists($value, self::GOODS_CATEGORY)) {
            throw new \Exception('Invalid goods category');
        }
    }

    // get key given value
    public static function getKeyByValue($value)
    {
        return array_search($value, self::GOODS_CATEGORY);
    }
}
