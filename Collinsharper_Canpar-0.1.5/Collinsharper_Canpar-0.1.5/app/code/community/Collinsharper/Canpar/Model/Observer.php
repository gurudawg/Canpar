<?php

class Collinsharper_Canpar_Model_Observer extends Varien_Object
{
    public function updateShippingBlockOptions($observer)
    {
        $block = $observer->getBlock();
        $transport = $observer->getTransport();

        if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available
            || $block instanceof Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
        ) {
            $isAdmin = $block instanceof Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form;
            $this->_addCanparOptionCode($block, $transport, $isAdmin);
        }
    }

    protected function _addCanparOptionCode($block, $transport, $isAdmin = false)
    {
        $change = false;
        $currentRate = false;
        $searchtagname = "s_method_canpar";

        $doc = new DOMDocument();
        $html = $transport->getHtml();
        @$doc->loadHTML($html);

        $inputs = $doc->getElementsByTagName('input');
        foreach ($inputs as $input) {
            $input_id = (string) $input->getAttribute('id');
            $tag_name = (string) $input->getAttribute('name');

            if ($isAdmin) {
                $valid_tag_name = $tag_name == 'order[shipping_method]';
                $valid_tag = $valid_tag_name && (stristr($input_id, $searchtagname));
                $valid_tag = $valid_tag && (string) $input->getAttribute('checked') == 'checked';

                if ($valid_tag) {
                    $currentRate = str_replace('canparmodule_', '', (string) $input->getAttribute('value'));

                    $shippingInfoDiv = false;
                    $divs = $doc->getElementsByTagName('div');
                    foreach ($divs as $div) {
                        $div_id = (string) $div->getAttribute('id');
                        if ($div_id == 'order-shipping-method-info') {
                            $shippingInfoDiv = $div;
                            break;
                        }
                    }

                    if (is_object($shippingInfoDiv)) {
                        $change = true;
                        $tagchilddoc = new DOMDocument();
                        $input_id = 's_method_canparmodule_' . $currentRate;
                        $tagchilddoc->loadXML($this->_getOptionsHtml($input_id, $block, true));

                        $tagchild = $tagchilddoc->getElementsByTagName('div')->item(0);
                        $ntagchild = $doc->importNode($tagchild, true);
                        $shippingInfoDiv->appendChild($ntagchild);
                    }

                    break;
                }
            } else {
                $valid_tag_name = $tag_name == 'shipping_method';
                $valid_tag = $valid_tag_name && (stristr($input_id, $searchtagname));

                if($valid_tag) {
                    $change = true;

                    $tagchilddoc = new DOMDocument();
                    $tagchilddoc->loadXML($this->_getOptionsHtml($input_id, $block));
                    $tagchild = $tagchilddoc->getElementsByTagName('div')->item(0);
                    $ntagchild = $doc->importNode($tagchild, true);
                    if($input->nextSibling) {
						$input->nextSibling->appendChild($ntagchild);
					}
					else{
						$input->appendChild($ntagchild);
					}
                }
            }
        }

        if ($change) {
            $html = $this->_applyUpdate($doc, $block);
        }

        $transport->setHtml($html);

        return $this;
    }

    protected function _applyUpdate($doc, $block)
    {
        // The correct template will be chosen based on which area (frontend, adminhtml) we're already in.
        $script = $block->getLayout()->createBlock('core/template')
            ->setTemplate('chcanpar/options_js.phtml')
            ->toHtml();

        $dochtml = $doc->C14N();
        $replace = array('<html>', '</html>', '<body>', '</body>');
        $dochtml = str_replace($replace, '', $dochtml);

        return "<!-- updated in " . __CLASS__ . " --> \n {$dochtml} \n {$script}";
    }

    protected function _getOptionsHtml($tag_id, $block, $visible = false)
    {
        return $block->getLayout()->createBlock('core/template')
            ->setArea('frontend')
            ->setTemplate('chcanpar/options.phtml')
            ->setTagId($tag_id)
            ->setIsVisible($visible)
            ->toHtml();
    }
}
