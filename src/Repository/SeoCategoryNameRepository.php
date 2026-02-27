<?php

namespace PsSeoNames\Repository;

use Db;

class SeoCategoryNameRepository
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getByCategory($idCategory, $idShop)
    {
        $idCategory = (int) $idCategory;
        $idShop = (int) $idShop;

        $rows = $this->db->executeS(
            'SELECT id_lang, seo_name
            FROM `' . _DB_PREFIX_ . 'psseonames_category`
            WHERE id_category = ' . $idCategory . ' AND id_shop = ' . $idShop
        );

        if (!is_array($rows)) {
            return [];
        }

        $localized = [];
        foreach ($rows as $row) {
            $localized[(int) $row['id_lang']] = (string) $row['seo_name'];
        }

        return $localized;
    }

    public function getOne($idCategory, $idLang, $idShop)
    {
        $idCategory = (int) $idCategory;
        $idLang = (int) $idLang;
        $idShop = (int) $idShop;

        $query = new \DbQuery();
        $query->select('seo_name');
        $query->from('psseonames_category');
        $query->where('id_category = ' . $idCategory);
        $query->where('id_lang = ' . $idLang);
        $query->where('id_shop = ' . $idShop);

        $value = $this->db->getValue($query);

        return is_string($value) ? $value : '';
    }

    public function upsertByCategoryAndShop($idCategory, $idShop, array $localizedSeoNames)
    {
        $idCategory = (int) $idCategory;
        $idShop = (int) $idShop;

        $this->db->delete(
            'psseonames_category',
            'id_category = ' . $idCategory . ' AND id_shop = ' . $idShop
        );

        foreach ($localizedSeoNames as $idLang => $seoName) {
            $idLang = (int) $idLang;
            $seoName = trim((string) $seoName);

            if ($idLang <= 0 || $seoName === '') {
                continue;
            }

            $this->db->insert('psseonames_category', [
                'id_category' => $idCategory,
                'id_lang' => $idLang,
                'id_shop' => $idShop,
                'seo_name' => pSQL($seoName),
            ]);
        }
    }

    public function deleteByCategory($idCategory)
    {
        $this->db->delete('psseonames_category', 'id_category = ' . (int) $idCategory);
    }
}
