import { registerBlockType } from '@wordpress/blocks';
import { TextControl } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';

registerBlockType('my-plugin/product-rating', {
    edit({ attributes, setAttributes }) {
        const blockProps = useBlockProps();
        return (
            <div {...blockProps}>
                <TextControl
                    label="Title"
                    value={attributes.title}
                    onChange={(value) => setAttributes({ title: value })}
                />

            </div>
        );
    },
    save() {
        return null;
    }
});