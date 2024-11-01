(function() {
    tinymce.PluginManager.add('standout_tc_button', function( editor, url ) {
        editor.addButton( 'standout_tc_button', {
            title: 'Stand Out!',
            type: 'menubutton',
            name: 'standout',
            icon: 'icon standout-editor-icon',
            menu: [
            	{
            		text: 'Highlighter',
            		onclick: function() {
                        editor.selection.setContent('[standout fx="highlighter"]' + editor.selection.getContent() + '[/standout]');
            		}
           		},
                {
                    text: 'Underline',
                    onclick: function() {
                        editor.selection.setContent('[standout fx="underline"]' + editor.selection.getContent() + '[/standout]');
                    }
                },
                {
                    text: '3D Text',
                    onclick: function() {
                        editor.selection.setContent('[standout fx="3dtext"]' + editor.selection.getContent() + '[/standout]');
                    },
                    menu: [
                        {
                            text: 'Elegant',
                            onclick: function() {
                                editor.stopPropagation();
                                editor.selection.setContent('[standout fx="3dtext" style="elegant"]' + editor.selection.getContent() + '[/standout]');
                            }
                        },
                        {
                            text: 'Superbold',
                            onclick: function() {
                                editor.stopPropagation();
                                editor.selection.setContent('[standout fx="3dtext" style="superbold"]' + editor.selection.getContent() + '[/standout]');
                            }
                        }
                    ]
                },
                {
                    text: 'Johnson Box',
                    onclick: function() {
                        editor.selection.setContent('[standoutbox fx="johnson"]' + editor.selection.getContent() + '[/standoutbox]');
                    }
                },
                {
                    text: 'Ribbon',
                    onclick: function() {
                        editor.selection.setContent('[standout fx="ribbon"]' + editor.selection.getContent() + '[/standout]');
                    }
                }
           ]
        });
    });
})();