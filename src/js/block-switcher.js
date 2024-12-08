// Registering a new block type for the WP block editor
wp.blocks.registerBlockType(
    "wplingua/languages-switcher", // Block name
    {
        title: wplngI18nGutenberg.label.title, // Block title
        description: wplngI18nGutenberg.label.description, // Block description
        icon: "translation", // Block icon
        attributes: {
            style: { type: "string", default: "" }, // Style attribute
            title: { type: "string", default: "" }, // Title attribute
            flags: { type: "string", default: "" }, // Flags attribute
            theme: { type: "string", default: "" }, // Theme attribute
        },
        // Edit function to define the block's editor interface
        edit: function (props) {
            return [
                // Render block content on the server side
                wp.element.createElement(
                    wp.serverSideRender,
                    {
                        block: "wplingua/languages-switcher",
                        attributes: props.attributes
                    }
                ),
                // Inspector controls for block attributes
                wp.element.createElement(
                    wp.blockEditor.InspectorControls,
                    {
                        key: "inspector"
                    },
                    wp.element.createElement(
                        "div",
                        { className: "wplng-block-attributes" },
                        // Style selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngI18nGutenberg.input.style,
                                value: props.attributes.style,
                                onChange: function (value) {
                                    props.setAttributes({ style: value });
                                },
                                options: [
                                    { value: "", label: wplngI18nGutenberg.label.default },
                                    { value: "list", label: wplngI18nGutenberg.style.list },
                                    { value: "block", label: wplngI18nGutenberg.style.block },
                                    { value: "dropdown", label: wplngI18nGutenberg.style.dropdown }
                                ]
                            }
                        ),
                        // Title selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngI18nGutenberg.input.title,
                                value: props.attributes.title,
                                onChange: function (value) {
                                    props.setAttributes({ title: value });
                                },
                                options: [
                                    { value: "", label: wplngI18nGutenberg.label.default },
                                    { value: "original", label: wplngI18nGutenberg.title.original },
                                    { value: "name", label: wplngI18nGutenberg.title.name },
                                    { value: "id", label: wplngI18nGutenberg.title.id },
                                    { value: "none", label: wplngI18nGutenberg.title.none }
                                ]
                            }
                        ),
                        // Flags selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngI18nGutenberg.input.flags,
                                value: props.attributes.flags,
                                onChange: function (value) {
                                    props.setAttributes({ flags: value });
                                },
                                options: [
                                    { value: "", label: wplngI18nGutenberg.label.default },
                                    { value: "circle", label: wplngI18nGutenberg.flags.circle },
                                    { value: "rectangular", label: wplngI18nGutenberg.flags.rectangular },
                                    { value: "wave", label: wplngI18nGutenberg.flags.wave },
                                    { value: "none", label: wplngI18nGutenberg.flags.none },
                                ]
                            }
                        ),
                        // Theme selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngI18nGutenberg.input.theme,
                                value: props.attributes.theme,
                                onChange: function (value) {
                                    props.setAttributes({ theme: value });
                                },
                                options: [
                                    { value: "", label: wplngI18nGutenberg.label.default },
                                    { value: "light-double-smooth", label: wplngI18nGutenberg.theme["light-double-smooth"] },
                                    { value: "light-double-square", label: wplngI18nGutenberg.theme["light-double-square"] },
                                    { value: "light-simple-smooth", label: wplngI18nGutenberg.theme["light-simple-smooth"] },
                                    { value: "light-simple-square", label: wplngI18nGutenberg.theme["light-simple-square"] },
                                    { value: "grey-double-smooth", label: wplngI18nGutenberg.theme["grey-double-smooth"] },
                                    { value: "grey-double-square", label: wplngI18nGutenberg.theme["grey-double-square"] },
                                    { value: "grey-simple-smooth", label: wplngI18nGutenberg.theme["grey-simple-smooth"] },
                                    { value: "grey-simple-square", label: wplngI18nGutenberg.theme["grey-simple-square"] },
                                    { value: "dark-double-smooth", label: wplngI18nGutenberg.theme["dark-double-smooth"] },
                                    { value: "dark-double-square", label: wplngI18nGutenberg.theme["dark-double-square"] },
                                    { value: "dark-simple-smooth", label: wplngI18nGutenberg.theme["dark-simple-smooth"] },
                                    { value: "dark-simple-square", label: wplngI18nGutenberg.theme["dark-simple-square"] },
                                    { value: "blurblack-double-smooth", label: wplngI18nGutenberg.theme["blurblack-double-smooth"] },
                                    { value: "blurblack-double-square", label: wplngI18nGutenberg.theme["blurblack-double-square"] },
                                    { value: "blurblack-simple-smooth", label: wplngI18nGutenberg.theme["blurblack-simple-smooth"] },
                                    { value: "blurblack-simple-square", label: wplngI18nGutenberg.theme["blurblack-simple-square"] },
                                    { value: "blurwhite-double-smooth", label: wplngI18nGutenberg.theme["blurwhite-double-smooth"] },
                                    { value: "blurwhite-double-square", label: wplngI18nGutenberg.theme["blurwhite-double-square"] },
                                    { value: "blurwhite-simple-smooth", label: wplngI18nGutenberg.theme["blurwhite-simple-smooth"] },
                                    { value: "blurwhite-simple-square", label: wplngI18nGutenberg.theme["blurwhite-simple-square"] },
                                ]
                            }
                        ),
                    )
                )
            ];
        },
        // Save function for the block
        save: function () {
            return null; // Dynamic block, content saved on server
        }
    }
);

