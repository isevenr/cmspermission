define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (Component, customerData, $) {
    'use strict';

    var cmsBlockInitialized = false;
    var intervalCheck;

    /**
     * Initialize sidebar
     */
    function initCmsPermissionWithPermission() {
        if (cmsBlockInitialized) {
            return;
        }

        cmsBlockInitialized = true;

        var cmsBlockPermissions = customerData.get('cms-block-permissions');
        $('.cms-block-permissions').each(function() {
            var id = parseInt($(this).data('cmsBlockId'));
            if (typeof(cmsBlockPermissions()['allowed_cms_blocks']) != 'undefined' && typeof(cmsBlockPermissions()['allowed_cms_blocks'][id]) != 'undefined') {
                clearInterval(intervalCheck);
                $(this).show();
            }
            // TODO: replacing html content is having trouble with requirejs. need to fix it
            //$(this).html(cmsBlockPermissions()['allowed_cms_blocks'][id]);
        });
    }

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.cmsBlockPermissions = customerData.get('cms-block-permissions');
            if ($.isEmptyObject(this.cmsBlockPermissions())) {
                try {
                    customerData.invalidate(['cms-block-permissions']);
                    customerData.reload(['cms-block-permissions'], true);
                }
                catch (err) {
                    console.log(err);
                }
            }

            intervalCheck = setInterval(initCmsPermissionWithPermission, 3000);
        }
    });
});
