/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

// The editor creator to use.
import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Code from '@ckeditor/ckeditor5-basic-styles/src/code';
import FontSize from '@ckeditor/ckeditor5-font/src/fontsize';
import FontFamily from '@ckeditor/ckeditor5-font/src/fontfamily';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight';

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import imageIcon from '@ckeditor/ckeditor5-core/theme/icons/image.svg';

class InsertImage extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add( 'insertImage', locale => {
            const view = new ButtonView( locale );

        view.set( {
            label: 'Insert image url',
            icon: imageIcon,
            tooltip: true
        } );

        // Callback executed once the image is clicked.
        view.on( 'execute', () => {
            const imageUrl = prompt( 'Image URL' );

        editor.model.change( writer => {
            const imageElement = writer.createElement( 'image', {
                src: imageUrl
            } );

        // Insert the image in the current selection location.
        editor.model.insertContent( imageElement, editor.model.document.selection );
    } );
    } );

        return view;
    } );
    }
}

export default class ClassicEditor extends ClassicEditorBase {}

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
    Essentials,
    UploadAdapter,
    Autoformat,
    Bold,
    Italic,
    BlockQuote,
    EasyImage,
    Heading,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Link,
    List,
    Paragraph,
    Code,
    FontSize,
    FontFamily,
    Alignment,
    Highlight,
    InsertImage,
];

// Editor configuration.
ClassicEditor.defaultConfig = {
    htmlEncodeOutput: true,
    toolbar: [
        'heading', '|','ImageUpload','bold', 'italic', 'highlight','fontSize', 'bulletedList', 'numberedList','alignment','|', 'undo','redo'],
    heading: {
        options: [
            {model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph'},
            {model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'},
            {model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'},
            {model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'},
        ]
    },
    highlight: {
        options:[
            { model: 'redPen', class: 'pen-red', title: 'Red pen', color: 'var(--ck-highlight-pen-red)', type: 'pen' },
            { model: 'greenPen', class: 'pen-green', title: 'Green pen', color: 'var(--ck-highlight-pen-green)', type: 'pen' },
            { model: 'rewards_bluePen', class: 'pen-rewards_blue', title: 'Rewards Blue pen', color: 'var(--ck-highlight-pen-rewards_blue)', type: 'pen' },
            { model: 'coralPen', class: 'pen-coral', title: 'Coral pen', color: 'var(--ck-highlight-pen-coral)', type: 'pen'},
            { model: 'sunshinePen', class: 'pen-sunshine', title: 'Sunshine pen', color: 'var(--ck-highlight-pen-sunshine)', type: 'pen' },
            { model: 'bright_magentaPen', class: 'pen-bright_magenta', title: 'Bright Magenta pen', color: 'var(--ck-highlight-pen-bright_magenta)', type: 'pen' },
            { model: 'gold_elitePen', class: 'pen-gold_elite', title: 'Gold Elite pen', color: 'var(--ck-highlight-pen-gold_elite)', type: 'pen' },
            { model: 'blackPen', class: 'pen-black', title: 'Black pen', color: 'var(--ck-highlight-pen-black)', type: 'pen' }
        ]
    },
    alignment: {
        options: [ 'left', 'center', 'right', 'justify']
    },
    image: {
        toolbar: ['imageStyle:full', 'imageStyle:side','imageStyle:alignLeft','imageStyle:alignCenter','imageStyle:alignRight' ],
        styles: [
            'side',
            'alignCenter',
            'full',
            'alignLeft',
            'alignRight'
        ]
    },
    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'zh-cn'
};