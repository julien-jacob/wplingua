wp.blocks.registerBlockType(
    'wplingua/languages-switcher',
    {
        title: wplngLocalize.label.title,
        description: wplngLocalize.label.description,
        icon: "translation",
        attributes: {
            style: { type: 'string', default: '' },
            title: { type: 'string', default: '' },
            flags: { type: 'string', default: '' },
            theme: { type: 'string', default: '' },
        },
        edit: function (props) {
            return [
                wp.element.createElement(
                    wp.serverSideRender,
                    {
                        block: "wplingua/languages-switcher",
                        attributes: props.attributes
                    }
                ),
                wp.element.createElement(
                    wp.editor.InspectorControls,
                    {
                        key: "inspector"
                    },
                    wp.element.createElement(
                        'div',
                        { className: 'wplng-block-attributes' },
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.style,
                                value: props.attributes.style,
                                onChange: function (value) {
                                    props.setAttributes({ style: value });
                                },
                                options: [
                                    { value: '', label: wplngLocalize.label.default },
                                    { value: 'list', label: wplngLocalize.style.list },
                                    { value: 'block', label: wplngLocalize.style.block },
                                    { value: 'dropdown', label: wplngLocalize.style.dropdown }
                                ]
                            }
                        ),
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.title,
                                value: props.attributes.title,
                                onChange: function (value) {
                                    props.setAttributes({ title: value });
                                },
                                options: [
                                    { value: '', label: wplngLocalize.label.default },
                                    { value: 'original', label: wplngLocalize.title.original },
                                    { value: 'name', label: wplngLocalize.title.name },
                                    { value: 'id', label: wplngLocalize.title.id },
                                    { value: 'none', label: wplngLocalize.title.none }
                                ]
                            }
                        ),
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.flags,
                                value: props.attributes.flags,
                                onChange: function (value) {
                                    props.setAttributes({ flags: value });
                                },
                                options: [
                                    { value: '', label: wplngLocalize.label.default },
                                    { value: 'circle', label: wplngLocalize.flags.circle },
                                    { value: 'rectangular', label: wplngLocalize.flags.rectangular },
                                    { value: 'wave', label: wplngLocalize.flags.wave },
                                    { value: 'none', label: wplngLocalize.flags.none },
                                ]
                            }
                        ),
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.theme,
                                value: props.attributes.theme,
                                onChange: function (value) {
                                    props.setAttributes({ theme: value });
                                },
                                options: [
                                    { value: '', label: wplngLocalize.label.default },
                                    { value: 'light-double-smooth', label: wplngLocalize.theme['light-double-smooth'] },
                                    { value: 'light-double-square', label: wplngLocalize.theme['light-double-square'] },
                                    { value: 'light-simple-smooth', label: wplngLocalize.theme['light-simple-smooth'] },
                                    { value: 'light-simple-square', label: wplngLocalize.theme['light-simple-square'] },
                                    { value: 'grey-double-smooth', label: wplngLocalize.theme['grey-double-smooth'] },
                                    { value: 'grey-double-square', label: wplngLocalize.theme['grey-double-square'] },
                                    { value: 'grey-simple-smooth', label: wplngLocalize.theme['grey-simple-smooth'] },
                                    { value: 'grey-simple-square', label: wplngLocalize.theme['grey-simple-square'] },
                                    { value: 'dark-double-smooth', label: wplngLocalize.theme['dark-double-smooth'] },
                                    { value: 'dark-double-square', label: wplngLocalize.theme['dark-double-square'] },
                                    { value: 'dark-simple-smooth', label: wplngLocalize.theme['dark-simple-smooth'] },
                                    { value: 'dark-simple-square', label: wplngLocalize.theme['dark-simple-square'] },
                                    { value: 'blurblack-double-smooth', label: wplngLocalize.theme['blurblack-double-smooth'] },
                                    { value: 'blurblack-double-square', label: wplngLocalize.theme['blurblack-double-square'] },
                                    { value: 'blurblack-simple-smooth', label: wplngLocalize.theme['blurblack-simple-smooth'] },
                                    { value: 'blurblack-simple-square', label: wplngLocalize.theme['blurblack-simple-square'] },
                                    { value: 'blurwhite-double-smooth', label: wplngLocalize.theme['blurwhite-double-smooth'] },
                                    { value: 'blurwhite-double-square', label: wplngLocalize.theme['blurwhite-double-square'] },
                                    { value: 'blurwhite-simple-smooth', label: wplngLocalize.theme['blurwhite-simple-smooth'] },
                                    { value: 'blurwhite-simple-square', label: wplngLocalize.theme['blurwhite-simple-square'] },
                                ]
                            }
                        ),
                    )
                )
            ];
        },
        save: function () {
            return null;
        }
    }
);
