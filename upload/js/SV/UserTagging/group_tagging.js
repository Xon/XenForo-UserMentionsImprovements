!function($, window, document, _undefined)
{
    XenForo.SV_UserGroupTagging = function($textarea)
    {
        $(document).on('EditorInit', function (e, data) {
            var oldFunc = data.config.pasteCleanUpCallback;
            data.config.pasteCleanUpCallback = function(e, ed, html)
            {
                html = oldFunc(e, ed, html);
                html = html.replace(/(.|^)<a\s[^>]*data-usergroup="(\d+, [^"]+)"[^>]*>([\w\W]+?)<\/a>/gi,
                    function(match, prefix, user, username) {
                        var userInfo = user.split(', ');
                        if (!parseInt(userInfo[0], 10))
                        {
                            return match;
                        }
                        return prefix + (prefix == '@' ? '' : '@') + userInfo[1].replace(/^@/, '');
                    }
                );
                return html;
            };
        });
    };
    XenForo.register('textarea.BbCodeWysiwygEditor', 'XenForo.SV_UserGroupTagging');
}
(jQuery, this, document);