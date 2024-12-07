
wp.blocks.registerBlockType(
    'wplingua/languages-switcher',
    {
        title: wplngLocalize.message.title,
        edit: function () {
            return wp.element.createElement(
                wp.serverSideRender,
                {
                    block: 'wplingua/languages-switcher'
                }
            );
        }
    }
);
