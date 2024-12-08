// Registering a new block type for the WP block editor
wp.blocks.registerBlockType(
    "wplingua/languages-switcher", // Block name
    {
        title: wplngLocalize.label.title, // Block title
        description: wplngLocalize.label.description, // Block description
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
                                label: wplngLocalize.input.style,
                                value: props.attributes.style,
                                onChange: function (value) {
                                    props.setAttributes({ style: value });
                                },
                                options: [
                                    { value: "", label: wplngLocalize.label.default },
                                    { value: "list", label: wplngLocalize.style.list },
                                    { value: "block", label: wplngLocalize.style.block },
                                    { value: "dropdown", label: wplngLocalize.style.dropdown }
                                ]
                            }
                        ),
                        // Title selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.title,
                                value: props.attributes.title,
                                onChange: function (value) {
                                    props.setAttributes({ title: value });
                                },
                                options: [
                                    { value: "", label: wplngLocalize.label.default },
                                    { value: "original", label: wplngLocalize.title.original },
                                    { value: "name", label: wplngLocalize.title.name },
                                    { value: "id", label: wplngLocalize.title.id },
                                    { value: "none", label: wplngLocalize.title.none }
                                ]
                            }
                        ),
                        // Flags selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.flags,
                                value: props.attributes.flags,
                                onChange: function (value) {
                                    props.setAttributes({ flags: value });
                                },
                                options: [
                                    { value: "", label: wplngLocalize.label.default },
                                    { value: "circle", label: wplngLocalize.flags.circle },
                                    { value: "rectangular", label: wplngLocalize.flags.rectangular },
                                    { value: "wave", label: wplngLocalize.flags.wave },
                                    { value: "none", label: wplngLocalize.flags.none },
                                ]
                            }
                        ),
                        // Theme selection control
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: wplngLocalize.input.theme,
                                value: props.attributes.theme,
                                onChange: function (value) {
                                    props.setAttributes({ theme: value });
                                },
                                options: [
                                    { value: "", label: wplngLocalize.label.default },
                                    { value: "light-double-smooth", label: wplngLocalize.theme["light-double-smooth"] },
                                    { value: "light-double-square", label: wplngLocalize.theme["light-double-square"] },
                                    { value: "light-simple-smooth", label: wplngLocalize.theme["light-simple-smooth"] },
                                    { value: "light-simple-square", label: wplngLocalize.theme["light-simple-square"] },
                                    { value: "grey-double-smooth", label: wplngLocalize.theme["grey-double-smooth"] },
                                    { value: "grey-double-square", label: wplngLocalize.theme["grey-double-square"] },
                                    { value: "grey-simple-smooth", label: wplngLocalize.theme["grey-simple-smooth"] },
                                    { value: "grey-simple-square", label: wplngLocalize.theme["grey-simple-square"] },
                                    { value: "dark-double-smooth", label: wplngLocalize.theme["dark-double-smooth"] },
                                    { value: "dark-double-square", label: wplngLocalize.theme["dark-double-square"] },
                                    { value: "dark-simple-smooth", label: wplngLocalize.theme["dark-simple-smooth"] },
                                    { value: "dark-simple-square", label: wplngLocalize.theme["dark-simple-square"] },
                                    { value: "blurblack-double-smooth", label: wplngLocalize.theme["blurblack-double-smooth"] },
                                    { value: "blurblack-double-square", label: wplngLocalize.theme["blurblack-double-square"] },
                                    { value: "blurblack-simple-smooth", label: wplngLocalize.theme["blurblack-simple-smooth"] },
                                    { value: "blurblack-simple-square", label: wplngLocalize.theme["blurblack-simple-square"] },
                                    { value: "blurwhite-double-smooth", label: wplngLocalize.theme["blurwhite-double-smooth"] },
                                    { value: "blurwhite-double-square", label: wplngLocalize.theme["blurwhite-double-square"] },
                                    { value: "blurwhite-simple-smooth", label: wplngLocalize.theme["blurwhite-simple-smooth"] },
                                    { value: "blurwhite-simple-square", label: wplngLocalize.theme["blurwhite-simple-square"] },
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

