<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_AdminLogo
 */

namespace ManiyaTech\AdminLogo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * ViewModel class for Admin Logo configuration.
 */
class AdminLogo implements ArgumentInterface
{
    private const XML_PATH_IS_ENABLED = 'adminlogo/general/enabled';
    private const XML_PATH_LOGO_TITLE = 'adminlogo/general/logo_title';
    private const XML_PATH_ADMIN_LOGIN_LOGO = 'adminlogo/general/login_page_logo';
    private const XML_PATH_ADMIN_MENU_LOGO = 'adminlogo/general/menu_logo';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var AssetRepository
     */
    private AssetRepository $assetRepo;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AssetRepository $assetRepo
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepo
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Check if the custom admin logo module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom logo title from config.
     *
     * @return string|null
     */
    public function getLogoTitle(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LOGO_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get admin login page logo URL.
     *
     * @return string
     */
    public function getAdminLoginPageLogo(): string
    {
        return $this->getMediaLogoPath(self::XML_PATH_ADMIN_LOGIN_LOGO);
    }

    /**
     * Get admin menu logo URL.
     *
     * @return string
     */
    public function getAdminMenuLogo(): string
    {
        return $this->getMediaLogoPath(self::XML_PATH_ADMIN_MENU_LOGO);
    }

    /**
     * Get full media URL for a given config path.
     *
     * @param string $configPath
     * @return string
     */
    private function getMediaLogoPath(string $configPath): string
    {
        $logo = $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
        
        if (empty($logo)) {
            return '';
        } elseif ($logo == 'menu-logo.ico') {
            return $this->assetRepo->getUrl('ManiyaTech_AdminLogo::images/menu-logo.ico');
        } elseif ($logo == 'admin-logo.jpg') {
            return $this->assetRepo->getUrl('ManiyaTech_AdminLogo::images/admin-logo.jpg');
        }

        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'ManiyaTech/Adminlogo/' . ltrim($logo, '/');
    }
}
