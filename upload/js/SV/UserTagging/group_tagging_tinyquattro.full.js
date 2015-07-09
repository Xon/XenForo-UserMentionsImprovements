!function($, window, document, _undefined)
{
    XenForo.SV_UserGroupTaggingTinyQuattro = function($editor)
    {
        tinymce.create('tinymce.plugins.xen_group_tagging',
        {
            init: function(editor)
            {
                //Only compatible with XenForo 1.2.x
                var tools = xenMCE.Lib.getTools();

                if(tools.getParam('oldXen'))
                    return false;

                editor.on('PastePreProcess', function(e){
                    e.content = e.content.replace(/([\w\W]|^)<a\s[^>]*data-usergroup="(\d+, [^"]+)"[^>]*>([\w\W]+?)<\/a>/gi,
                            function(match, prefix, user, username) {
                                var userInfo = user.split(', ');
                                if (!parseInt(userInfo[0], 10)){
                                    return match;
                                }
                                return prefix + (prefix == '@' ? '' : '@') + userInfo[1].replace(/^@/, '');
                            }
                    );
                });
            }
        });

        var quattroData = $editor.data('quattro');

        if(quattroData){
            if ($.inArray('xen_tagging', quattroData.settings.plugins)) {
                tinymce.PluginManager.add('xen_group_tagging', tinymce.plugins.xen_group_tagging);
                quattroData.settings.plugins.push('xen_group_tagging');
            }
        }
    };
    XenForo.register('textarea.BbCodeWysiwygEditor', 'XenForo.SV_UserGroupTaggingTinyQuattro');
}
(jQuery, this, document);