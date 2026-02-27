<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/src/Repository/SeoCategoryNameRepository.php';

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PsSeoNames\Repository\SeoCategoryNameRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PsSeoNames extends Module
{
    private const FIELD_NAME = 'psseonames_seo_name';

    /** @var SeoCategoryNameRepository|null */
    private $repository;

    /** @var array<int, string|null> */
    private $requestSeoNameCache = [];

    public function __construct()
    {
        $this->name = 'psseonames';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'Custom';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PS SEO Names');
        $this->description = $this->l('Allows storing and showing an alternative visible category name (SEO Name).');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionCategoryFormBuilderModifier')
            && $this->registerHook('actionAfterCreateCategoryFormHandler')
            && $this->registerHook('actionAfterUpdateCategoryFormHandler')
            && $this->registerHook('actionObjectCategoryDeleteAfter')
            && $this->registerHook('actionCategoryControllerSetVariables')
            && $this->installSql();
    }

    public function uninstall()
    {
        return $this->uninstallSql() && parent::uninstall();
    }

    public function hookActionCategoryFormBuilderModifier(array $params)
    {
        if (empty($params['form_builder'])) {
            return;
        }

        $idCategory = isset($params['id']) ? (int) $params['id'] : 0;
        $idShop = (int) $this->context->shop->id;
        $formBuilder = $params['form_builder'];

        $localizedValues = $idCategory > 0
            ? $this->getRepository()->getByCategory($idCategory, $idShop)
            : [];

        $formBuilder->add(self::FIELD_NAME, TranslatableType::class, [
            'label' => $this->l('Nombre Seo'),
            'type' => TextType::class,
            'required' => false,
            'data' => $localizedValues,
            'options' => [
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
                'empty_data' => '',
            ],
        ]);

        if (isset($params['data']) && is_array($params['data'])) {
            $params['data'][self::FIELD_NAME] = $localizedValues;
        }
    }

    public function hookActionAfterCreateCategoryFormHandler(array $params)
    {
        $this->saveSeoNamesFromFormData($params);
    }

    public function hookActionAfterUpdateCategoryFormHandler(array $params)
    {
        $this->saveSeoNamesFromFormData($params);
    }

    public function hookActionObjectCategoryDeleteAfter(array $params)
    {
        if (empty($params['object']) || !Validate::isLoadedObject($params['object'])) {
            return;
        }

        $idCategory = (int) $params['object']->id;
        $this->getRepository()->deleteByCategory($idCategory);
        unset($this->requestSeoNameCache[$idCategory]);
    }

    public function hookActionCategoryControllerSetVariables(array &$params)
    {
        if (empty($params['category']) || !is_array($params['category'])) {
            return;
        }

        $category = $params['category'];
        $idCategory = isset($category['id']) ? (int) $category['id'] : 0;

        if ($idCategory <= 0) {
            return;
        }

        $seoName = $this->getSeoNameForRequest($idCategory);
        if ($seoName === null || $seoName == '') {
            return;
        }

        $params['category']['name'] = $seoName;

        if (isset($params['listing']) && is_array($params['listing'])) {
            $params['listing']['label'] = $seoName;
        }

        if (isset($params['category_name'])) {
            $params['category_name'] = $seoName;
        }
    }

    private function saveSeoNamesFromFormData(array $params)
    {
        if (empty($params['id']) || empty($params['form_data']) || !is_array($params['form_data'])) {
            return;
        }

        $idCategory = (int) $params['id'];
        $idShop = (int) $this->context->shop->id;

        $seoNames = [];
        if (isset($params['form_data'][self::FIELD_NAME]) && is_array($params['form_data'][self::FIELD_NAME])) {
            foreach ($params['form_data'][self::FIELD_NAME] as $idLang => $value) {
                $idLang = (int) $idLang;
                if ($idLang <= 0) {
                    continue;
                }

                $seoNames[$idLang] = $this->sanitizeSeoName($value);
            }
        }

        $this->getRepository()->upsertByCategoryAndShop($idCategory, $idShop, $seoNames);
        unset($this->requestSeoNameCache[$idCategory]);
    }

    private function sanitizeSeoName($value)
    {
        $sanitized = trim(strip_tags((string) $value));

        if (Tools::strlen($sanitized) > 255) {
            $sanitized = Tools::substr($sanitized, 0, 255);
        }

        return $sanitized;
    }

    private function getSeoNameForRequest($idCategory)
    {
        $idCategory = (int) $idCategory;

        if (array_key_exists($idCategory, $this->requestSeoNameCache)) {
            return $this->requestSeoNameCache[$idCategory];
        }

        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

        $seoName = $this->getRepository()->getOne($idCategory, $idLang, $idShop);
        $this->requestSeoNameCache[$idCategory] = $seoName !== '' ? $seoName : null;

        return $this->requestSeoNameCache[$idCategory];
    }

    private function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = new SeoCategoryNameRepository(Db::getInstance());
        }

        return $this->repository;
    }

    private function installSql()
    {
        return $this->runSqlFile(__DIR__ . '/sql/install.php');
    }

    private function uninstallSql()
    {
        return $this->runSqlFile(__DIR__ . '/sql/uninstall.php');
    }

    private function runSqlFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $queries = include $file;
        if (!is_array($queries)) {
            return false;
        }

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
