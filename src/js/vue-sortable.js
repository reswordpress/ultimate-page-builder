;(function ($) {

    const vSortable = {};

    if (!$().sortable) {
        throw new Error('jQuery UI Sortable not found');
    }

    vSortable.install = function (Vue, options) {

        Vue.directive('sortable', {

            bind : function (el, binding, vnode) {

            },

            update : function (newValue, oldValue, vnode) {

            },
            unbind : function (el) {
                $(el).sortable("destroy");
            },

            inserted : function (el, binding, vnode) {

                const values = {oldIndex : null, newIndex : null};

                $(el).sortable(binding.value || {});
                $(el).disableSelection();

                $(el).sortable("option", "start", (event, ui) => {
                    "use strict";
                    values.oldIndex = ui.item.index();
                });

                $(el).sortable("option", "update", (event, ui) => {
                    "use strict";

                    values.newIndex = ui.item.index();

                    if (!vnode.context.onUpdate) {
                        throw new Error('require onUpdate method');
                    }

                    vnode.context.onUpdate(event, $.extend(true, {}, values));

                    // reset :)
                    values.oldIndex = null;
                    values.newIndex = null;
                });
            }
        });
    };

    if (typeof exports == "object") {
        module.exports = vSortable
    }
    else if (typeof define == "function" && define.amd) {
        define([], function () {
            return vSortable
        })
    }
    else if (window.Vue) {
        window.vSortable = vSortable;
        Vue.use(vSortable)
    }

})(window.jQuery);