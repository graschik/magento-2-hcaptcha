<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Plugin\Magento\ReCaptchaAdminUi\Block\Adminhtml\System\Config\Form\Field\Notice;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\ReCaptchaAdminUi\Block\Adminhtml\System\Config\Form\Field\Notice;

class AddHCaptchaNotice
{
    /**
     * Add hcaptcha notice
     *
     * @param Notice $subject
     * @param string $result
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRender(
        Notice $subject,
        string $result,
        AbstractElement $element
    ): string {
        $result .= '<td colspan="4"><p class="' . $element->getId() . '_notice">' . '<strong>' . __('Important:')
            . ' ' . '</strong>' . ' <span>' . __('Please note, for hCAPTCHA to be enabled,
        the valid "hCaptcha Site Key" and "hCaptcha Secret Key" fields are required.') . '</span>' . '</p></td>';

        return '<tr id="row_' . $element->getHtmlId() . '">' . $result . '</tr>';
    }
}
