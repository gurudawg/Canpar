
    var canpar_ops = {
        residential : false,
        nsr         : false,
        xc          : false
    };

    var collectRates = function(item)
    {
        canpar_ops = {
            selectedrate : false,
            residential  : false,
            nsr          : false,
            xc           : false
        };
        
        var isFrontend = typeof $$('input:checked[type=radio][name=shipping_method]')[0] != "undefined",
            isAdmin    = typeof $$('input:checked[type=radio][name=order[shipping_method]]')[0] != "undefined";

        if (isFrontend) {
            checkout.setLoadWaiting('shipping-method');
            canpar_ops.selectedrate = $$('input:checked[type=radio][name=shipping_method]')[0].value;
        } else if (isAdmin) {
            canpar_ops.selectedrate = $$('input:checked[type=radio][name=order[shipping_method]]')[0].value;
        } else {
            console.log('Reached unpredicted situation, cannot resolve.  Unsure if frontend or backend.');
            return;
        }

        if (canpar_ops.selectedrate) {
            canpar_ops.residential = $('s_method_' + canpar_ops.selectedrate + '_residential').checked;
            canpar_ops.nsr         = $('s_method_' + canpar_ops.selectedrate + '_nsr').checked;
            canpar_ops.xc          = $('s_method_' + canpar_ops.selectedrate + '_xc').checked;
        }

        new Ajax.Updater('checkout-shipping-method-load', canparurl, {
            method: 'post',
            evalScripts: true,
            parameters: canpar_ops,
            onComplete: function() {
                if (isAdmin) {
                    order.loadShippingRates();
                    return;
                }

                var el = $(item.readAttribute('rel'));
                el.checked = true;
                showOptionsPanel(item.readAttribute('rel'));
                checkout.setLoadWaiting(false);
            }
        });

        return;
    };

    var hideAllOptionsPanels = function()
    {
        $$('.canparopt').each(function(e) {
            e.hide();
        });

        return;
    };

    var showOptionsPanel = function(methodId)
    {
        $(methodId + '_canparopt').show();
        return;
    };

    var observeOptions = function()
    {
        $H(canpar_ops).each(function(pair) {
            $$('input.' + pair.key).each(function(el) {
                Event.observe(el, 'click', function() {
                    collectRates(this);
                });
            });
        });

        return;
    };
